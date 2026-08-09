<?php

namespace Tests\Feature;

use App\Models\GenerationIa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Jobs\FailingTestGenerationIaJob;
use Tests\Jobs\TestGenerationIaJob;
use Tests\TestCase;

class GenerationIaJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_base_job_has_correct_defaults(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->create(['user_id' => $user->id]);

        $job = new TestGenerationIaJob($generation);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(120, $job->timeout);
        $this->assertEquals([10, 30, 60], $job->backoff);
        $this->assertEquals('generations', $job->queue);
    }

    public function test_base_job_accepts_generation_model(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->create(['user_id' => $user->id]);

        $job = new TestGenerationIaJob($generation);

        $this->assertEquals($generation->id, $job->generation->id);
    }

    public function test_job_executes_and_updates_status_to_completed(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        TestGenerationIaJob::dispatchSync($generation);

        $generation->refresh();
        $this->assertEquals(GenerationIa::STATUS_COMPLETED, $generation->status);
        $this->assertNotNull($generation->completed_at);
    }

    public function test_job_sets_started_at_on_processing(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        $job = new TestGenerationIaJob($generation);
        $job->handle();

        $generation->refresh();
        $this->assertNotNull($generation->started_at);
    }

    public function test_failed_job_updates_status_to_failed(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        try {
            FailingTestGenerationIaJob::dispatchSync($generation);
        } catch (\Throwable $e) {
            // Expected exception
        }

        $generation->refresh();
        $this->assertEquals(GenerationIa::STATUS_FAILED, $generation->status);
        $this->assertEquals('Simulated AI API failure', $generation->error_message);
    }

    public function test_failed_job_records_exception_message(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        $job = new FailingTestGenerationIaJob($generation);

        try {
            $job->handle();
        } catch (\Throwable $e) {
            $job->failed($e);
        }

        $generation->refresh();
        $this->assertNotNull($generation->error_message);
        $this->assertStringContainsString('Simulated AI API failure', $generation->error_message);
    }

    public function test_job_dispatched_to_generations_queue(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->create(['user_id' => $user->id]);

        Queue::fake();

        TestGenerationIaJob::dispatch($generation);

        Queue::assertPushed(TestGenerationIaJob::class, function ($job) {
            return $job->queue === 'generations';
        });
    }

    public function test_generation_ia_factory_creates_pending_by_default(): void
    {
        $generation = GenerationIa::factory()->create();

        $this->assertEquals(GenerationIa::STATUS_PENDING, $generation->status);
        $this->assertNull($generation->job_id);
        $this->assertNull($generation->error_message);
        $this->assertNull($generation->started_at);
        $this->assertNull($generation->completed_at);
    }

    public function test_generation_ia_factory_processing_state(): void
    {
        $generation = GenerationIa::factory()->processing()->create();

        $this->assertEquals(GenerationIa::STATUS_PROCESSING, $generation->status);
        $this->assertNotNull($generation->started_at);
    }

    public function test_generation_ia_factory_completed_state(): void
    {
        $generation = GenerationIa::factory()->completed()->create();

        $this->assertEquals(GenerationIa::STATUS_COMPLETED, $generation->status);
        $this->assertNotNull($generation->started_at);
        $this->assertNotNull($generation->completed_at);
        $this->assertNotNull($generation->response);
        $this->assertNotNull($generation->tokens_used);
    }

    public function test_generation_ia_factory_failed_state(): void
    {
        $generation = GenerationIa::factory()->failed()->create();

        $this->assertEquals(GenerationIa::STATUS_FAILED, $generation->status);
        $this->assertNotNull($generation->started_at);
        $this->assertNotNull($generation->error_message);
    }
}
