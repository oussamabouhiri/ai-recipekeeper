<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Marie Dupont',
                'email' => 'marie@example.com',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'recipes' => [
                    [
                        'title' => 'Tarte aux Fraises',
                        'description' => 'Une tarte délicieuse aux fraises fraîches avec une crème pâtissière vanillée.',
                        'prep_time' => 30,
                        'cook_time' => 25,
                        'servings' => 8,
                        'difficulty' => 'Moyen',
                        'statut' => 'published',
                        'categories' => ['Dessert', 'Pâtisseries'],
                        'ingredients' => [
                            ['name' => 'Farine', 'quantity' => '250', 'unit' => 'g'],
                            ['name' => 'Beurre', 'quantity' => '125', 'unit' => 'g'],
                            ['name' => 'Fraises', 'quantity' => '500', 'unit' => 'g'],
                            ['name' => 'Crème fraîche', 'quantity' => '20', 'unit' => 'cl'],
                            ['name' => 'Sucre', 'quantity' => '100', 'unit' => 'g'],
                        ],
                        'steps' => [
                            'Préparez la pâte brisée avec la farine, le beurre et un peu d\'eau.',
                            'Étalez la pâte dans un moule et piquez le fond.',
                            'Faites cuire à blanc 15 minutes.',
                            'Préparez la crème pâtissière et versez-la sur le fond.',
                            'Disposez les fraises sur la crème.',
                            'Vernissez avec de la confiture de fraises.',
                        ],
                    ],
                    [
                        'title' => 'Salade de Quinoa',
                        'description' => 'Une salade saine et légère au quinoa, légumes croquants et vinaigrette citronnée.',
                        'prep_time' => 15,
                        'cook_time' => 15,
                        'servings' => 4,
                        'difficulty' => 'Facile',
                        'statut' => 'published',
                        'categories' => ['Salades', 'Végétarien'],
                        'ingredients' => [
                            ['name' => 'Riz', 'quantity' => '200', 'unit' => 'g'],
                            ['name' => 'Concombre', 'quantity' => '1', 'unit' => 'pièce'],
                            ['name' => 'Tomates', 'quantity' => '2', 'unit' => 'pièces'],
                            ['name' => 'Citron', 'quantity' => '1', 'unit' => 'jus'],
                            ['name' => 'Huile d\'olive', 'quantity' => '3', 'unit' => 'c. à soupe'],
                        ],
                        'steps' => [
                            'Faites cuire le quinoa selon les instructions.',
                            'Coupez les légumes en petits dés.',
                            'Mélangez le quinoa refroidi avec les légumes.',
                            'Assaisonnez avec le jus de citron et l\'huile d\'olive.',
                            'Servez frais.',
                        ],
                    ],
                    [
                        'title' => 'Soupe de Poissons',
                        'description' => 'Une soupe de poissons traditionnelle, parfumée au safran et servie avec des croûtons.',
                        'prep_time' => 20,
                        'cook_time' => 40,
                        'servings' => 6,
                        'difficulty' => 'Moyen',
                        'statut' => 'published',
                        'categories' => ['Soupes'],
                        'ingredients' => [
                            ['name' => 'Saumon', 'quantity' => '400', 'unit' => 'g'],
                            ['name' => 'Oignons', 'quantity' => '2', 'unit' => 'pièces'],
                            ['name' => 'Ail', 'quantity' => '3', 'unit' => 'gousses'],
                            ['name' => 'Tomates', 'quantity' => '3', 'unit' => 'pièces'],
                            ['name' => 'Vin blanc', 'quantity' => '15', 'unit' => 'cl'],
                        ],
                        'steps' => [
                            'Faites revenir les oignons et l\'ail dans l\'huile d\'olive.',
                            'Ajoutez les tomates concassées et le vin blanc.',
                            'Versez le bouillon et laissez mijoter 20 minutes.',
                            'Ajoutez les morceaux de poisson et cuisez 15 minutes.',
                            'Mixez et passez au tamis.',
                            'Servez avec des croûtons de pain grillé.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Jean Martin',
                'email' => 'jean@example.com',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'recipes' => [
                    [
                        'title' => 'Poulet au Citron',
                        'description' => 'Des cuisses de poulet marinées au citron et aux herbes, rôties au four.',
                        'prep_time' => 15,
                        'cook_time' => 45,
                        'servings' => 4,
                        'difficulty' => 'Facile',
                        'statut' => 'published',
                        'categories' => ['Plat principal'],
                        'ingredients' => [
                            ['name' => 'Poulet', 'quantity' => '8', 'unit' => 'cuisses'],
                            ['name' => 'Citron', 'quantity' => '2', 'unit' => 'pièces'],
                            ['name' => 'Thym', 'quantity' => '1', 'unit' => 'bouquet'],
                            ['name' => 'Ail', 'quantity' => '4', 'unit' => 'gousses'],
                            ['name' => 'Huile d\'olive', 'quantity' => '3', 'unit' => 'c. à soupe'],
                        ],
                        'steps' => [
                            'Marinez les cuisses de poulet avec le citron, l\'ail et les herbes.',
                            'Laissez reposer au moins 1 heure au réfrigérateur.',
                            'Préchauffez le four à 200°C.',
                            'Faites dorer les cuisses dans une poêle.',
                            'Enfournez 35 minutes.',
                            'Servez avec du riz ou des légumes.',
                        ],
                    ],
                    [
                        'title' => 'Pâtes au Pesto',
                        'description' => 'Des spaghettis frais au pesto maison, parmesan et pignons grillés.',
                        'prep_time' => 10,
                        'cook_time' => 12,
                        'servings' => 4,
                        'difficulty' => 'Facile',
                        'statut' => 'published',
                        'categories' => ['Plat principal', 'Rapide'],
                        'ingredients' => [
                            ['name' => 'Pâtes', 'quantity' => '400', 'unit' => 'g'],
                            ['name' => 'Basilic', 'quantity' => '1', 'unit' => 'botte'],
                            ['name' => 'Parmesan', 'quantity' => '80', 'unit' => 'g'],
                            ['name' => 'Ail', 'quantity' => '2', 'unit' => 'gousses'],
                            ['name' => 'Huile d\'olive', 'quantity' => '10', 'unit' => 'c. à soupe'],
                        ],
                        'steps' => [
                            'Faites cuire les pâtes al dente.',
                            'Pendant ce temps, mixez le basilic, l\'ail, le parmesan et l\'huile d\'olive.',
                            'Égouttez les pâtes en réservant un peu d\'eau de cuisson.',
                            'Mélangez les pâtes chaudes avec le pesto.',
                            'Ajoutez un peu d\'eau de cuisson si nécessaire.',
                            'Parsemez de parmesan et servez immédiatement.',
                        ],
                    ],
                    [
                        'title' => 'Brownies au Chocolat',
                        'description' => 'Des brownies fondants au chocolat noir, croustillants à l\'extérieur et moelleux à l\'intérieur.',
                        'prep_time' => 15,
                        'cook_time' => 25,
                        'servings' => 12,
                        'difficulty' => 'Facile',
                        'statut' => 'published',
                        'categories' => ['Dessert'],
                        'ingredients' => [
                            ['name' => 'Chocolat noir', 'quantity' => '200', 'unit' => 'g'],
                            ['name' => 'Beurre', 'quantity' => '150', 'unit' => 'g'],
                            ['name' => 'Œufs', 'quantity' => '3', 'unit' => 'pièces'],
                            ['name' => 'Sucre', 'quantity' => '200', 'unit' => 'g'],
                            ['name' => 'Farine', 'quantity' => '100', 'unit' => 'g'],
                        ],
                        'steps' => [
                            'Préchauffez le four à 180°C.',
                            'Faites fondre le chocolat et le beurre au bain-marie.',
                            'Fouettez les œufs avec le sucre.',
                            'Incorporez le mélange chocolat fondu.',
                            'Ajoutez la farine tamisée et mélangez.',
                            'Versez dans un moule et enfournez 25 minutes.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Sophie Bernard',
                'email' => 'sophie@example.com',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'recipes' => [
                    [
                        'title' => 'Curry de Légumes',
                        'description' => 'Un curry de légumes coloré et parfumé, servi avec du riz basmati.',
                        'prep_time' => 15,
                        'cook_time' => 30,
                        'servings' => 4,
                        'difficulty' => 'Facile',
                        'statut' => 'published',
                        'categories' => ['Plat principal', 'Végétarien'],
                        'ingredients' => [
                            ['name' => 'Courgettes', 'quantity' => '2', 'unit' => 'pièces'],
                            ['name' => 'Poivrons', 'quantity' => '2', 'unit' => 'pièces'],
                            ['name' => 'Pois chiches', 'quantity' => '400', 'unit' => 'g'],
                            ['name' => 'Crème fraîche', 'quantity' => '20', 'unit' => 'cl'],
                            ['name' => 'Curry', 'quantity' => '2', 'unit' => 'c. à soupe'],
                        ],
                        'steps' => [
                            'Coupez les légumes en morceaux.',
                            'Faites-les revenir dans l\'huile d\'olive.',
                            'Ajoutez le curry et remuez 1 minute.',
                            'Versez la crème fraîche et laissez mijoter 20 minutes.',
                            'Ajoutez les pois chiches égouttés.',
                            'Servez avec du riz basmati.',
                        ],
                    ],
                    [
                        'title' => 'Smoothie Bowl',
                        'description' => 'Une bowl de smoothie à l\'açaí, garnie de fruits frais et de granola.',
                        'prep_time' => 10,
                        'cook_time' => 0,
                        'servings' => 2,
                        'difficulty' => 'Facile',
                        'statut' => 'published',
                        'categories' => ['Dessert', 'Rapide'],
                        'ingredients' => [
                            ['name' => 'Myrtilles', 'quantity' => '200', 'unit' => 'g'],
                            ['name' => 'Bananes', 'quantity' => '2', 'unit' => 'pièces'],
                            ['name' => 'Yaourt nature', 'quantity' => '150', 'unit' => 'g'],
                            ['name' => 'Amandes', 'quantity' => '30', 'unit' => 'g'],
                        ],
                        'steps' => [
                            'Mixez les myrtilles, la banane et le yaourt.',
                            'Versez dans un bol.',
                            'Garnissez de fruits frais et de granola.',
                            'Parsemez d\'amandes et servez immédiatement.',
                        ],
                    ],
                    [
                        'title' => 'Tartelettes au Citron',
                        'description' => 'Des mini tartelettes au citron meringuée, parfaites pour un dessert élégant.',
                        'prep_time' => 30,
                        'cook_time' => 20,
                        'servings' => 6,
                        'difficulty' => 'Moyen',
                        'statut' => 'published',
                        'categories' => ['Dessert', 'Pâtisseries'],
                        'ingredients' => [
                            ['name' => 'Farine', 'quantity' => '200', 'unit' => 'g'],
                            ['name' => 'Beurre', 'quantity' => '100', 'unit' => 'g'],
                            ['name' => 'Citron', 'quantity' => '4', 'unit' => 'jus'],
                            ['name' => 'Œufs', 'quantity' => '3', 'unit' => 'pièces'],
                            ['name' => 'Sucre', 'quantity' => '150', 'unit' => 'g'],
                        ],
                        'steps' => [
                            'Préparez la pâte sucrée et étalez-la dans des moules.',
                            'Faites cuire à blanc 10 minutes.',
                            'Préparez la crème au citron.',
                            'Versez la crème sur les fonds de tarte.',
                            'Préparez la meringue italienne.',
                            'Garnissez de meringue et caramélisez au chalumeau.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($users as $userData) {
            $recipes = $userData['recipes'];
            unset($userData['recipes']);

            $user = User::create($userData);

            $categories = Category::pluck('id', 'name');

            foreach ($recipes as $recipeData) {
                $recipeCategories = $recipeData['categories'];
                unset($recipeData['categories']);

                $ingredients = $recipeData['ingredients'];
                unset($recipeData['ingredients']);

                $steps = $recipeData['steps'];
                unset($recipeData['steps']);

                $recipeData['user_id'] = $user->id;
                $recipeData['is_ai_generated'] = false;

                $recipe = Recette::create($recipeData);

                foreach ($steps as $index => $instruction) {
                    $recipe->etapes()->create([
                        'step_number' => $index + 1,
                        'instruction' => $instruction,
                    ]);
                }

                foreach ($ingredients as $ingredientData) {
                    $ingredient = Ingredient::firstOrCreate(
                        ['name' => $ingredientData['name']]
                    );
                    $recipe->ingredients()->syncWithoutDetaching([
                        $ingredient->id => [
                            'quantity' => $ingredientData['quantity'],
                            'unit' => $ingredientData['unit'],
                        ],
                    ]);
                }

                foreach ($recipeCategories as $categoryName) {
                    if (isset($categories[$categoryName])) {
                        $recipe->categories()->syncWithoutDetaching([$categories[$categoryName]]);
                    }
                }
            }
        }
    }
}
