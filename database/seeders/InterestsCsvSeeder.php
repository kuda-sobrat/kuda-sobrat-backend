<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InterestsCsvSeeder extends Seeder
{
    public function run()
    {
        // Путь к CSV файлам
        $interestsCsvPath = database_path('data/interests.csv');
        $relationsCsvPath = database_path('data/interest_relations.csv');

        // Очищаем таблицы перед импортом
        DB::table('interest_relations')->delete();
        DB::table('interests')->delete();

        // Импортируем таблицу interests
        if (File::exists($interestsCsvPath)) {
            $handle = fopen($interestsCsvPath, 'r');

            // Предполагаем, что в первой строке заголовки столбцов
            $header = fgetcsv($handle, 0, ',', '"');

            while (($data = fgetcsv($handle, 0, ',', '"')) !== FALSE) {
                $entry = array_combine($header, $data);
                DB::table('interests')->insert($entry);
            }

            fclose($handle);
            $this->command->info('Таблица interests успешно импортирована из CSV.');
        } else {
            $this->command->error('CSV файл не найден: ' . $interestsCsvPath);
        }

        // Импортируем таблицу interest_relations
        if (File::exists($relationsCsvPath)) {
            $handle = fopen($relationsCsvPath, 'r');

            // Предполагаем, что в первой строке заголовки столбцов
            $header = fgetcsv($handle, 0, ',', '"');

            while (($data = fgetcsv($handle, 0, ',', '"')) !== FALSE) {
                $entry = array_combine($header, $data);
                DB::table('interest_relations')->insert($entry);
            }

            fclose($handle);
            $this->command->info('Таблица interest_relations успешно импортирована из CSV.');
        } else {
            $this->command->error('CSV файл не найден: ' . $relationsCsvPath);
        }
    }
}
