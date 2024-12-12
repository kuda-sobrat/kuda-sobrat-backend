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
        Schema::table('context_events', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('context_events', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable();

            // Если ранее был внешний ключ, можно его снова установить
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }
};
