<?php

namespace App\Support\Geocoder;

class Point
{
    public function __construct(
        public $latitude,
        public $longitude,
    ) {
    }
}
