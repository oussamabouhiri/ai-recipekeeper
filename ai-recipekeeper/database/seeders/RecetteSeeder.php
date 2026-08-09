<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recette;
use Illuminate\Database\Seeder;

class RecetteSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'Entrée',
            'Plat principal',
            'Dessert',
        ])->map(fn (string $name) => Category::create([
            'name' => $name,
            'description' => null,
        ]));

        $ingredients = collect([
            'Farine', 'Œufs', 'Sucre', 'Lait', 'Beurre', 'Sel', 'Poivre', 'Tomates',
        ])->map(fn (string $name) => Ingredient::create(['name' => $name]));

        Recette::factory()->count(5)->create()->each(function (Recette $recette) use ($categories, $ingredients) {
            $recette->etapes()->createMany([
                ['step_number' => 1, 'instruction' => 'Préparer tous les ingrédients.'],
                ['step_number' => 2, 'instruction' => 'Mélanger puis faire cuire.'],
                ['step_number' => 3, 'instruction' => 'Laisser reposer et servir.'],
            ]);

            $recette->ingredients()->attach(
                $ingredients->random(3)->mapWithKeys(fn (Ingredient $ingredient) => [
                    $ingredient->id => [
                        'quantity' => (string) fake()->numberBetween(1, 500),
                        'unit' => fake()->randomElement(['g', 'ml', 'pièce', 'pincée']),
                    ],
                ])->all()
            );

            $recette->categories()->attach($categories->random(2)->pluck('id'));
        });
    }
}
