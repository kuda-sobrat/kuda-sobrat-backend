<?php

namespace App\Contracts\Interfaces;

use App\Models\ContextPost;

interface ContextServiceInterface
{
    /**
     * Метод обработки
     *
     * @param int $contextId
     * @param string|null $contextClass
     * @return mixed
     */
    public function processContext(int $contextId, ?string $contextClass = null): void;
}
