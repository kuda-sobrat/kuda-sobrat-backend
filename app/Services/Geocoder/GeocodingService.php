<?php

namespace App\Services\Geocoder;

use App\Contracts\Interfaces\GeocodingServiceInterface;
use App\Providers\NominatimProvider;
use App\Support\Geocoder\Point;
use App\Support\Geocoder\ReverseGeocodingResult;

/**
 * Сервис для геокодирования
 *
 * TODO: Реализовать карусель (для замены сервиса в случае, если истек лимит или не работает)
 * TODO: Формат ответа (DTO)
 */
class GeocodingService extends DaDataGeocodingService implements GeocodingServiceInterface
{
}
