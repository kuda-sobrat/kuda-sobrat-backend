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
//            $table->unsignedBigInteger('social_link_id')->after('id')->index()->nullable()->comment('Внешний ключ social_links');
            $table->foreign('social_link_id')->references('id')->on('community_social_links')->onDelete('cascade');
//            $table->dropColumn('community_id');
//            $table->dropColumn('source_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('context_posts', function (Blueprint $table) {
            $table->dropForeign(['social_link_id']);
//            $table->dropColumn('social_link_id');
        });
    }
};
