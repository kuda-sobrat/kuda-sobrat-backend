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
        Schema::create('context_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->index()->nullable()->comment('Внешний ключ на events');
            $table->string('source_id')->nullable()->comment('Идентификатор поста в соц. сети');
            $table->text('text')->comment('Исходный текст поста');
            $table->text('processed_text')->comment('Обработанный текст (поле обработки chatGPT)');
            $table->string('unique_hash')->index()->comment('Хэш поста (для предотвращения дубликатов)');
            $table->unsignedBigInteger('context_request_id')->index()->comment('Внешний ключ на context_requests');
            $table->unsignedBigInteger('context_response_id')->index()->comment('Внешний ключ на context_responses');
            $table->json('tags')->comment('Тэги');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('context_posts');
    }
};
