<?php

namespace App\Jobs;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class TestJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Bus::batch([new FetchEventInterestsFromGPTJob(1)])
            ->then(function (Batch $batch) {
                Log::info('Then callback called.');
            })
            ->catch(function (Batch $batch, Throwable $e) {
                Log::error('Catch callback called: ' . $e->getMessage());
            })
            ->finally(function (Batch $batch) {
                Log::info('Finally callback called.');
            })
            ->dispatch();

        Log::info('After dispatch.');
    }
}
