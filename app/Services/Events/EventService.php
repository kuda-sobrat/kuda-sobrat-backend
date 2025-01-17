<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Models\Interest;
use App\Support\Geocoder\Point;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для работы с мероприятиями (событиями)
 */
class EventService
{
    /**
     * Получить запрос событий с учетом геолокации и весовых коэффициентов.
     *
     * @param Point|array $coordinates Координаты ['latitude' =>, 'longitude' => ]
     * @param array $options Опции (radius, weight_popularity, weight_distance)
     * @return Builder Запрос событий
     */
    public function getEventsByLocation(mixed $coordinates, array $options = []): Builder
    {
        if (is_array($coordinates)) {
            $coordinates = new Point($coordinates[0], $coordinates[1]);
        }

        // Получаем параметры из опций или из конфига по умолчанию
        $radius = $options['radius'] ?? config('event.default_radius');
        $weightPopularity = $options['weight_popularity'] ?? config('event.weight_popularity');
        $weightDistance = $options['weight_distance'] ?? config('event.weight_distance');

        $maxDistance = $radius; // Максимальное расстояние — радиус поиска

        // Получаем максимальное значение popularity_score для нормализации
        $maxPopularity = Event::max('popularity_score') ?? 1;

        $wkt = 'POINT(' . $coordinates->longitude . ' ' . $coordinates->latitude . ')';

        // Формируем запрос
        $eventsQuery = Event::selectRaw("
                events.*,
                ST_Distance(
                    ST_Transform(events.location, 3857),
                    ST_Transform(ST_GeomFromText(?, 4326), 3857)
                ) AS distance,
                (
                    (? * (events.popularity_score / ?)) +
                    (? * (1 - (ST_Distance(
                        ST_Transform(events.location, 3857),
                        ST_Transform(ST_GeomFromText(?, 4326), 3857)
                    ) / ?)))
                ) AS ranking_score
            ", [
            $wkt,                   // Для ST_GeomFromText в SELECT
            $weightPopularity,     // Для weight_popularity
            $maxPopularity,        // Для max_popularity
            $weightDistance,       // Для weight_distance
            $wkt,                   // Для ST_GeomFromText в формуле ranking_score
            $maxDistance,          // Для max_distance
        ])
            ->whereRaw("
                ST_Distance(
                    ST_Transform(events.location, 3857),
                    ST_Transform(ST_GeomFromText(?, 4326), 3857)
                ) <= ?
            ", [
                $wkt,       // Для ST_GeomFromText в WHERE
                $radius,    // Максимальное расстояние
            ])
            ->where('is_archived', false)
            ->orderByDesc('ranking_score');

        return $eventsQuery;
    }

    /**
     * Получить запрос событий с учетом интересов и их иерархии.
     *
     * @param array $interestIds Список ID интересов
     * @param array $options Опции (weight_popularity, weight_interest_match)
     * @return Builder Запрос событий
     */
    public function getEventsByInterests(array $interestIds, array $options = []): Builder
    {
        // Получаем параметры из опций или из конфига по умолчанию
        $weightPopularity = $options['weight_popularity'] ?? config('event.weight_popularity', 0.5);

        // Получаем полный список интересов (включая родителей и дочерние)
        $allInterestIds = $this->getAllRelatedInterestIds($interestIds);

        // Если массив интересов пустой, устанавливаем значение по умолчанию
        if (empty($allInterestIds)) {
            $allInterestIds = []; // Или можете настроить поведение без интересов
        }

        // Получаем максимальное значение popularity_score для нормализации
        $maxPopularity = Event::max('popularity_score') ?? 1;

        // Подзапрос для расчёта количества совпавших интересов
        $eventInterestMatchSubquery = DB::table('event_interest')
            ->select(
                'event_interest.event_id',
                DB::raw("COUNT(DISTINCT event_interest.interest_id) AS matched_interests")
            )
            ->whereIn('event_interest.interest_id', $allInterestIds)
            ->groupBy('event_interest.event_id');

        // Базовый запрос для мероприятий
        $baseQuery = Event::select('events.*')
            ->leftJoinSub(
                $eventInterestMatchSubquery,
                'eim',
                'eim.event_id',
                '=',
                'events.id'
            )
            ->addSelect(DB::raw("
            COALESCE(eim.matched_interests, 0) AS matched_interests,
            CASE WHEN eim.matched_interests IS NOT NULL THEN 1 ELSE 0 END AS is_interest_matched
        "))
            ->where('events.is_archived', false)
            ->whereNull('events.deleted_at')
            ->where('events.start_datetime', '>=', now()); // Учитываем только будущие мероприятия

        // Подзапрос для выбора одного мероприятия из каждой группы
        $bestEventsSubquery = DB::table('events as e')
            ->select('e.id')
            ->joinSub($baseQuery, 'base', 'base.id', '=', 'e.id')
            ->where(function($query) {
                $query->whereRaw("
                e.id = (
                    SELECT e_inner.id FROM events AS e_inner
                    WHERE e_inner.event_group_id = e.event_group_id
                    AND e_inner.start_datetime >= NOW()
                    ORDER BY e_inner.start_datetime ASC
                    LIMIT 1
                )
            ")
                    ->orWhereNull('e.event_group_id'); // Учитываем мероприятия без группы
            });

        // Формируем запрос, используя мероприятия из подзапроса
        $eventsQuery = Event::fromSub($bestEventsSubquery, 'best_events')
            ->join('events', 'events.id', '=', 'best_events.id')
            ->leftJoinSub(
                $eventInterestMatchSubquery,
                'eim',
                'eim.event_id',
                '=',
                'events.id'
            )
            ->select('events.*')
            ->selectRaw("
            COALESCE(eim.matched_interests, 0) AS matched_interests,
            CASE WHEN eim.matched_interests IS NOT NULL THEN 1 ELSE 0 END AS is_interest_matched,
            ({$weightPopularity} * (events.popularity_score / {$maxPopularity})) AS popularity_score_normalized
        ")
            // Сортировка с приоритетом совпадения интересов
            ->orderByDesc('is_interest_matched')       // Сначала мероприятия с совпадением интересов
            ->orderByDesc('matched_interests')         // Затем по количеству совпавших интересов
            ->orderByDesc('popularity_score_normalized') // Затем по популярности
            ->orderBy('events.start_datetime');        // Затем по дате начала

        return $eventsQuery;
    }

    /**
     * Получить запрос событий с учетом геолокации, интересов и их иерархии.
     *
     * @param Point|array $coordinates Координаты ['latitude', 'longitude'] или экземпляр Point
     * @param array $interestIds Список ID интересов
     * @param array $options Опции (radius, weight_popularity, weight_distance, weight_interest_match)
     * @return Builder Запрос событий
     */
    public function getEventsByLocationAndInterests(mixed $coordinates, array $interestIds, array $options = []): Builder
    {
        // Преобразуем координаты в объект Point, если они переданы в виде массива
        if (is_array($coordinates)) {
            $coordinates = new Point($coordinates[0], $coordinates[1]);
        }

        // Преобразуем точку в WKT формат для использования в SQL
        $wktPoint = "POINT({$coordinates->longitude} {$coordinates->latitude})";

        // Извлекаем опции или устанавливаем значения по умолчанию
        $radius = $options['default_radius'] ?? config('event.default_radius', 0.3); // в метрах

        // Получаем все интересы, включая их дочерние (учет иерархии)
        $allInterestIds = $this->getAllRelatedInterestIds($interestIds);

        // Подзапрос для расчёта количества совпавших интересов и флага наличия совпадения
        $eventInterestMatchSubquery = DB::table('event_interest')
            ->select(
                'event_interest.event_id',
                DB::raw("COUNT(DISTINCT event_interest.interest_id) AS matched_interests"),
                DB::raw("1 AS has_interest_match")
            )
            ->whereIn('event_interest.interest_id', $allInterestIds)
            ->groupBy('event_interest.event_id');

        // Базовый запрос (без глобальных скоупов)
        $baseQuery = Event::withoutGlobalScopes()
            ->select('events.*')
            ->selectRaw("
            ST_Distance_Sphere(
                events.location,
                ST_GeomFromText(?, 4326)
            ) AS distance,
            COALESCE(eim.matched_interests, 0) AS matched_interests,
            COALESCE(eim.has_interest_match, 0) AS has_interest_match
        ", [$wktPoint])
            ->leftJoinSub(
                $eventInterestMatchSubquery,
                'eim',
                'eim.event_id',
                '=',
                'events.id'
            )
            ->whereRaw("
            ST_Distance_Sphere(
                events.location,
                ST_GeomFromText(?, 4326)
            ) <= ?
        ", [$wktPoint, $radius])
            ->where('events.is_archived', false)
            ->whereNull('events.deleted_at') // Явно применяем условие для soft delete
            ->where('events.start_datetime', '>=', now()) // Учитываем только будущие мероприятия
            ->groupBy('events.id');

        // Подзапрос для выбора одного мероприятия из каждой группы
        $bestEventsSubquery = DB::table('events as e')
            ->select('e.id')
            ->joinSub($baseQuery, 'base', 'base.id', '=', 'e.id')
            ->whereRaw("
            e.id = (
                SELECT e_inner.id FROM events AS e_inner
                WHERE e_inner.event_group_id = e.event_group_id
                AND e_inner.start_datetime >= NOW()
                ORDER BY e_inner.start_datetime ASC
                LIMIT 1
            )
        ");

        // Объединяем с подзапросом и применяем сортировку
        $finalQuery = Event::withoutGlobalScopes()
            ->fromSub($bestEventsSubquery, 'best_events')
            ->join('events', 'events.id', '=', 'best_events.id')
            ->leftJoinSub(
                $eventInterestMatchSubquery,
                'eim',
                'eim.event_id',
                '=',
                'events.id'
            )
            ->select('events.*')
            ->selectRaw("
            ST_Distance_Sphere(
                events.location,
                ST_GeomFromText(?, 4326)
            ) AS distance,
            COALESCE(eim.matched_interests, 0) AS matched_interests,
            COALESCE(eim.has_interest_match, 0) AS has_interest_match
        ", [$wktPoint])
            ->orderByDesc('has_interest_match')       // Сначала мероприятия с совпадением интересов
            ->orderByDesc('matched_interests')        // Затем по количеству совпавших интересов
            ->orderBy('distance')                     // Затем по расстоянию
            ->orderByDesc('events.popularity_score'); // Затем по популярности

        return $finalQuery;
    }

    /**
     * @param string $query
     * @param int $limit
     * @return Collection<Event>
     */
    public function getEventSuggestions(string $query, int $limit = 10): Collection
    {
        $suggestions = Event::select('name', DB::raw('MAX(start_datetime) as latest_start_datetime'))
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('location_name', 'LIKE', "%{$query}%");
            })
            ->where('is_archived', false)
            ->whereNull('deleted_at')
            ->groupBy('name')
            ->orderBy('latest_start_datetime', 'DESC') // Сортируем по максимальной дате создания
            ->limit($limit)
            ->get();

        return $suggestions;
    }

    /**
     * Метод поиска по мероприятиям
     *
     * @param string $query
     * @param array $filters
     * @param array $options
     * @return Builder
     */
    public function searchEvents(string $query, array $filters = [], array $options = []): Builder
    {
        // Извлекаем опции или устанавливаем значения по умолчанию
        $weightPopularity = $options['weight_popularity'] ?? config('event.weight_popularity', 0.5);
        $weightRelevance = $options['weight_relevance'] ?? config('event.weight_relevance', 0.5);

        // Получаем максимальное значение popularity_score для нормализации
        $maxPopularity = Event::max('popularity_score') ?? 1;

        // Базовый запрос
        $baseQuery = Event::select('events.*')
            ->where('events.is_archived', false)
            ->whereNull('events.deleted_at')
//            ->where('events.start_datetime', '>=', now())

            // Поиск по запросу
            ->where(function ($q) use ($query) {
                $q->where('events.name', 'LIKE', "%{$query}%")
                    ->orWhere('events.description', 'LIKE', "%{$query}%")
                    ->orWhere('events.location_name', 'LIKE', "%{$query}%")
                    ->orWhere('events.formatted_address', 'LIKE', "%{$query}%");
            });

        // Применение фильтров

        // 1. Фильтр по интересам
        if (!empty($filters['interests'])) {
            $interestIds = $filters['interests'];

            // Получаем все связанные интересы, включая их дочерние
            $allInterestIds = $this->getAllRelatedInterestIds($interestIds);

            $baseQuery->whereExists(function ($query) use ($allInterestIds) {
                $query->select(DB::raw(1))
                    ->from('event_interest')
                    ->whereColumn('event_interest.event_id', 'events.id')
                    ->whereIn('event_interest.interest_id', $allInterestIds);
            });
        }

        // 2. Фильтр по геолокации
        if (!empty($filters['location']) && !empty($filters['radius'])) {
            $location = $filters['location']; // ['latitude' => ..., 'longitude' => ...]
            $radius = $filters['radius']; // В метрах

            $wktPoint = "POINT({$location['longitude']} {$location['latitude']})";

            $baseQuery->whereRaw("
            ST_Distance_Sphere(
                events.location,
                ST_GeomFromText(?, 4326)
            ) <= ?
        ", [$wktPoint, $radius]);
        }

        // 3. Фильтр по времени
        if (!empty($filters['timeRange'])) {
            $timeRange = $filters['timeRange']; // ['start' => ..., 'end' => ...]
            $start = $timeRange['start'];
            $end = $timeRange['end'];

            $baseQuery->whereBetween('events.start_datetime', [$start, $end]);
        }

        // Подзапрос для выбора одного мероприятия из каждой группы
        $bestEventsSubquery = DB::table('events as e')
            ->select('e.id')
            ->joinSub($baseQuery, 'base', 'base.id', '=', 'e.id')
            ->where(function($query) {
                $query->whereRaw("
                e.id = (
                    SELECT e_inner.id FROM events AS e_inner
                    WHERE e_inner.event_group_id = e.event_group_id
                    AND e_inner.start_datetime >= NOW()
                    ORDER BY e_inner.start_datetime ASC
                    LIMIT 1
                )
            ")
                    ->orWhereNull('e.event_group_id'); // Учитываем мероприятия без группы
            });

        // Формируем окончательный запрос с расчетом рейтинга
        $eventsQuery = Event::fromSub($bestEventsSubquery, 'best_events')
            ->join('events', 'events.id', '=', 'best_events.id')
            ->select('events.*')
            ->selectRaw("
            (
                ({$weightPopularity} * (events.popularity_score / {$maxPopularity})) +
                ({$weightRelevance} * (
                    (CASE WHEN events.name LIKE ? THEN 1 ELSE 0 END) +
                    (CASE WHEN events.description LIKE ? THEN 0.5 ELSE 0 END)
                ))
            ) AS ranking_score
        ", ["%{$query}%", "%{$query}%"])
            ->orderByDesc('ranking_score')
            ->orderBy('events.start_datetime');

        return $eventsQuery;
    }

    /**
     * Возвращает порцию мероприятий согласно переданному курсору
     *
     * @param Point|array $coordinates Координаты ['latitude', 'longitude'] или экземпляр Point
     * @param array $interestIds Список ID интересов
     * @param array $parameters Дополнительные параметры
     * @return LengthAwarePaginator
     */
    public function getEventsFeed(mixed $coordinates, array $interestIds, array $parameters, array $filters = []): LengthAwarePaginator
    {
        $cursor = $parameters['cursor'] ?? null;
        $perPage = $parameters['per_page'] ?? 15;
        $isActual = $parameters['is_actual'] ?? true;

        // Получаем базовый запрос
        if (!empty($coordinates->latitude) || !empty($coordinates->longitude)) {
            $eventsQuery = $this->getEventsByLocationAndInterests($coordinates, $interestIds);
        } else {
            $eventsQuery = $this->getEventsByInterests($interestIds);
        }

        // Применяем фильтр по времени (актуальности)
        if ($isActual) {
            $eventsQuery
                ->where('start_datetime', '>=', Carbon::now())
                ->orWhere('end_datetime', '>=', Carbon::now());
        }

        // Применяем дополнительные фильтры

        // Фильтрация по радиусу и местоположению
        if (!empty($filters['radius']) && !empty($filters['location']['latitude']) && !empty($filters['location']['longitude'])) {
            $latitude = $filters['location']['latitude'];
            $longitude = $filters['location']['longitude'];
            $radius = $filters['radius'];

            $eventsQuery->whereRaw("
            ST_Distance_Sphere(
                point(longitude, latitude),
                point(?, ?)
            ) <= ?
        ", [$longitude, $latitude, $radius]);
        }

        // Фильтрация по временным диапазонам
        if (!empty($filters['timeRange']['start']) && !empty($filters['timeRange']['end'])) {
            $startTime = $filters['timeRange']['start'];
            $endTime = $filters['timeRange']['end'];

            $eventsQuery->whereBetween('start_datetime', [$startTime, $endTime]);
        }

        try {
            // Загружаем связанные данные
            $eventsQuery->with('attachments');
            $eventsQuery->with('eventGroup');

            // Пагинация результатов
            $events = $eventsQuery->paginate($perPage, ['*'], 'page', $cursor);
        } catch (\Exception $e) {
            // Логируем ошибку
            Log::error('Ошибка при получении ленты мероприятий: ' . $e->getMessage());
            throw $e;
        }

        return $events;
    }



    /**
     * Получить все связанные ID интересов (родители и дочерние)
     *
     * @param array $interestIds
     * @return array
     */
    protected function getAllRelatedInterestIds(array $interestIds): array
    {
        $allInterestIds = collect();

        foreach ($interestIds as $interestId) {
            $interest = Interest::find($interestId);
            if ($interest) {
                $unfoldedInterests = $interest->getUnfoldedInterests();
                $allInterestIds = $allInterestIds->merge($unfoldedInterests->pluck('id'));
            }
        }

        return $allInterestIds->unique()->values()->all();
    }

    /**
     * Подзапрос для расчета коэффициента совпадения интересов мероприятия
     *
     * @param array $interestIds
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getEventInterestMatchSubquery(array $interestIds)
    {
        // Количество интересов пользователя
        $userInterestsCount = count($interestIds) ?: 1; // предотвращаем деление на ноль

        // Подсчитываем количество совпадающих интересов для каждого события
        return DB::table('event_interest')
            ->select('event_interest.event_id', DB::raw("COUNT(*) / $userInterestsCount as match_score"))
            ->whereIn('event_interest.interest_id', $interestIds)
            ->groupBy('event_interest.event_id');
    }
}
