<?php

namespace App\Support\Geocoder;

class GeocodingResult
{
    public Point $coordinates;
    public string $formattedAddress;
    public array $components;
    public float $accuracy;
}
