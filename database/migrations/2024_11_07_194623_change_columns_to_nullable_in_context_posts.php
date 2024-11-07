<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('context_posts', function (Blueprint $table) {
            $table->text('processed_text')->nullable()->comment('Обработанный текст (поле обработки chatGPT)')->change();
            $table->unsignedBigInteger('context_request_id')->nullable()->comment('Внешний ключ на context_requests')->change();
            $table->unsignedBigInteger('context_response_id')->nullable()->comment('Внешний ключ на context_responses')->change();
            $table->json('tags')->nullable()->comment('Тэги')->change();
            $table->string('source_type')->after('source_id')->index()->comment('Тип источника');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Перед изменением столбцов на NOT NULL, заменяем NULL значения на не NULL
        DB::table('context_posts')->whereNull('processed_text')->update(['processed_text' => '']);
        DB::table('context_posts')->whereNull('context_request_id')->update(['context_request_id' => 0]);
        DB::table('context_posts')->whereNull('context_response_id')->update(['context_response_id' => 0]);
        DB::table('context_posts')->whereNull('tags')->update(['tags' => json_encode([])]);

        Schema::table('context_posts', function (Blueprint $table) {
            $table->text('processed_text')->nullable(false)->comment('Обработанный текст (поле обработки chatGPT)')->change();
            $table->unsignedBigInteger('context_request_id')->nullable(false)->default(0)->comment('Внешний ключ на context_requests')->change();
            $table->unsignedBigInteger('context_response_id')->nullable(false)->default(0)->comment('Внешний ключ на context_responses')->change();
            $table->json('tags')->nullable(false)->comment('Тэги')->change();
            $table->dropColumn('source_type');
        });
    }
};
