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
            $table->enum('status', ['created', 'pending', 'completed', 'failed'])->after('event_id')->default('created')->index()->comment('Статус обработки');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('context_posts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
