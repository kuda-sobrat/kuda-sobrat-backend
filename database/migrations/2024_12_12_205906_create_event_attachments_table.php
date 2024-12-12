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
        Schema::create('event_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->index()->comment('Внешний ключ на event');
            $table->string('type')->comment('Тип вложения');
            $table->string('url', 1024)->nullable()->comment('URL вложения (если есть)');
            $table->string('title')->nullable()->comment('Название (если есть)');
            $table->text('text')->nullable()->comment('Текст, описание (если есть)');
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attachments');
    }
};
