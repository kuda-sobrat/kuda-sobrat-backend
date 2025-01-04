<?php

namespace App\Jobs;

use App\Services\Context\ContextService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessContextJob implements ShouldQueue
{
    use Batchable, Dispatchable, Queueable, SerializesModels;

    /**
     * Создание нового экземпляра задания.
     *
     * @param int $contextId
     * @param string $contextClass
     */
    public function __construct(
        protected int $contextId,
        protected string $contextClass) {
    }

    /**
     * @param ContextService $contextService
     * @return void
     * @throws \Exception
     */
    public function handle(ContextService $contextService): void
    {
        $contextService->processContext($this->contextId, $this->contextClass);
    }
}
