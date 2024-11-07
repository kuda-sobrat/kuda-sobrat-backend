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
        Schema::table('context_attachments', function (Blueprint $table) {
            $table->string('url', 1024)->nullable()->comment('URL вложения (если есть)')->change();
            $table->string('type')->after('context_id')->comment('Тип приложения');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('context_attachments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
