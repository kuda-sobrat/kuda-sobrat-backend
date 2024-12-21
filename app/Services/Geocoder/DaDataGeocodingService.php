<?php

namespace App\Services\Geocoder;

use App\Contracts\Interfaces\GeocodingServiceInterface;
use App\Exceptions\GeocodingException;
use App\Support\Geocoder\GeocodingResult;
use App\Support\Geocoder\Point;
use App\Support\Geocoder\ReverseGeocodingResult;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class DaDataGeocodingService implements GeocodingServiceInterface
{
    private string $apiUrl = 'https://cleaner.dadata.ru/api/v1/clean';
    private string $suggestUrl = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs';
    private string $token;
    private string $secret;
    private Client $httpClient;

    public function __construct()
    {
        $this->token = env('DA_DATA_GEOCODING_TOKEN');
        $this->secret = env('DA_DATA_GEOCODING_SECRET');
        $this->httpClient = new Client([
            'headers' => [
                'Authorization' => 'Token ' . $this->token,
                'X-Secret' => $this->secret,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function geocode(string $address, array $options = []): array
    {
        // Используем метод /geocode/address для геокодирования
        // Однако DaData не предоставляет прямого метода геокодирования по адресу
        // Поэтому используем метод /suggest/address или /clean/address

        // Используем метод /clean/address для стандартизации и получения координат
        $url = $this->apiUrl . '/address';

        try {
            $response = $this->httpClient->post($url, [
                'body' => json_encode([$address]),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data[0]['qc']) && $data[0]['qc'] === '5') {
                // Код качества 5 означает, что адрес не распознан
                throw new GeocodingException('Address not recognized');
            }

            $result = new GeocodingResult();
            $result->coordinates = new Point((float)$data[0]['geo_lat'], (float)$data[0]['geo_lon']);
            $result->formattedAddress = $data[0]['result'];
            $result->components = $this->extractComponents($data[0]);
            $result->accuracy = $this->mapQualityCodeToAccuracy($data[0]['qc_geo']);

            return [$result];
        } catch (GuzzleException $e) {
            throw new GeocodingException('Geocoding request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function reverseGeocode(Point $coordinates, array $options = []): ReverseGeocodingResult
    {
        // TODO: Implement reverseGeocode() method.
    }

    public function validateAddress(string $address, array $options = []): bool
    {
        // TODO: Implement validateAddress() method.
    }

    public function batchGeocode(array $addresses, array $options = []): array
    {
        // TODO: Implement batchGeocode() method.
    }

    public function batchReverseGeocode(array $coordinates, array $options = []): array
    {
        // TODO: Implement batchReverseGeocode() method.
    }

    /**
     * Помощник для извлечения компонентов адреса.
     *
     * @param array $data
     * @return array
     */
    private function extractComponents(array $data): array
    {
        return [
            'country' => $data['country'],
            'region' => $data['region'],
            'region_type' => $data['region_type_full'],
            'city' => $data['city'],
            'city_district' => $data['city_district'],
            'settlement' => $data['settlement'],
            'street' => $data['street'],
            'house' => $data['house'],
            'block' => $data['block'],
            'flat' => $data['flat'],
            // Добавьте другие компоненты по необходимости
        ];
    }

    /**
     * Преобразование кода качества геокоординат в примерную точность (accuracy).
     *
     * @param string $qcGeo
     * @return float
     */
    private function mapQualityCodeToAccuracy(string $qcGeo): float
    {
        // qc_geo: Код точности координат
        // 0 — Точные координаты
        // 1 — Ближайший дом
        // 2 — Улица
        // 3 — Населенный пункт
        // 4 — Город
        // 5 — Координаты не определены

        switch ($qcGeo) {
            case '0':
                return 1.0; // Максимальная точность
            case '1':
                return 0.9;
            case '2':
                return 0.7;
            case '3':
                return 0.5;
            case '4':
                return 0.3;
            default:
                return 0.0; // Координаты не определены
        }
    }
}
