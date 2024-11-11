<?php

namespace App\Console\Commands;

use App\Enums\ProcessStatusEnum;
use App\Jobs\ProcessContextJob;
use App\Models\ContextResponse;
use Illuminate\Console\Command;

class ProcessContextResponsesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'context-responses:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обрабатывает соответствующие записи из context_responses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contextResponses = ContextResponse::with('contextRequest')
            ->whereHas('contextRequest', function($query) {
                $query->where('type', 'setup_event');
            })
            ->where('status', '=', ProcessStatusEnum::Created->value)
            ->get();

        foreach ($contextResponses as $contextResponse) {
            try {
                ProcessContextJob::dispatch($contextResponse->id, ContextResponse::class);
                $this->info("ContextResponse ID {$contextResponse->id} был поставлен в очередь.");
            } catch (\Exception $e) {
                $contextResponse->update(['status' => ProcessStatusEnum::Failed->value]);
                $this->info("ContextResponse ID {$contextResponse->id} завершилась с ошибкой: " . $e->getMessage());
            }
        }

        $this->info('Все ожидающие ContextResponse были поставлены в очередь на выполнение.');
    }
}
