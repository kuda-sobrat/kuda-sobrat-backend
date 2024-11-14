<?php

namespace App\Services;

class FormatterService
{
    /**
     * Форматирует данные в JSON-строку.
     *
     * @param  mixed  $data
     * @return Object|array|null
     */
    public function formatToJson(string $data): Object|array|null
    {
        $json = json_decode($data);
        if ($data != null && empty($tmp)) {
            $pattern = '/```json\s*(\{.*?\})\s*```/s';
            $patternB = '/```json\s*(\[.*?\])\s*```/s';
            if (preg_match($pattern, $data, $matches) || preg_match($patternB, $data, $matches)) {
                $json = json_decode($matches[1]);
            }
        }
        return $json;
    }
}
