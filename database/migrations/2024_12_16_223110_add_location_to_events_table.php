<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Добавляем поле location типа POINT как nullable
        Schema::table('events', function (Blueprint $table) {
            $table->point('location')->nullable()->after('longitude');
        });

        // 2. Обновляем существующие записи, заполняя поле location на основе latitude и longitude
        // Обрабатываем записи с NULL значениями в latitude или longitude
        // Можно заменить NULL на 0 или другие дефолтные значения
        DB::statement('
            UPDATE `events`
            SET `latitude` = IFNULL(`latitude`, 0), `longitude` = IFNULL(`longitude`, 0)
        ');

        // Обновляем поле location
        DB::statement('
            UPDATE `events`
            SET `location` = ST_SRID(Point(`longitude`, `latitude`), 4326)
        ');

        // 3. Изменяем столбец location на NOT NULL
        Schema::table('events', function (Blueprint $table) {
            $table->point('location')->nullable(false)->change();
        });

        // 4. Создаем пространственный индекс на поле location
        Schema::table('events', function (Blueprint $table) {
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем пространственный индекс
        Schema::table('events', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']);
        });

        // Удаляем столбец location
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
