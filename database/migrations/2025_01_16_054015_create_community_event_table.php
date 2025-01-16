<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('community_event', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_id')->index()->comment('Внешний ключ на communities');
            $table->unsignedBigInteger('event_id')->index()->comment('Внешний ключ на events');

            // Добавляем внешние ключи и индексы
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            // Предотвращаем дубликаты связей
            $table->unique(['community_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_event');
    }
};
