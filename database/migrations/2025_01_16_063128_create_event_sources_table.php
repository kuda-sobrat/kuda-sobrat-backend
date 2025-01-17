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
        Schema::create('event_sources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->index()->comment('ID мероприятия');
            $table->unsignedBigInteger('social_link_id')->index()->comment('ID социальной сети');
            $table->unsignedBigInteger('source_id')->index()->comment('ID поста из источника');
            $table->string('generated_link')->nullable()->comment('Ссылка на источник');

            // Внешние ключи
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('social_link_id')->references('id')->on('community_social_links')->onDelete('cascade');

            $table->unique(['event_id', 'social_link_id', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sources');
    }
};
