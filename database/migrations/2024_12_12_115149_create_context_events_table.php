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
        Schema::create('context_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_id')->nullable()->index()->comment('ID сообщества');
            $table->unsignedBigInteger('event_id')->nullable()->index()->comment('ID мероприятия');
            $table->unsignedBigInteger('context_id')->index()->comment('ID context_posts');
            $table->enum('status', ['created', 'pending', 'completed', 'failed'])->default('created')->index()->comment('Статус обработки');
            $table->string('name')->comment('Название мероприятия');
            $table->text('description')->comment('Описание мероприятия');
            $table->dateTime('start_datetime')->nullable()->comment('Дата и время начала');
            $table->dateTime('end_datetime')->nullable()->comment('Дата и время завершения');
            $table->string('location')->comment('Локация (текст)');
            $table->timestamps();

            // Внешний ключ на communities
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('context_id')->references('id')->on('context_responses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('context_events');
    }
};
