<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Models\Interest;
use App\Support\Geocoder\Point;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
        $weightInterestMatch = $options['weight_interest_match'] ?? config('event.weight_interest_match', 0.5);

        // Получаем развернутый список интересов (включая родителей и дочерние)
        $allInterestIds = $this->getAllRelatedInterestIds($interestIds);

        // Получаем максимальное значение popularity_score для нормализации
        $maxPopularity = Event::max('popularity_score') ?? 1;

        // Формируем запрос
        $eventsQuery = Event::selectRaw("
            events.*,
            CASE WHEN ei.event_id IS NOT NULL THEN 1 ELSE 0 END as is_interest_matched,
            (
                (? * (events.popularity_score / ?)) +
                (? * CASE WHEN ei.event_id IS NOT NULL THEN 1 ELSE 0 END)
            ) AS ranking_score
        ", [
            $weightPopularity,           // Вес популярности
            $maxPopularity,              // Максимальная популярность
            $weightInterestMatch        // Вес совпадения интересов
        ])
            ->leftJoin('event_interest as ei', function ($join) use ($allInterestIds) {
                $join->on('events.id', '=', 'ei.event_id')
                    ->whereIn('ei.interest_id', $allInterestIds);
            })
            ->where('events.is_archived', false)
            ->where(function ($query) {
                $query->whereNull('events.deleted_at')->orWhere('events.deleted_at', '>', now());
            })
            ->groupBy('events.id')
            ->orderByDesc('ranking_score');

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
        $weightPopularity = $options['weight_popularity'] ?? config('event.weight_popularity', 0.3);
        $weightDistance = $options['weight_distance'] ?? config('event.weight_distance', 0.3);
        $weightInterestMatch = $options['weight_interest_match'] ?? config('event.weight_interest_match', 10);

        // Максимальные значения для нормализации
        $maxPopularity = $options['max_popularity'] ?? config('event.max_popularity', 100); // Максимальная популярность
        $maxDistance = $options['max_distance'] ?? config('event.max_distance', $radius); // Максимальное расстояние

        // Получаем все интересы, включая их дочерние (учет иерархии)
        $allInterestIds = $this->getAllRelatedInterestIds($interestIds);

        // Получаем количество интересов пользователя для нормализации match_score
        $userInterestsCount = count($allInterestIds);

        // Если нет интересов, устанавливаем значение по умолчанию
        if ($userInterestsCount === 0) {
            $userInterestsCount = 1;
        }

        // Подзапрос для расчёта match_score и флага has_interest_match
        $eventInterestMatchSubquery = DB::table('event_interest')
            ->select(
                'event_interest.event_id',
                DB::raw("COUNT(DISTINCT event_interest.interest_id) / {$userInterestsCount} as match_score"),
                DB::raw("1 as has_interest_match") // Флаг, указывающий на наличие совпадения интересов
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
            COALESCE(eim.match_score, 0) AS match_score,
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
            ->groupBy('events.id');

        // Предыдущий внешний запрос становится базовым запросом
        $baseQueryWithRanking = Event::withoutGlobalScopes()
            ->fromSub($baseQuery, 'base')
            ->select('base.*')
            ->selectRaw("
            (
                ({$weightPopularity} * (base.popularity_score / {$maxPopularity})) +
                ({$weightDistance} * (1 - (base.distance / {$maxDistance}))) +
                ({$weightInterestMatch} * base.match_score)
            ) AS ranking_score
        ");

        $finalQuery = Event::withoutGlobalScopes()
            ->fromSub($baseQueryWithRanking, 'ranked')
            ->select('ranked.*')
            ->orderByDesc('ranking_score');

        return $finalQuery;
    }

    /**
     * Возвращает порцию мероприятий согласно переданному курсору
     *
     * @param Point|array $coordinates Координаты ['latitude', 'longitude'] или экземпляр Point
     * @param array $interestIds Список ID интересов
     * @param array $parameters Дополнительные параметры
     * @return Paginator
     */
    public function getEventsFeed(mixed $coordinates, array $interestIds, array $parameters): Paginator
    {
        $cursor = $parameters['cursor'] ?? null;
        $perPage = $parameters['per_page'] ?? 15;

        // Получаем базовый запрос
        $eventsQuery = $this->getEventsByLocationAndInterests($coordinates, $interestIds);

        try {
            $events = $eventsQuery->simplePaginate($perPage, page: $cursor);
        } catch (\Exception $e) {
            dd($e->getMessage());
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
