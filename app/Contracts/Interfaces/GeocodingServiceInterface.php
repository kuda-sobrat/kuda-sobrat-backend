<?php

namespace App\Contracts\Interfaces;

use App\Exceptions\GeocodingException;
use App\Support\Geocoder\GeocodingResult;
use App\Support\Geocoder\ReverseGeocodingResult;
use App\Support\Geocoder\Point;

interface GeocodingServiceInterface
{
    /**
     * Прямое геокодирование: преобразование адреса или названия места в координаты.
     *
     * @param string $address Текстовый адрес или название места.
     * @param array $options Дополнительные параметры (например, язык, регион).
     * @return GeocodingResult[] Массив результатов геокодирования.
     *
     * @throws GeocodingException
     */
    public function geocode(string $address, array $options = []): array;

    /**
     * Обратное геокодирование: преобразование координат в адрес или описание места.
     *
     * @param Point $coordinates Координаты
     * @param array $options Дополнительные параметры (например, язык, уровень детализации).
     * @return ReverseGeocodingResult Результат обратного геокодирования.
     *
     * @throws GeocodingException
     */
    public function reverseGeocode(Point $coordinates, array $options = []): ReverseGeocodingResult;

    /**
     * Валидация адреса: проверка корректности и существования адреса.
     *
     * @param string $address Текстовый адрес.
     * @param array $options Дополнительные параметры.
     * @return bool Возвращает true, если адрес валиден.
     *
     * @throws GeocodingException
     */
    public function validateAddress(string $address, array $options = []): bool;

    /**
     * Пакетное геокодирование: обработка нескольких адресов одновременно.
     *
     * @param string[] $addresses Массив адресов.
     * @param array $options Дополнительные параметры.
     * @return GeocodingResult[][] Массив массивов результатов для каждого адреса.
     *
     * @throws GeocodingException
     */
    public function batchGeocode(array $addresses, array $options = []): array;

    /**
     * Пакетное обратное геокодирование: обработка нескольких наборов координат одновременно.
     *
     * @param array<Point|array> $coordinates Массив координат (['latitude' => float, 'longitude' => float]).
     * @param array $options Дополнительные параметры.
     * @return ReverseGeocodingResult[] Массив результатов обратного геокодирования.
     *
     * @throws GeocodingException
     */
    public function batchReverseGeocode(array $coordinates, array $options = []): array;
}
