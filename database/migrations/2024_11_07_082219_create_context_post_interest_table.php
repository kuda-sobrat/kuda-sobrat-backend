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
        Schema::create('context_post_interest', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->index()->comment('Внешний ключ на communities');
            $table->unsignedBigInteger('interest_id')->index()->comment('Внешний ключ на interests');
            $table->float('confidence')->comment('Уровень уверенности (от 0 до 1)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('context_post_interest');
    }
};
