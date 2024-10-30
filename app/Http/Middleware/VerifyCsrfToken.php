<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URI, которые должны быть исключены из CSRF-защиты.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
    ];
}
