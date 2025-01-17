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
            $table->enum('type', ['paid', 'free', 'by_appointment'])
                ->after('attendees')
                ->index()
                ->nullable()
                ->comment('Тип мероприятия: платное, бесплатное, по записи, не определено');

            $table->decimal('cost')
                ->after('type')
                ->nullable()
                ->comment('Стоимость мероприятия');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('cost');
        });
    }
};
