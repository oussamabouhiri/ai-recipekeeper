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
            ['name' => 'Entrées froides', 'description' => 'Hors-d\'œuvres et entrées servies froides'],
            ['name' => 'Soupes', 'description' => 'Potages et crèmes veloutées'],
            ['name' => 'Salades', 'description' => 'Salades composées et vertes'],
            ['name' => 'Pâtisseries', 'description' => 'Gâteaux, tartes et douceurs'],
            ['name' => 'Accompagnements', 'description' => 'Garnitures et côtés'],
            ['name' => 'Plats de résistance', 'description' => 'Grands plats pour les occasions spéciales'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
