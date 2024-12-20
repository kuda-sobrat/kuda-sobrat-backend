<?php

namespace App\Casts;

use App\Support\Point;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class PointCast implements CastsAttributes
{
    protected Point $point;
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value) {
            return null;
        }

        // $value - бинарные данные из базы данных
        $bytes = $value;

        // 2. Читаем порядок байтов (1 байт)
        $byteOrder = ord($bytes[4]);

        // 3. Устанавливаем текущую позицию
        $position = 5;

        // 4. Читаем тип геометрии (4 байта)
        if ($byteOrder === 0) { // big endian
            $geometryType = unpack('N', substr($bytes, $position, 4))[1];
        } else { // little endian
            $geometryType = unpack('V', substr($bytes, $position, 4))[1];
        }
        $position += 4;

        // Проверяем, что это POINT
        if ($geometryType !== 1) {
            throw new \Exception('Неожиданный тип геометрии: ' . $geometryType);
        }

        // 5. Читаем координаты X и Y (по 8 байт каждая)
        if ($byteOrder === 0) { // big endian
            // Чтение в формате big endian
            $x = unpack('d', strrev(substr($bytes, $position, 8)))[1];
            $position += 8;
            $y = unpack('d', strrev(substr($bytes, $position, 8)))[1];
        } else { // little endian
            // Чтение в формате little endian
            $x = unpack('d', substr($bytes, $position, 8))[1];
            $position += 8;
            $y = unpack('d', substr($bytes, $position, 8))[1];
        }

        $this->point = new Point($y, $x);
        return $this->point;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof Point && isset($value->latitude) && isset($value->longitude)) {
            $latitude = (float) $value->latitude;
            $longitude = (float) $value->longitude;


            // Проверяем валидность координат
            if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
                throw new \InvalidArgumentException('Недопустимые значения широты или долготы.');
            }

            // Создаем WKB бинарную строку с SRID
            // Формат WKB:
            // - 4 байта: SRID (беззнаковое целое, little-endian)
            // - 1 байт: порядок байтов (1 для little-endian)
            // - 4 байта: тип геометрии (1 для POINT, беззнаковое целое)
            // - 8 байт: координата X (double)
            // - 8 байт: координата Y (double)

            $srid = 4326;
            $wkb = pack('V', $srid); // SRID, беззнаковое целое, little-endian
            $wkb .= pack('C', 1);    // Порядок байтов, 1 для little-endian
            $wkb .= pack('V', 1);    // Тип геометрии, POINT = 1
            $wkb .= pack('dd', $longitude, $latitude); // Координаты X и Y (double)

            return $wkb;
        }

        return null;
    }
}
