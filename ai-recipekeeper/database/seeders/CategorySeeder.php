<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Entrée', 'description' => 'Plats d\'entrée et apéritifs'],
            ['name' => 'Plat principal', 'description' => 'Plats principaux'],
            ['name' => 'Dessert', 'description' => 'Desserts et pâtisseries'],
            ['name' => 'Boisson', 'description' => 'Boissons et cocktails'],
            ['name' => 'Végétarien', 'description' => 'Recettes sans viande ni poisson'],
            ['name' => 'Rapide', 'description' => 'Recettes en moins de 30 minutes'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
