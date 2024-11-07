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
        Schema::create('event_interest', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->index()->comment('Внешний ключ на events');
            $table->unsignedBigInteger('interest_id')->index()->comment('Внешний ключ на interests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_interest');
    }
};
