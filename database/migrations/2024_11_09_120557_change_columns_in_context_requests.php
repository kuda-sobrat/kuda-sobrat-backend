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
        Schema::table('context_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed', 'failed'])->comment('Статус запроса')->change();
            $table->unsignedBigInteger('context_id')->after('id')->index()->comment('Внешний ключ на context_posts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('context_requests', function (Blueprint $table) {
            $table->dropColumn('context_id');
        });
    }
};
