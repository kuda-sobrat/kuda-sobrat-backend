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
        Schema::create('context_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index()->comment('Тип источника ("vk", "telegram" и т.д.)');
            $table->text('context')->comment('Сформированный контекст');
            $table->enum('status', ['created', 'sent', 'received', 'processed', 'error'])->comment('Статус запроса');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('context_requests');
    }
};
