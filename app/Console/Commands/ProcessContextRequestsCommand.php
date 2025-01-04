<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessContextRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'context-requests:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обрабатывает соответствующие записи из context_requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // TODO: Выделить обработку, запускать job-s отдельно
    }
}
