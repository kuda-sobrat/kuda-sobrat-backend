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
            $table->unsignedBigInteger('event_group_id')->index()->nullable()->after('id')->comment('ID группы мероприятий');

            // Добавляем внешний ключ
            $table->foreign('event_group_id')
                ->references('id')
                ->on('event_groups')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('context_events', function (Blueprint $table) {
            // Удаляем внешний ключ и столбец
            $table->dropForeign(['event_group_id']);
            $table->dropColumn('event_group_id');
        });
    }
};
