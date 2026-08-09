<?php

namespace Tests\Feature;

use App\Models\GenerationIa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GenerationIaModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_constants_are_defined(): void
    {
        $this->assertEquals('pending', GenerationIa::STATUS_PENDING);
        $this->assertEquals('processing', GenerationIa::STATUS_PROCESSING);
        $this->assertEquals('completed', GenerationIa::STATUS_COMPLETED);
        $this->assertEquals('failed', GenerationIa::STATUS_FAILED);
    }

    public function test_model_has_correct_fillable_attributes(): void
    {
        $model = new GenerationIa;

        $this->assertContains('status', $model->getFillable());
        $this->assertContains('job_id', $model->getFillable());
        $this->assertContains('error_message', $model->getFillable());
        $this->assertContains('user_id', $model->getFillable());
        $this->assertContains('prompt', $model->getFillable());
    }

    public function test_is_pending_returns_true_when_status_is_pending(): void
    {
        $generation = GenerationIa::factory()->create([
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        $this->assertTrue($generation->isPending());
        $this->assertFalse($generation->isProcessing());
        $this->assertFalse($generation->isCompleted());
        $this->assertFalse($generation->isFailed());
    }

    public function test_is_processing_returns_true_when_status_is_processing(): void
    {
        $generation = GenerationIa::factory()->create([
            'status' => GenerationIa::STATUS_PROCESSING,
        ]);

        $this->assertFalse($generation->isPending());
        $this->assertTrue($generation->isProcessing());
        $this->assertFalse($generation->isCompleted());
        $this->assertFalse($generation->isFailed());
    }

    public function test_is_completed_returns_true_when_status_is_completed(): void
    {
        $generation = GenerationIa::factory()->create([
            'status' => GenerationIa::STATUS_COMPLETED,
        ]);

        $this->assertFalse($generation->isPending());
        $this->assertFalse($generation->isProcessing());
        $this->assertTrue($generation->isCompleted());
        $this->assertFalse($generation->isFailed());
    }

    public function test_is_failed_returns_true_when_status_is_failed(): void
    {
        $generation = GenerationIa::factory()->create([
            'status' => GenerationIa::STATUS_FAILED,
        ]);

        $this->assertFalse($generation->isPending());
        $this->assertFalse($generation->isProcessing());
        $this->assertFalse($generation->isCompleted());
        $this->assertTrue($generation->isFailed());
    }

    public function test_started_at_is_cast_to_datetime(): void
    {
        $generation = GenerationIa::factory()->create([
            'started_at' => now(),
        ]);

        $this->assertInstanceOf(Carbon::class, $generation->started_at);
    }

    public function test_completed_at_is_cast_to_datetime(): void
    {
        $generation = GenerationIa::factory()->create([
            'completed_at' => now(),
        ]);

        $this->assertInstanceOf(Carbon::class, $generation->completed_at);
    }

    public function test_started_at_is_null_when_not_set(): void
    {
        $generation = GenerationIa::factory()->create();

        $this->assertNull($generation->started_at);
    }

    public function test_completed_at_is_null_when_not_set(): void
    {
        $generation = GenerationIa::factory()->create();

        $this->assertNull($generation->completed_at);
    }

    public function test_generation_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $generation->user);
        $this->assertEquals($user->id, $generation->user->id);
    }

    public function test_default_status_is_pending(): void
    {
        $generation = GenerationIa::factory()->create();

        $this->assertEquals(GenerationIa::STATUS_PENDING, $generation->status);
    }
}
