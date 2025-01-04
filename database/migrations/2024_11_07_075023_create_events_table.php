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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->comment('Название мероприятия');
            $table->string('slug')->index()->comment('Короткая ссылка мероприятия в системе');
            $table->text('description')->comment('Описание мероприятия');
            $table->dateTime('start_datetime')->index()->comment('Дата и время начала');
            $table->dateTime('end_datetime')->nullable()->comment('Дата и время окончания');
            $table->string('location')->comment('Место проведения');
            $table->string('unique_hash')->unique()->index()->comment('Хэш мероприятия');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
