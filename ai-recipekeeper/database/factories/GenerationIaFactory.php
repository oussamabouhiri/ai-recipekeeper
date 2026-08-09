<?php

namespace Database\Factories;

use App\Models\GenerationIa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GenerationIa>
 */
class GenerationIaFactory extends Factory
{
    protected $model = GenerationIa::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'prompt' => fake()->sentence(),
            'response' => null,
            'model_used' => 'gpt-4',
            'tokens_used' => null,
            'status' => GenerationIa::STATUS_PENDING,
            'job_id' => null,
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function processing(): static
    {
        return $this->state(fn () => [
            'status' => GenerationIa::STATUS_PROCESSING,
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => GenerationIa::STATUS_COMPLETED,
            'started_at' => now(),
            'completed_at' => now(),
            'response' => fake()->paragraph(),
            'tokens_used' => fake()->numberBetween(100, 1000),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => GenerationIa::STATUS_FAILED,
            'started_at' => now(),
            'error_message' => 'API request failed',
        ]);
    }
}
