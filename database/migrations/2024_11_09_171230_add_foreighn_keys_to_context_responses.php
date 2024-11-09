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
        Schema::table('context_responses', function (Blueprint $table) {
            $table->foreign('context_id')
                ->references('id')->on('context_requests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('context_responses', function (Blueprint $table) {
            $table->dropForeign(['context_id']);
        });
    }
};
