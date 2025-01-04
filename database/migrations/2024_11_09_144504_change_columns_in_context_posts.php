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
        Schema::table('context_posts', function (Blueprint $table) {
            $table->dropColumn('context_request_id');
            $table->dropColumn('context_response_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('context_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('context_request_id')->index()->comment('Внешний ключ на context_requests');
            $table->unsignedBigInteger('context_response_id')->index()->comment('Внешний ключ на context_responses');
        });
    }
};
