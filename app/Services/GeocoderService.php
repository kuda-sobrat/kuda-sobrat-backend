<?php

namespace App\Services;

use App\Providers\NominatimProvider;

/**
 * Сервис для геокодирования
 *
 * TODO: Реализовать карусель (для замены сервиса в случае, если истек лимит или не работает)
 * TODO: Формат ответа (DTO)
 */
class GeocoderService
{
    /**
     * Получает координаты по адресу
     *
     * @param $address
     * @return array
     * @throws \Spatie\Geocoder\Exceptions\CouldNotGeocode
     */
    public function geocode($address) {
        $geocoder = new NominatimProvider();
        return $geocoder->getAllCoordinatesForAddress($address);
    }
}
