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
        Schema::create('parameters', function (Blueprint $table) {
            $table->string('param_name', 50)->primary();
            $table->decimal('param_value', 10, 4);
        });

        // Заполняем таблицу начальными коэффициентами
        DB::table('parameters')->insert([
            ['param_name' => 'views_coefficient', 'param_value' => 1000],
            ['param_name' => 'shares_coefficient', 'param_value' => 100],
            ['param_name' => 'attendees_coefficient', 'param_value' => 10],
            ['param_name' => 'decay_coefficient', 'param_value' => 0.01], // Коэффициент затухания для часов
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
