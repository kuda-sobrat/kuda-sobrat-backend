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
        Schema::create('community_interest', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_id')->index()->comment('Внешний ключ на communities');
            $table->unsignedBigInteger('interest_id')->index()->comment('Внешний ключ на interests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_interest');
    }
};
