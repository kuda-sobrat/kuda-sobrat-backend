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
        Schema::table('communities', function (Blueprint $table) {
            $table->string('city')->nullable()->after('description')->comment('Город');
            $table->string('street')->nullable()->after('city')->comment('Улица');
            $table->string('house')->nullable()->after('street')->comment('Дом');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropColumn(['city', 'street', 'house']);
        });
    }
};
