<?php

namespace Database\Factories;

use App\Models\Favori;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favori>
 */
class FavoriFactory extends Factory
{
    protected $model = Favori::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recette_id' => Recette::factory(),
        ];
    }
}
