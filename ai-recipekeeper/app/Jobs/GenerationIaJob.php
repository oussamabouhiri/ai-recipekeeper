<?php

namespace App\Jobs;

use App\Models\GenerationIa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

abstract class GenerationIaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public array|int $backoff = [10, 30, 60];

    public function __construct(
        public GenerationIa $generation,
    ) {
        $this->queue = 'generations';
    }

    abstract public function handle(): void;

    public function failed(Throwable $exception): void
    {
        $this->generation->update([
            'status' => GenerationIa::STATUS_FAILED,
            'error_message' => $exception->getMessage(),
        ]);
    }
}
