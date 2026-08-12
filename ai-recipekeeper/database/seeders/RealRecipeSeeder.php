<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Database\Seeder;

class RealRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $categories = Category::pluck('id', 'name');

        $recipes = $this->getRecipes();

        foreach ($recipes as $recipeData) {
            $recipe = Recette::firstOrCreate(
                ['title' => $recipeData['title']],
                [
                    'description' => $recipeData['description'],
                    'prep_time' => $recipeData['prep_time'],
                    'cook_time' => $recipeData['cook_time'],
                    'servings' => $recipeData['servings'],
                    'difficulty' => $recipeData['difficulty'],
                    'user_id' => $user->id,
                    'is_ai_generated' => false,
                    'statut' => 'published',
                ]
            );

            foreach ($recipeData['steps'] as $index => $instruction) {
                $recipe->etapes()->firstOrCreate(
                    ['step_number' => $index + 1],
                    ['instruction' => $instruction]
                );
            }

            foreach ($recipeData['ingredients'] as $ingredientData) {
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

            foreach ($recipeData['categories'] as $categoryName) {
                if (isset($categories[$categoryName])) {
                    $recipe->categories()->syncWithoutDetaching([$categories[$categoryName]]);
                }
            }
        }
    }

    private function getRecipes(): array
    {
        return [
            $this->coqAuVin(),
            $this->ratatouille(),
            $this->tarteTatin(),
            $this->bouillabaisse(),
            $this->boeufBourguignon(),
            $this->quicheLorraine(),
            $this->cremeBrulee(),
            $this->soupeAOignon(),
            $this->pouletRôti(),
            $this->saladeNicoise(),
            $this->magretDeCanard(),
            $this->gratinDauphinois(),
            $this->mousseAuChocolat(),
            $this->couscousAuPoulet(),
            $this->tarteAuCitronMeringuee(),
            $this->steakFrites(),
            $this->patesCarbonara(),
            $this->pouletBasquaise(),
            $this->tarteAuFromage(),
            $this->chiliConCarne(),
            $this->potAuFeu(),
            $this->saladeCesar(),
            $this->clafoutisAuxCerises(),
            $this->risottoAuxChampignons(),
            $this->tartareSaumon(),
            $this->saumonGlaceAuMielEtCarottesRoties(),
        ];
    }

    private function coqAuVin(): array
    {
        return [
            'title' => 'Coq au Vin',
            'description' => 'Un classique de la cuisine française, le coq au vin est un plat mijoté à base de poulet mariné dans du vin rouge avec des champignons, des lardons et des oignons grelots.',
            'prep_time' => 30,
            'cook_time' => 120,
            'servings' => 6,
            'difficulty' => 'Moyen',
            'categories' => ['Plat principal', 'Plats de résistance'],
            'ingredients' => [
                ['name' => 'Poulet', 'quantity' => '1', 'unit' => 'poulet entier'],
                ['name' => 'Vin rouge', 'quantity' => '75', 'unit' => 'cl'],
                ['name' => 'Lardons', 'quantity' => '200', 'unit' => 'g'],
                ['name' => 'Champignons', 'quantity' => '250', 'unit' => 'g'],
                ['name' => 'Oignons', 'quantity' => '12', 'unit' => 'pièces'],
                ['name' => 'Ail', 'quantity' => '4', 'unit' => 'gousses'],
                ['name' => 'Beurre', 'quantity' => '50', 'unit' => 'g'],
                ['name' => 'Farine', 'quantity' => '2', 'unit' => 'c. à soupe'],
                ['name' => 'Thym', 'quantity' => '1', 'unit' => 'bouquet'],
                ['name' => 'Laurier', 'quantity' => '2', 'unit' => 'feuilles'],
            ],
            'steps' => [
                'Marinez le poulet coupé dans le vin rouge avec les herbes pendant au moins 2 heures.',
                'Faites revenir les lardons dans une poêle sans huile jusqu\'à ce qu\'ils soient croustillants. Réservez.',
                'Dans une cocotte, faites dorer les morceaux de poulet dans le beurre. Réservez.',
                'Faites revenir les oignons grelots pelés et l\'hachis d\'ail dans la même cocotte.',
                'Ajoutez la farine et remuez pendant 1 minute. Versez le vin de marinade et le bouillon.',
                'Replacez le poulet, les lardons et les champignons. Couvrez et laissez mijoter 1h30 à feu doux.',
                'Rectifiez l\'assaisonnement et servez avec des pommes de terre vapeur ou des nouilles.',
            ],
        ];
    }

    private function ratatouille(): array
    {
        return [
            'title' => 'Ratatouille',
            'description' => 'Un plat provençal traditionnel à base de légumes mijotés. Couleurs et saveurs du Sud de la France dans un plat généreux et parfumé.',
            'prep_time' => 20,
            'cook_time' => 45,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal', 'Végétarien'],
            'ingredients' => [
                ['name' => 'Aubergines', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Courgettes', 'quantity' => '3', 'unit' => 'pièces'],
                ['name' => 'Poivrons', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Tomates', 'quantity' => '6', 'unit' => 'pièces'],
                ['name' => 'Oignons', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Ail', 'quantity' => '3', 'unit' => 'gousses'],
                ['name' => 'Basilic', 'quantity' => '1', 'unit' => 'botte'],
                ['name' => 'Huile d\'olive', 'quantity' => '6', 'unit' => 'c. à soupe'],
            ],
            'steps' => [
                'Coupez les aubergines, les courgettes et les poivrons en rondles. Émincez les oignons.',
                'Faites revenir les oignons dans l\'huile d\'olive jusqu\'à ce qu\'ils soient translucides.',
                'Ajoutez les aubergines et les poivrons. Laissez cuire 10 minutes à feu moyen.',
                'Incorporez les tomates concassées et l\'hachis d\'ail. Salez, poivrez.',
                'Ajoutez les courgettes et le basilic ciselé. Couvrez et laissez mijoter 30 minutes.',
                'Servez chaud ou froid, accompagné de riz ou de pain maison.',
            ],
        ];
    }

    private function tarteTatin(): array
    {
        return [
            'title' => 'Tarte Tatin',
            'description' => 'La tarte Tatin est un dessert inversé aux pommes caramélisées, cuit au four puis retournée. Un classique incontournable de la pâtisserie française.',
            'prep_time' => 20,
            'cook_time' => 45,
            'servings' => 8,
            'difficulty' => 'Moyen',
            'categories' => ['Dessert', 'Pâtisseries'],
            'ingredients' => [
                ['name' => 'Pommes', 'quantity' => '8', 'unit' => 'pièces'],
                ['name' => 'Beurre', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Sucre', 'quantity' => '150', 'unit' => 'g'],
                ['name' => 'Farine', 'quantity' => '250', 'unit' => 'g'],
                ['name' => 'Cannelle', 'quantity' => '1', 'unit' => 'c. à café'],
                ['name' => 'Citron', 'quantity' => '1', 'unit' => 'jus'],
            ],
            'steps' => [
                'Pelez et coupez les pommes en quartiers. Arrosez-les de jus de citron.',
                'Dans une poêle, faites fondre le beurre avec le sucre et la cannelle jusqu\'à obtenir un caramel ambré.',
                'Répartissez le caramel dans un moule à tarte. Disposez les pommes bien serrées.',
                'Fournez 10 minutes à 200°C pour faire dorer les pommes.',
                'Couvrez de pâte feuilletée, trouez le fond et enfournez 25 minutes.',
                'Laissez tiédir 5 minutes, passez une lame de couteau autour et retournez délicatement sur un plat.',
            ],
        ];
    }

    private function bouillabaisse(): array
    {
        return [
            'title' => 'Bouillabaisse',
            'description' => 'La bouillabaisse est une soupe de poissons traditionnelle de Marseille, parfumée au safran et servie avec des croûtons et de la rouille.',
            'prep_time' => 30,
            'cook_time' => 60,
            'servings' => 6,
            'difficulty' => 'Difficile',
            'categories' => ['Plat principal', 'Soupes', 'Plats de résistance'],
            'ingredients' => [
                ['name' => 'Saumon', 'quantity' => '500', 'unit' => 'g'],
                ['name' => 'Crevettes', 'quantity' => '300', 'unit' => 'g'],
                ['name' => 'Thon', 'quantity' => '400', 'unit' => 'g'],
                ['name' => 'Oignons', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Ail', 'quantity' => '4', 'unit' => 'gousses'],
                ['name' => 'Tomates', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Vin blanc', 'quantity' => '20', 'unit' => 'cl'],
                ['name' => 'Bouillon de volaille', 'quantity' => '1', 'unit' => 'litre'],
                ['name' => 'Safran', 'quantity' => '1', 'unit' => 'pincée'],
                ['name' => 'Huile d\'olive', 'quantity' => '4', 'unit' => 'c. à soupe'],
                ['name' => 'Laurier', 'quantity' => '2', 'unit' => 'feuilles'],
                ['name' => 'Thym', 'quantity' => '1', 'unit' => 'bouquet'],
            ],
            'steps' => [
                'Coupez les poissons en morceaux. Émincez les oignons et l\'ail.',
                'Dans une grande marmite, faites revenir les oignons et l\'ail dans l\'huile d\'olive.',
                'Ajoutez les tomates concassées, le vin blanc, le bouillon, le safran, le laurier et le thym.',
                'Portez à ébullition puis ajoutez les morceaux de poisson les plus fermes (thon).',
                'Après 10 minutes, ajoutez le saumon et les crevettes. Laissez cuire 15 minutes.',
                'Servez dans des bols avec des croûtons de pain grillé et de la rouille.',
            ],
        ];
    }

    private function boeufBourguignon(): array
    {
        return [
            'title' => 'Boeuf Bourguignon',
            'description' => 'Un plat mijoté emblématique de Bourgogne : des morceaux de bœuf fondants dans un jus de vin rouge riche, accompagnés de champignons et d\'oignons grelots.',
            'prep_time' => 30,
            'cook_time' => 180,
            'servings' => 6,
            'difficulty' => 'Moyen',
            'categories' => ['Plat principal', 'Plats de résistance'],
            'ingredients' => [
                ['name' => 'Bœuf', 'quantity' => '1', 'unit' => 'kg'],
                ['name' => 'Vin rouge', 'quantity' => '75', 'unit' => 'cl'],
                ['name' => 'Lardons', 'quantity' => '200', 'unit' => 'g'],
                ['name' => 'Champignons', 'quantity' => '300', 'unit' => 'g'],
                ['name' => 'Oignons', 'quantity' => '8', 'unit' => 'pièces'],
                ['name' => 'Ail', 'quantity' => '3', 'unit' => 'gousses'],
                ['name' => 'Carottes', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Farine', 'quantity' => '3', 'unit' => 'c. à soupe'],
                ['name' => 'Bouillon de volaille', 'quantity' => '30', 'unit' => 'cl'],
                ['name' => 'Thym', 'quantity' => '1', 'unit' => 'bouquet'],
                ['name' => 'Laurier', 'quantity' => '2', 'unit' => 'feuilles'],
            ],
            'steps' => [
                'Coupez le bœuf en gros cubes. Salez et poivrez.',
                'Faites dorer la viande dans l\'huile d\'olive par petits lots. Réservez.',
                'Faites revenir les lardons, puis les oignons et l\'ail.',
                'Ajoutez la farine et remuez 1 minute. Versez le vin rouge et le bouillon.',
                'Replacez la viande, ajoutez les carottes coupées, le thym et le laurier.',
                'Couvrez et laissez mijoter à feu doux pendant 2h30. Ajoutez les champignons à la fin.',
                'Servez avec des pommes de terre vapeur ou des pâtes.',
            ],
        ];
    }

    private function quicheLorraine(): array
    {
        return [
            'title' => 'Quiche Lorraine',
            'description' => 'Une tarte salée garnie d\'une appétissante crème aux œufs avec des lardons croustillants. Parfaite pour un déjeuner ou un dîner léger.',
            'prep_time' => 20,
            'cook_time' => 40,
            'servings' => 6,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal', 'Entrée'],
            'ingredients' => [
                ['name' => 'Farine', 'quantity' => '250', 'unit' => 'g'],
                ['name' => 'Beurre', 'quantity' => '125', 'unit' => 'g'],
                ['name' => 'Lardons', 'quantity' => '200', 'unit' => 'g'],
                ['name' => 'Œufs', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Crème fraîche', 'quantity' => '20', 'unit' => 'cl'],
                ['name' => 'Lait', 'quantity' => '10', 'unit' => 'cl'],
                ['name' => 'Fromage râpé', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Muscade', 'quantity' => '1', 'unit' => 'pincée'],
            ],
            'steps' => [
                'Préchauffez le four à 180°C. Préparez la pâte brisée avec la farine, le beurre et un peu d\'eau.',
                'Étalez la pâte dans un moule à tarte et piquez le fond.',
                'Faites cuire les lardons dans une poêle jusqu\'à ce qu\'ils soient croustillants.',
                'Dans un bol, fouettez les œufs avec la crème fraîche, le lait et la muscade.',
                'Répartissez les lardons sur le fond de tarte. Versez la préparation aux œufs.',
                'Parsemez de fromage râpé et enfournez 35-40 minutes jusqu\'à ce que le dessus soit doré.',
            ],
        ];
    }

    private function cremeBrulee(): array
    {
        return [
            'title' => 'Crème Brûlée',
            'description' => 'Une crème dessert onctueuse à la vanille, surmontée d\'une fine couche de caramel croustillant que l\'on brûle au chalumeau.',
            'prep_time' => 15,
            'cook_time' => 50,
            'servings' => 6,
            'difficulty' => 'Moyen',
            'categories' => ['Dessert', 'Pâtisseries'],
            'ingredients' => [
                ['name' => 'Crème fraîche', 'quantity' => '50', 'unit' => 'cl'],
                ['name' => 'Lait', 'quantity' => '20', 'unit' => 'cl'],
                ['name' => 'Œufs', 'quantity' => '6', 'unit' => 'pièces'],
                ['name' => 'Sucre', 'quantity' => '100', 'unit' => 'g'],
            ],
            'steps' => [
                'Préchauffez le four à 150°C. Faites chauffer la crème et le lait sans bouillir.',
                'Dans un bol, fouettez les jaunes d\'œufs avec le sucre jusqu\'à ce que le mélange blanchisse.',
                'Versez la crème chaude en filet sur les jaunes en fouettant constamment.',
                'Versez dans des ramequins et cuisez au bain-marie 45-50 minutes.',
                'Laissez refroidir puis réfrigérez au moins 2 heures.',
                'Au moment de servir, saupoudrez de sucre et caramelisez au chalumeau.',
            ],
        ];
    }

    private function soupeAOignon(): array
    {
        return [
            'title' => 'Soupe à l\'Oignon',
            'description' => 'Une soupe française traditionnelle à base d\'oignons caramélisés, gratinée de fromage et servie avec des croûtons de pain.',
            'prep_time' => 15,
            'cook_time' => 60,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Soupes', 'Entrée'],
            'ingredients' => [
                ['name' => 'Oignons', 'quantity' => '6', 'unit' => 'pièces'],
                ['name' => 'Beurre', 'quantity' => '50', 'unit' => 'g'],
                ['name' => 'Farine', 'quantity' => '2', 'unit' => 'c. à soupe'],
                ['name' => 'Bouillon de volaille', 'quantity' => '1', 'unit' => 'litre'],
                ['name' => 'Vin blanc', 'quantity' => '10', 'unit' => 'cl'],
                ['name' => 'Fromage râpé', 'quantity' => '150', 'unit' => 'g'],
                ['name' => 'Pain de mie', 'quantity' => '8', 'unit' => 'tranches'],
                ['name' => 'Thym', 'quantity' => '1', 'unit' => 'c. à café'],
            ],
            'steps' => [
                'Émincez finement les oignons. Faites-les fondre dans le beurre à feu doux pendant 20 minutes.',
                'Augmentez le feu et laissez les oignons caraméliser jusqu\'à obtenir une couleur brune.',
                'Ajoutez la farine et remuez 1 minute. Versez le vin blanc.',
                'Ajoutez le bouillon et le thym. Laissez mijoter 30 minutes.',
                'Versez dans des bols. Placez des croûtons de pain grillé et parsemez de fromage.',
                'Passez sous le gril du four jusqu\'à ce que le fromage soit fondu et doré.',
            ],
        ];
    }

    private function pouletRôti(): array
    {
        return [
            'title' => 'Poulet Rôti aux Herbes',
            'description' => 'Un poulet entier rôti au four avec un mélange d\'herbes fraîches, citron et beurre, pour une peau croustillante et une chair juteuse.',
            'prep_time' => 15,
            'cook_time' => 75,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal'],
            'ingredients' => [
                ['name' => 'Poulet', 'quantity' => '1', 'unit' => 'poulet entier'],
                ['name' => 'Beurre', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Citron', 'quantity' => '1', 'unit' => 'pièce'],
                ['name' => 'Ail', 'quantity' => '4', 'unit' => 'gousses'],
                ['name' => 'Thym', 'quantity' => '1', 'unit' => 'bouquet'],
                ['name' => 'Romarin', 'quantity' => '1', 'unit' => 'branche'],
                ['name' => 'Persil', 'quantity' => '1', 'unit' => 'botte'],
                ['name' => 'Huile d\'olive', 'quantity' => '2', 'unit' => 'c. à soupe'],
            ],
            'steps' => [
                'Préchauffez le four à 200°C. Rincez et séchez le poulet.',
                'Mélangez le beurre mou avec les herbes hachées, l\'ail écrasé et le jus de citron.',
                'Soulevez la peau du poulet et répartissez le beurre aux herbes dessous.',
                'Frottez le poulet avec l\'huile d\'olive et posez le citron vidé à l\'intérieur.',
                'Enfournez et laissez rôti 1h15 en arrosant régulièrement.',
                'Laissez reposer 10 minutes avant de détailler et servir.',
            ],
        ];
    }

    private function saladeNicoise(): array
    {
        return [
            'title' => 'Salade Niçoise',
            'description' => 'Une salade composée complète et colorée de Nice, avec thon, légumes crus, œufs durs et olives, assaisonnée d\'une vinaigrette à l\'huile d\'olive.',
            'prep_time' => 20,
            'cook_time' => 10,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Salades', 'Rapide'],
            'ingredients' => [
                ['name' => 'Thon', 'quantity' => '400', 'unit' => 'g'],
                ['name' => 'Pommes de terre', 'quantity' => '400', 'unit' => 'g'],
                ['name' => 'Haricots verts', 'quantity' => '200', 'unit' => 'g'],
                ['name' => 'Œufs', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Tomates', 'quantity' => '3', 'unit' => 'pièces'],
                ['name' => 'Concombre', 'quantity' => '1', 'unit' => 'pièce'],
                ['name' => 'Citron', 'quantity' => '1', 'unit' => 'jus'],
                ['name' => 'Huile d\'olive', 'quantity' => '4', 'unit' => 'c. à soupe'],
            ],
            'steps' => [
                'Faites cuire les pommes de terre et les œufs dans de l\'eau bouillante salée.',
                'Passez les haricots verts à l\'eau bouillante salée pendant 3 minutes, puis rincez à l\'eau froide.',
                'Coupez les tomates en quartiers et le concombre en rondelles.',
                'Disposez la salade sur un plat, entourez de pommes de terre chaudes et de haricots verts.',
                'Placez le thon au centre et les œufs coupés en quartiers.',
                'Assaisonnez avec le jus de citron, l\'huile d\'olive, sel et poivre.',
            ],
        ];
    }

    private function magretDeCanard(): array
    {
        return [
            'title' => 'Magret de Canard',
            'description' => 'Des filets de canard poêlés, nappés d\'une sauce au miel et au gingembre, accompagnés de légumes de saison.',
            'prep_time' => 15,
            'cook_time' => 20,
            'servings' => 4,
            'difficulty' => 'Moyen',
            'categories' => ['Plat principal'],
            'ingredients' => [
                ['name' => 'Porc', 'quantity' => '4', 'unit' => 'magrets'],
                ['name' => 'Miel', 'quantity' => '3', 'unit' => 'c. à soupe'],
                ['name' => 'Sauce soja', 'quantity' => '2', 'unit' => 'c. à soupe'],
                ['name' => 'Gingembre', 'quantity' => '1', 'unit' => 'c. à café'],
                ['name' => 'Ail', 'quantity' => '2', 'unit' => 'gousses'],
                ['name' => 'Carottes', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Courgettes', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Huile d\'olive', 'quantity' => '2', 'unit' => 'c. à soupe'],
            ],
            'steps' => [
                'Grattez la peau du magret en croisillons. Salez et poivrez.',
                'Faites cuire le magret peau vers le fond dans une poêle chaude pendant 6 minutes.',
                'Retournez et cuisez 4 minutes de plus. Réservez et laissez reposer.',
                'Dans la même poêle, versez le miel, la sauce soja, le gingembre et l\'ail haché.',
                'Laissez réduire 2-3 minutes jusqu\'à obtenir une sauce sirupeuse.',
                'Coupez le magret en fines tranches, nappez de sauce et servez avec les légumes sautés.',
            ],
        ];
    }

    private function gratinDauphinois(): array
    {
        return [
            'title' => 'Gratin Dauphinois',
            'description' => 'Un plat régional à base de pommes de terre fines, cuites au lait et à la crème, gratinés au four jusqu\'à obtenir une croûte dorée.',
            'prep_time' => 20,
            'cook_time' => 60,
            'servings' => 6,
            'difficulty' => 'Facile',
            'categories' => ['Accompagnements', 'Plat principal'],
            'ingredients' => [
                ['name' => 'Pommes de terre', 'quantity' => '1', 'unit' => 'kg'],
                ['name' => 'Lait', 'quantity' => '50', 'unit' => 'cl'],
                ['name' => 'Crème fraîche', 'quantity' => '20', 'unit' => 'cl'],
                ['name' => 'Ail', 'quantity' => '2', 'unit' => 'gousses'],
                ['name' => 'Fromage râpé', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Muscade', 'quantity' => '1', 'unit' => 'pincée'],
                ['name' => 'Beurre', 'quantity' => '30', 'unit' => 'g'],
            ],
            'steps' => [
                'Préchauffez le four à 180°C. Épluchez et coupez les pommes de terre en fines rondles.',
                'Frottez un plat à gratin avec l\'ail coupé en deux et beurrez-le.',
                'Disposez les couches de pommes de terre dans le plat.',
                'Mélangez le lait, la crème, la muscade et versez sur les pommes de terre.',
                'Parsemez de fromage râpé et de noisettes de beurre.',
                'Enfournez 55-60 minutes jusqu\'à ce que le dessus soit bien doré.',
            ],
        ];
    }

    private function mousseAuChocolat(): array
    {
        return [
            'title' => 'Mousse au Chocolat',
            'description' => 'Une mousse au chocolat légère et aérienne, préparée avec des œufs frais et du chocolat noir fondant. Un dessert classique et généreux.',
            'prep_time' => 20,
            'cook_time' => 0,
            'servings' => 6,
            'difficulty' => 'Moyen',
            'categories' => ['Dessert'],
            'ingredients' => [
                ['name' => 'Œufs', 'quantity' => '6', 'unit' => 'pièces'],
                ['name' => 'Sucre', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Beurre', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Chocolat noir', 'quantity' => '200', 'unit' => 'g'],
            ],
            'steps' => [
                'Faites fondre le chocolat et le beurre au bain-marie. Laissez tiédir.',
                'Séparez les blancs des jaunes d\'œufs.',
                'Incorporez les jaunes d\'œufs un à un au mélange chocolat fondu.',
                'Montez les blancs en neige ferme avec une pincée de sel, puis ajoutez le sucre.',
                'Incorporez délicatement les blancs en neige au mélange chocolat.',
                'Répartissez dans des verrines et réfrigérez au moins 4 heures.',
            ],
        ];
    }

    private function couscousAuPoulet(): array
    {
        return [
            'title' => 'Couscous au Poulet',
            'description' => 'Un plat généreux à base de semoule, poulet épicé et légumes fondants, aromatisé au cumin et au paprika. Un grand classique du dimanche.',
            'prep_time' => 20,
            'cook_time' => 60,
            'servings' => 6,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal'],
            'ingredients' => [
                ['name' => 'Poulet', 'quantity' => '1', 'unit' => 'poulet entier'],
                ['name' => 'Riz', 'quantity' => '400', 'unit' => 'g'],
                ['name' => 'Carottes', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Courgettes', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Navets', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Pois chiches', 'quantity' => '400', 'unit' => 'g'],
                ['name' => 'Cumin', 'quantity' => '2', 'unit' => 'c. à café'],
                ['name' => 'Paprika', 'quantity' => '1', 'unit' => 'c. à café'],
                ['name' => 'Safran', 'quantity' => '1', 'unit' => 'pincée'],
                ['name' => 'Bouillon de volaille', 'quantity' => '1', 'unit' => 'litre'],
            ],
            'steps' => [
                'Coupez le poulet en morceaux. Faites-le dorer dans une grande marmite.',
                'Ajoutez les oignons hachés et l\'ail. Laissez revenir 2 minutes.',
                'Ajoutez les épices (cumin, paprika, safran) et remuez.',
                'Versez le bouillon, les légumes en morceaux et les pois chiches égouttés.',
                'Laissez mijoter 45 minutes à feu doux.',
                'Pendant ce temps, faites cuire la semoule selon les instructions. Servez le bouillon par-dessus.',
            ],
        ];
    }

    private function tarteAuCitronMeringuee(): array
    {
        return [
            'title' => 'Tarte au Citron Meringuée',
            'description' => 'Une tarte au citron crémeuse et acidulée, surmontée d\'une meringue italienne caramélisée au chalumeau. Un dessert élégant et rafraîchissant.',
            'prep_time' => 30,
            'cook_time' => 30,
            'servings' => 8,
            'difficulty' => 'Difficile',
            'categories' => ['Dessert', 'Pâtisseries'],
            'ingredients' => [
                ['name' => 'Farine', 'quantity' => '250', 'unit' => 'g'],
                ['name' => 'Beurre', 'quantity' => '125', 'unit' => 'g'],
                ['name' => 'Citron', 'quantity' => '6', 'unit' => 'jus'],
                ['name' => 'Œufs', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Sucre', 'quantity' => '200', 'unit' => 'g'],
            ],
            'steps' => [
                'Préchauffez le four à 180°C. Préparez la pâte sucrée et étalez-la dans un moule.',
                'Faites cuire à blanc 15 minutes avec des billes de cuisson.',
                'Préparez la crème au citron : faites chauffer le jus avec le sucre, puis ajoutez les œufs fouettés et le beurre.',
                'Versez la crème sur le fond de tarte et laissez prendre au réfrigérateur.',
                'Montez la meringue italienne avec les blancs d\'œufs et le sirop de sucre.',
                'Garnissez la tarte de meringue et caramélisez au chalumeau.',
            ],
        ];
    }

    private function steakFrites(): array
    {
        return [
            'title' => 'Steak Frites',
            'description' => 'Le classique bistrot : un steak saignant ou à point, accompagné de frites maison croustillantes et d\'une sauce au choix.',
            'prep_time' => 15,
            'cook_time' => 20,
            'servings' => 2,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal', 'Rapide'],
            'ingredients' => [
                ['name' => 'Bœuf', 'quantity' => '2', 'unit' => 'steaks'],
                ['name' => 'Pommes de terre', 'quantity' => '800', 'unit' => 'g'],
                ['name' => 'Beurre', 'quantity' => '50', 'unit' => 'g'],
                ['name' => 'Ail', 'quantity' => '2', 'unit' => 'gousses'],
                ['name' => 'Thym', 'quantity' => '1', 'unit' => 'branche'],
                ['name' => 'Huile d\'olive', 'quantity' => '1', 'unit' => 'litre'],
            ],
            'steps' => [
                'Épluchez et coupez les pommes de terre en bâtonnets. Rincez et séchez-les.',
                'Faites frire les frites dans l\'huile chaude (170°C) jusqu\'à ce qu\'elles soient dorées.',
                'Égouttez sur du papier absorbant et salez immédiatement.',
                'Dans une poêle très chaude, faites cuire les steaks 2-3 minutes de chaque côté.',
                'Ajoutez le beurre, l\'ail et le thym, et arrosez les steaks.',
                'Servez immédiatement avec les frites.',
            ],
        ];
    }

    private function patesCarbonara(): array
    {
        return [
            'title' => 'Pâtes Carbonara',
            'description' => 'Un plat italien devenu un classique en France : des spaghettis crémeux aux lardons, œufs et parmesan, sans crème.',
            'prep_time' => 10,
            'cook_time' => 15,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal', 'Rapide'],
            'ingredients' => [
                ['name' => 'Pâtes', 'quantity' => '400', 'unit' => 'g'],
                ['name' => 'Lardons', 'quantity' => '200', 'unit' => 'g'],
                ['name' => 'Œufs', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Parmesan', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Poivre', 'quantity' => '2', 'unit' => 'c. à café'],
            ],
            'steps' => [
                'Faites cuire les pâtes dans de l\'eau bouillante salée selon les instructions du paquet.',
                'Pendant ce temps, faites revenir les lardons dans une poêle sans huile.',
                'Dans un bol, fouettez les œufs avec le parmesan râpé et le poivre.',
                'Égouttez les pâtes en réservant une louche d\'eau de cuisson.',
                'Ajoutez les pâtes chaudes aux lardons, hors du feu. Incorporez le mélange œufs.',
                'Mélangez vivement en ajoutant un peu d\'eau de cuisson si nécessaire. Servez immédiatement.',
            ],
        ];
    }

    private function pouletBasquaise(): array
    {
        return [
            'title' => 'Poulet Basquaise',
            'description' => 'Un plat du Sud-Ouest à base de poulet mijoté avec des poivrons, des tomates et du piment d\'Espelette. Saveurs ensoleillées.',
            'prep_time' => 20,
            'cook_time' => 45,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal'],
            'ingredients' => [
                ['name' => 'Poulet', 'quantity' => '1', 'unit' => 'poulet entier'],
                ['name' => 'Poivrons', 'quantity' => '3', 'unit' => 'pièces'],
                ['name' => 'Tomates', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Oignons', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Ail', 'quantity' => '3', 'unit' => 'gousses'],
                ['name' => 'Paprika', 'quantity' => '2', 'unit' => 'c. à café'],
                ['name' => 'Huile d\'olive', 'quantity' => '4', 'unit' => 'c. à soupe'],
            ],
            'steps' => [
                'Coupez le poulet en morceaux. Faites-le dorer dans l\'huile d\'olive.',
                'Ajoutez les oignons émincés et l\'ail. Laissez revenir.',
                'Ajoutez les poivrons coupés en lanières et les tomates concassées.',
                'Parsemez de paprika et mélangez bien.',
                'Couvrez et laissez mijoter 35-40 minutes à feu doux.',
                'Servez avec du riz basque ou des pommes de terre vapeur.',
            ],
        ];
    }

    private function tarteAuFromage(): array
    {
        return [
            'title' => 'Tarte au Fromage',
            'description' => 'Une tarte salée crémeuse et généreuse aux trois fromages, avec une pointe de moutarde. Parfaite pour un repas en famille.',
            'prep_time' => 15,
            'cook_time' => 35,
            'servings' => 6,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal', 'Entrée'],
            'ingredients' => [
                ['name' => 'Farine', 'quantity' => '250', 'unit' => 'g'],
                ['name' => 'Beurre', 'quantity' => '125', 'unit' => 'g'],
                ['name' => 'Œufs', 'quantity' => '3', 'unit' => 'pièces'],
                ['name' => 'Crème fraîche', 'quantity' => '20', 'unit' => 'cl'],
                ['name' => 'Parmesan', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Mozzarella', 'quantity' => '150', 'unit' => 'g'],
                ['name' => 'Moutarde', 'quantity' => '1', 'unit' => 'c. à soupe'],
            ],
            'steps' => [
                'Préchauffez le four à 180°C. Préparez la pâte brisée et étalez-la dans un moule.',
                'Étalez la moutarde sur le fond de tarte.',
                'Mélangez les œufs avec la crème fraîche. Versez sur la moutarde.',
                'Répartissez les fromages râpés et la mozzarella coupée.',
                'Enfournez 30-35 minutes jusqu\'à ce que le dessus soit doré.',
                'Laissez tiédir 5 minutes avant de servir.',
            ],
        ];
    }

    private function chiliConCarne(): array
    {
        return [
            'title' => 'Chili Con Carne',
            'description' => 'Un plat épicé mexicain à base de bœuf haché, haricots rouges et tomates, relevé au cumin et au piment. Convivial et réconfortant.',
            'prep_time' => 15,
            'cook_time' => 45,
            'servings' => 6,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal'],
            'ingredients' => [
                ['name' => 'Bœuf', 'quantity' => '500', 'unit' => 'g'],
                ['name' => 'Tomates', 'quantity' => '6', 'unit' => 'pièces'],
                ['name' => 'Oignons', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Ail', 'quantity' => '3', 'unit' => 'gousses'],
                ['name' => 'Cumin', 'quantity' => '2', 'unit' => 'c. à café'],
                ['name' => 'Paprika', 'quantity' => '1', 'unit' => 'c. à café'],
                ['name' => 'Pois chiches', 'quantity' => '400', 'unit' => 'g'],
                ['name' => 'Huile d\'olive', 'quantity' => '2', 'unit' => 'c. à soupe'],
            ],
            'steps' => [
                'Faites revenir les oignons et l\'ail dans l\'huile d\'olive.',
                'Ajoutez le bœuf haché et faites-le dorer.',
                'Incorporez les épices (cumin, paprika) et remuez.',
                'Ajoutez les tomates concassées et les pois chiches égouttés.',
                'Laissez mijoter 35-40 minutes à feu doux en remuant régulièrement.',
                'Servez avec du riz, des tortillas ou du pain.',
            ],
        ];
    }

    private function potAuFeu(): array
    {
        return [
            'title' => 'Pot-au-Feu',
            'description' => 'Un plat traditionnel français à base de viande de bœuf mijotée avec des légumes racines. Un classique de la cuisine familiale.',
            'prep_time' => 20,
            'cook_time' => 180,
            'servings' => 6,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal', 'Plats de résistance'],
            'ingredients' => [
                ['name' => 'Bœuf', 'quantity' => '1', 'unit' => 'kg'],
                ['name' => 'Carottes', 'quantity' => '4', 'unit' => 'pièces'],
                ['name' => 'Pommes de terre', 'quantity' => '6', 'unit' => 'pièces'],
                ['name' => 'Poireaux', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Céleri', 'quantity' => '2', 'unit' => 'branches'],
                ['name' => 'Oignons', 'quantity' => '2', 'unit' => 'pièces'],
                ['name' => 'Laurier', 'quantity' => '2', 'unit' => 'feuilles'],
                ['name' => 'Thym', 'quantity' => '1', 'unit' => 'bouquet'],
            ],
            'steps' => [
                'Dans une grande marmite, faites chauffer l\'eau et ajoutez la viande.',
                'Portez à ébullition puis retirez l\'écume.',
                'Ajoutez les oignons piqués de clous de girofle, le laurier et le thym.',
                'Laissez mijoter à feu doux pendant 2h30.',
                'Ajoutez les légumes coupés en morceaux et poursuivez la cuisson 30 minutes.',
                'Servez la viande et les légumes avec la bouillon, en accompagnant de cornichons et de moutarde.',
            ],
        ];
    }

    private function saladeCesar(): array
    {
        return [
            'title' => 'Salade César',
            'description' => 'Une salade croquante avec poulet grillé, croûtons parmesan et sauce césar maison. Un classique américain devenu universel.',
            'prep_time' => 20,
            'cook_time' => 15,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Salades', 'Rapide'],
            'ingredients' => [
                ['name' => 'Poulet', 'quantity' => '2', 'unit' => 'filets'],
                ['name' => 'Parmesan', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Pain de mie', 'quantity' => '4', 'unit' => 'tranches'],
                ['name' => 'Citron', 'quantity' => '1', 'unit' => 'jus'],
                ['name' => 'Ail', 'quantity' => '2', 'unit' => 'gousses'],
                ['name' => 'Huile d\'olive', 'quantity' => '4', 'unit' => 'c. à soupe'],
                ['name' => 'Moutarde', 'quantity' => '1', 'unit' => 'c. à café'],
            ],
            'steps' => [
                'Grillez les filets de poulet avec un peu d\'huile d\'olive. Laissez reposer puis coupez en tranches.',
                'Préparez les croûtons : coupez le pain en cubes, mélangez avec l\'huile et l\'ail, faites dorer au four.',
                'Préparez la sauce : mélangez le jus de citron, l\'huile, la moutarde et le parmesan râpé.',
                'Lavez et émiettez la laitue romaine dans un saladier.',
                'Ajoutez le poulet, les croûtons et nappez de sauce.',
                'Parsemez de parmesan en copeaux et servez immédiatement.',
            ],
        ];
    }

    private function clafoutisAuxCerises(): array
    {
        return [
            'title' => 'Clafoutis aux Cerises',
            'description' => 'Un dessert du Limousin à base de pâte fluide aux cerises. Simple et délicieux, chaud ou froid.',
            'prep_time' => 15,
            'cook_time' => 35,
            'servings' => 8,
            'difficulty' => 'Facile',
            'categories' => ['Dessert'],
            'ingredients' => [
                ['name' => 'Cerises', 'quantity' => '500', 'unit' => 'g'],
                ['name' => 'Œufs', 'quantity' => '3', 'unit' => 'pièces'],
                ['name' => 'Sucre', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Farine', 'quantity' => '100', 'unit' => 'g'],
                ['name' => 'Lait', 'quantity' => '30', 'unit' => 'cl'],
                ['name' => 'Beurre', 'quantity' => '30', 'unit' => 'g'],
                ['name' => 'Cannelle', 'quantity' => '1', 'unit' => 'c. à café'],
            ],
            'steps' => [
                'Préchauffez le four à 180°C. Beurrsez un plat à gratin.',
                'Équeutez les cerises et répartissez-les dans le plat.',
                'Dans un bol, fouettez les œufs avec le sucre jusqu\'à ce que le mélange blanchisse.',
                'Ajoutez la farine tamisée, le lait et la cannelle. Mélangez bien.',
                'Versez la pâte sur les cerises.',
                'Enfournez 30-35 minutes jusqu\'à ce que le clafoutis soit gonflé et doré.',
            ],
        ];
    }

    private function risottoAuxChampignons(): array
    {
        return [
            'title' => 'Risotto aux Champignons',
            'description' => 'Un risotto crémeux et parfumé aux champignons de Paris, avec du parmesan et du beurre pour une texture onctueuse.',
            'prep_time' => 10,
            'cook_time' => 30,
            'servings' => 4,
            'difficulty' => 'Moyen',
            'categories' => ['Plat principal', 'Végétarien'],
            'ingredients' => [
                ['name' => 'Riz', 'quantity' => '300', 'unit' => 'g'],
                ['name' => 'Champignons', 'quantity' => '300', 'unit' => 'g'],
                ['name' => 'Oignons', 'quantity' => '1', 'unit' => 'pièce'],
                ['name' => 'Vin blanc', 'quantity' => '10', 'unit' => 'cl'],
                ['name' => 'Bouillon de volaille', 'quantity' => '1', 'unit' => 'litre'],
                ['name' => 'Parmesan', 'quantity' => '80', 'unit' => 'g'],
                ['name' => 'Beurre', 'quantity' => '50', 'unit' => 'g'],
                ['name' => 'Ail', 'quantity' => '2', 'unit' => 'gousses'],
            ],
            'steps' => [
                'Faites chauffer le bouillon dans une casserole et gardez-le chaud.',
                'Dans une poêle, faites revenir les oignons et l\'ail dans le beurre.',
                'Ajoutez les champignons émincés et faites-les dorer.',
                'Ajoutez le riz et torréifiez-le pendant 2 minutes en remuant.',
                'Versez le vin blanc et laissez s\'évaporer. Ajoutez le bouillon louche par louche en remuant.',
                'Quand le riz est crémeux, retirez du feu, incorporez le parmesan et un peu de beurre.',
            ],
        ];
    }

    private function tartareSaumon(): array
    {
        return [
            'title' => 'Tartare de Saumon',
            'description' => 'Un tartare de saumon frais et délicat, assaisonné au citron, à l\'huile d\'olive et aux herbes fraîches. Un entrée légère et élégante.',
            'prep_time' => 20,
            'cook_time' => 0,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Entrée', 'Entrées froides', 'Rapide'],
            'ingredients' => [
                ['name' => 'Saumon', 'quantity' => '400', 'unit' => 'g'],
                ['name' => 'Citron', 'quantity' => '1', 'unit' => 'jus'],
                ['name' => 'Huile d\'olive', 'quantity' => '3', 'unit' => 'c. à soupe'],
                ['name' => 'Ciboulette', 'quantity' => '1', 'unit' => 'botte'],
                ['name' => 'Ail', 'quantity' => '1', 'unit' => 'gousse'],
                ['name' => 'Sauce soja', 'quantity' => '1', 'unit' => 'c. à soupe'],
            ],
            'steps' => [
                'Coupez le saumon frais en petits cubes réguliers.',
                'Dans un bol, mélangez le jus de citron, l\'huile d\'olive et la sauce soja.',
                'Ajoutez l\'ail finement haché et la ciboulette ciselée.',
                'Incorporez délicatement le saumon au mélange.',
                'Assaisonnez selon votre goût et réfrigérez 30 minutes.',
                'Servez sur des toasts ou avec une salade verte.',
            ],
        ];
    }

    private function saumonGlaceAuMielEtCarottesRoties(): array
    {
        return [
            'title' => 'Saumon Glacé au Miel et Carottes Rôties',
            'description' => 'Des pavés de saumon glacés au miel et à la sauce soja, servis avec des carottes rôties au four. Un plat équilibré, sucré-salé et rapide à préparer.',
            'prep_time' => 15,
            'cook_time' => 25,
            'servings' => 4,
            'difficulty' => 'Facile',
            'categories' => ['Plat principal', 'Rapide'],
            'ingredients' => [
                ['name' => 'Saumon', 'quantity' => '4', 'unit' => 'pavés'],
                ['name' => 'Miel', 'quantity' => '3', 'unit' => 'c. à soupe'],
                ['name' => 'Sauce soja', 'quantity' => '3', 'unit' => 'c. à soupe'],
                ['name' => 'Gingembre', 'quantity' => '1', 'unit' => 'c. à café'],
                ['name' => 'Carottes', 'quantity' => '500', 'unit' => 'g'],
                ['name' => 'Huile d\'olive', 'quantity' => '2', 'unit' => 'c. à soupe'],
                ['name' => 'Citron', 'quantity' => '1', 'unit' => 'jus'],
                ['name' => 'Sel', 'quantity' => '1', 'unit' => 'pincée'],
                ['name' => 'Poivre', 'quantity' => '1', 'unit' => 'pincée'],
            ],
            'steps' => [
                'Préchauffez le four à 200 °C. Épluchez les carottes et coupez-les en bâtonnets.',
                'Mélangez les carottes avec une cuillère à soupe d\'huile d\'olive, salez et poivrez. Disposez-les sur une plaque et enfournez 20 minutes.',
                'Dans un bol, mélangez le miel, la sauce soja, le gingembre râpé et le jus de citron.',
                'Badigeonnez les pavés de saumon avec la moitié de la sauce.',
                'Faites cuire le saumon dans une poêle huilée 3 à 4 minutes par face, en nappant de sauce en fin de cuisson.',
                'Servez les pavés glacés avec les carottes rôties et un filet de sauce.',
            ],
        ];
    }
}
