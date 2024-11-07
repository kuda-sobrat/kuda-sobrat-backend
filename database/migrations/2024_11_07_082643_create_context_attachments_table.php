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
        Schema::create('context_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('context_id')->index()->comment('Внешний ключ на context_request');
            $table->string('url')->nullable()->comment('URL вложения (если есть)');
            $table->string('title')->nullable()->comment('Название (если есть)');
            $table->text('text')->nullable()->comment('Текст, описание (если есть)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('context_attachments');
    }
};
