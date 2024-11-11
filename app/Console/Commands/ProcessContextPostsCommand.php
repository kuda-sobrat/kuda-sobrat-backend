<?php

namespace App\Console\Commands;

use App\Enums\ProcessStatusEnum;
use App\Jobs\ProcessContextJob;
use App\Models\ContextPost;
use Illuminate\Console\Command;

class ProcessContextPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'context-posts:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ставит в очередь на выполнение записи из context_posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contextPosts = ContextPost::query()->where('status', '=', ProcessStatusEnum::Created->value)->get();

        foreach ($contextPosts as $contextPost) {
            ProcessContextJob::dispatch($contextPost->id, ContextPost::class);
            $this->info("ContextPost ID {$contextPost->id} был поставлен в очередь.");
        }

        $this->info('Все ожидающие ContextPosts были поставлены в очередь на выполнение.');
    }
}
