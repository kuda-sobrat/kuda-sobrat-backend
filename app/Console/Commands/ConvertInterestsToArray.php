<?php

namespace App\Console\Commands;

use App\Models\Interest;
use App\Repositories\InterestRepository;
use Illuminate\Console\Command;

class ConvertInterestsToArray extends Command
{
    protected $signature = 'interests:convert-to-array';

    protected $description = 'Преобразует список интересов в массив и выводит его';

    /**
     * Execute the console command.
     */
    public function handle(
        InterestRepository $interestRepository
    ) {
        // Получаем все интересы
        $interests = Interest::with('children')->get();

        // Построим дерево интересов
        $tree = $interestRepository->getTree();

        // Выводим массив в консоль
        $this->info('Список интересов в виде иерархического массива:');
        dd($tree);
    }
}
