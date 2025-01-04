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
        Schema::table('event_shares', function (Blueprint $table) {
            $table->unique(['event_id', 'user_id', 'social_network_id'], 'event_user_social_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_shares', function (Blueprint $table) {
            $table->dropUnique('event_user_social_unique');
        });
    }
};
