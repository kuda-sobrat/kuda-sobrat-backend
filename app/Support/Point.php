<?php

namespace App\Support;

class Point
{
    public function __construct(
        public $latitude,
        public $longitude,
    ) {
    }

    public function isEmpty(): bool
    {
        return empty($this->latitude) && empty($this->longitude);
    }
}
