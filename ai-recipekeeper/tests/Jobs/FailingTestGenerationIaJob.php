<?php

namespace Tests\Jobs;

use App\Jobs\GenerationIaJob;

class FailingTestGenerationIaJob extends GenerationIaJob
{
    public function handle(): void
    {
        throw new \RuntimeException('Simulated AI API failure');
    }
}
