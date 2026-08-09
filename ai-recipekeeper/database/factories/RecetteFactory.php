<?php

namespace Database\Factories;

use App\Models\Recette;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recette>
 */
class RecetteFactory extends Factory
{
    protected $model = Recette::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'prep_time' => fake()->numberBetween(5, 120),
            'cook_time' => fake()->numberBetween(5, 240),
            'servings' => fake()->numberBetween(1, 12),
            'difficulty' => fake()->randomElement(['Facile', 'Moyen', 'Difficile']),
            'image_path' => null,
            'statut' => 'published',
            'user_id' => User::factory(),
            'is_ai_generated' => false,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn () => ['statut' => 'hidden']);
    }
}
