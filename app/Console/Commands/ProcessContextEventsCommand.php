<?php

namespace App\Console\Commands;

use App\Enums\ProcessStatusEnum;
use App\Jobs\ProcessContextJob;
use App\Models\ContextEvent;
use App\Models\ContextResponse;
use App\Services\Context\ContextEventService;
use Illuminate\Console\Command;

class ProcessContextEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'context-responses:events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обрабатывает соответствующие записи из context_events';

    public function __construct(
      public ContextEventService $contextEventService,
    ) {
        parent::__construct();
    }

    /**
     * Обработка записей завершившихся ошибкой
     *
     * @return void
     */
    public function processFailed() {
        $contextEventsFailed = ContextEvent::query()->where('status', '=', ProcessStatusEnum::Failed->value)->orderByDesc('start_datetime')->get();

        foreach ($contextEventsFailed as $contextEventFailed) {
            try {
                /** @var ContextEvent $contextEventFailed */
                $this->contextEventService->processContext($contextEventFailed->id, ContextEvent::class);
                $contextEventFailed->status = ProcessStatusEnum::Completed;
                $contextEventFailed->contextPost->status = ProcessStatusEnum::Completed;
                $contextEventFailed->save();
                $contextEventFailed->contextPost->save();
                dump('nice', $contextEventFailed->name);
            } catch (\Exception $e) {
                dump($e->getMessage());
                $this->error($e->getMessage());
            }
        }
        $this->info('Все ожидающие ContextEventFailed были поставлены в очередь на выполнение.');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->processFailed();
    }
}
