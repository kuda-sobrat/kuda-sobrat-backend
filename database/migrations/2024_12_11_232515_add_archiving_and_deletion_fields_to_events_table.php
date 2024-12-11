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
        Schema::table('events', function (Blueprint $table) {
            // Флаг, указывающий на то, что событие заархивировано
            $table->boolean('is_archived')->default(false)->index()->after('popularity_score')->comment('Событие заархивировано?');

            // Время, когда событие было заархивировано
            $table->timestamp('archived_at')->nullable()->after('is_archived')->comment('Время архивации');

            // Время, когда событие было помечено для удаления
            $table->timestamp('marked_for_deletion_at')->nullable()->after('archived_at')->comment('Время архивации');

            // Добавление столбца для мягкого удаления (soft deletes)
            $table->softDeletes()->comment('Столбец для мягкого удаления'); // Добавляет столбец deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'archived_at', 'marked_for_deletion_at']);
            $table->dropSoftDeletes();
        });
    }
};
