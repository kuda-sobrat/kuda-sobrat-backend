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
        Schema::create('community_social_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_id')->index()->comment('Внешний ключ на communities');
            $table->unsignedBigInteger('social_network_id')->index()->comment('Внешний ключ на social_networks');
            $table->string('social_network_community_id')->nullable()->comment('ID сообщества в соц. сети');
            $table->string('path')->comment('Ссылка на сообщество');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_social_links');
    }
};
