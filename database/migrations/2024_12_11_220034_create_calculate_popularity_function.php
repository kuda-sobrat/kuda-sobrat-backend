<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Определяем функцию
        $sql = <<<SQL
CREATE FUNCTION calculate_popularity(
    p_event_id INT,
    p_views INT,
    p_shares INT,
    p_created DATETIME
)
RETURNS DECIMAL(10,4)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_attendees INT DEFAULT 0;
    DECLARE v_age_hours INT;
    DECLARE v_popularity DECIMAL(10,4);

    DECLARE v_views_coeff DECIMAL(10,4);
    DECLARE v_shares_coeff DECIMAL(10,4);
    DECLARE v_attendees_coeff DECIMAL(10,4);
    DECLARE v_decay_coeff DECIMAL(10,4);
    DECLARE v_max_age_hours DECIMAL(10,4);
    DECLARE v_age_factor DECIMAL(10,4);

    -- Получаем коэффициенты из таблицы параметров
    SELECT
        COALESCE(MAX(CASE WHEN param_name = 'views_coefficient' THEN param_value END), 1000),
        COALESCE(MAX(CASE WHEN param_name = 'shares_coefficient' THEN param_value END), 100),
        COALESCE(MAX(CASE WHEN param_name = 'attendees_coefficient' THEN param_value END), 10),
        COALESCE(MAX(CASE WHEN param_name = 'decay_coefficient' THEN param_value END), 0.01),
        COALESCE(MAX(CASE WHEN param_name = 'max_age_hours' THEN param_value END), 24)
    INTO
        v_views_coeff,
        v_shares_coeff,
        v_attendees_coeff,
        v_decay_coeff,
        v_max_age_hours
    FROM parameters;

    -- Обработка NULL значений параметров функции
    IF p_views IS NULL THEN SET p_views = 0; END IF;
    IF p_shares IS NULL THEN SET p_shares = 0; END IF;
    IF p_created IS NULL THEN SET p_created = NOW(); END IF;

    -- Получаем количество участников
    SELECT COUNT(*) INTO v_attendees FROM event_attendees WHERE event_id = p_event_id;

    -- Вычисляем возраст события в часах
    SET v_age_hours = TIMESTAMPDIFF(HOUR, p_created, NOW());

    -- Вычисляем фактор возраста
    IF v_age_hours <= v_max_age_hours THEN
        -- Усиливаем популярность для новых событий
        SET v_age_factor = 1 + ((v_max_age_hours - v_age_hours) / v_max_age_hours);
    ELSE
        -- Обычное затухание для старых событий
        SET v_age_factor = EXP(-v_decay_coeff * v_age_hours);
    END IF;

    -- Вычисляем популярность
    SET v_popularity = (
        (p_views / v_views_coeff) +
        (p_shares / v_shares_coeff) +
        (v_attendees / v_attendees_coeff)
    ) * v_age_factor;

    RETURN v_popularity;
END;
SQL;

        // Создаём функцию
        DB::unprepared('DROP FUNCTION IF EXISTS calculate_popularity;');
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем функцию при откате миграции
        DB::unprepared('DROP FUNCTION IF EXISTS calculate_popularity;');
    }
};
