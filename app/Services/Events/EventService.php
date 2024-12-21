<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Support\Geocoder\Point;
use Illuminate\Database\Eloquent\Builder;

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
        $weight_popularity = $options['weight_popularity'] ?? config('event.weight_popularity');
        $weight_distance = $options['weight_distance'] ?? config('event.weight_distance');

        $max_distance = $radius; // Максимальное расстояние — радиус поиска

        // Получаем максимальное значение popularity_score для нормализации
        $max_popularity = Event::max('popularity_score') ?? 1;

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
            $weight_popularity,     // Для weight_popularity
            $max_popularity,        // Для max_popularity
            $weight_distance,       // Для weight_distance
            $wkt,                   // Для ST_GeomFromText в формуле ranking_score
            $max_distance,          // Для max_distance
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
}
