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
        Schema::create('context_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('context_id')->index()->comment('Внешний ключ на context_requests');
            $table->text('response')->comment('Ответ от языковой модели');
            $table->string('model')->comment('Модель');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('context_responses');
    }
};
