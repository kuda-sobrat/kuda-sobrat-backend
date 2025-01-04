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
        Schema::create('interest_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('interest_id')->index()->comment('Дочерний интерес');
            $table->unsignedBigInteger('parent_interest_id')->index()->comment('Родительский интерес');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interest_relations');
    }
};
