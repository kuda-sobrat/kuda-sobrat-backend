<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Spatie\Geocoder\Exceptions\CouldNotGeocode;
use Spatie\Geocoder\Geocoder;

// TODO Вынести в отдельный сервис
class NominatimProvider extends Geocoder
{
    public function __construct()
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => config('app.name') . ' (' . config('app.url') . ')'
            ]
        ]);
        $this->endpoint = 'https://nominatim.openstreetmap.org/search';
        parent::__construct($client);
    }

    protected function getRequestPayload(array $parameters): array
    {
        $parameters = array_merge([
            'format' => 'jsonv2',
            'addressdetails' => 1,
            'limit' => 1,
            'zoom' => 18
        ], $parameters);

        return ['query' => $parameters];
    }

    public function getAllCoordinatesForAddress(string $address): array
    {
        if (empty($address)) {
            return $this->emptyResponse();
        }

        $payload = $this->getRequestPayload(['q' => $address]);
        $response = $this->client->request('GET', $this->endpoint, $payload);

        if ($response->getStatusCode() !== 200) {
            throw CouldNotGeocode::couldNotConnect();
        }

        $geocodingResponse = json_decode($response->getBody());

        if (! empty($geocodingResponse->error_message)) {
            throw CouldNotGeocode::serviceReturnedError($geocodingResponse->error_message);
        }

        if (! count($geocodingResponse)) {
            return $this->emptyResponse();
        }

        return $this->formatResponse($geocodingResponse);
    }

    public function getAllAddressesForCoordinates(float $lat, float $lng): array
    {
        $payload = $this->getRequestPayload([
            'lat' => "$lat",
            'lon' => "$lng",
        ]);

        $response = $this->client->request('GET', $this->endpoint, $payload);

        if ($response->getStatusCode() !== 200) {
            throw CouldNotGeocode::couldNotConnect();
        }

        $reverseGeocodingResponse = json_decode($response->getBody());

        if (! empty($reverseGeocodingResponse->error_message)) {
            throw CouldNotGeocode::serviceReturnedError($reverseGeocodingResponse->error_message);
        }

        if (! count($reverseGeocodingResponse->results)) {
            return $this->emptyResponse();
        }

        return $this->formatResponse($reverseGeocodingResponse);
    }

    // TODO: форматирование ответа
    protected function formatResponse($response): array
    {
        $locations = array_map(function ($result) {
            return [
                'lat' => $result->lat,
                'lng' => $result->lon,
                'formatted_address' => $result->display_name,
                'viewport' => $result->boundingbox,
                'address_components' => $result->address,
                'place_id' => $result->place_id,
            ];
        }, $response);

        return $locations;
    }
}
