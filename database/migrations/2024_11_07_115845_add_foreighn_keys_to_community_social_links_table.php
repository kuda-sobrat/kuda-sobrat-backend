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
        Schema::table('community_social_links', function (Blueprint $table) {
            $table->foreign('community_id')
                ->references('id')->on('communities')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_social_links', function (Blueprint $table) {
            $table->dropForeign(['community_id']);
        });
    }
};
