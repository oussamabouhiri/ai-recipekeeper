<?php

namespace Tests\Jobs;

use App\Jobs\GenerationIaJob;
use App\Models\GenerationIa;

class TestGenerationIaJob extends GenerationIaJob
{
    public function handle(): void
    {
        $this->generation->update([
            'status' => GenerationIa::STATUS_PROCESSING,
            'started_at' => now(),
        ]);

        // Simulate work

        $this->generation->update([
            'status' => GenerationIa::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }
}
