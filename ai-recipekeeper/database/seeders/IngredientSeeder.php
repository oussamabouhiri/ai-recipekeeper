<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Protéines
            'Poulet', 'Bœuf', 'Porc', 'Agneau', 'Saumon', 'Crevettes', 'Thon',
            'Lardons', 'Jambon', 'Œufs',

            // Légumes
            'Tomates', 'Oignons', 'Ail', 'Carottes', 'Pommes de terre', 'Courgettes',
            'Aubergines', 'Poivrons', 'Champignons', 'Brocoli', 'Haricots verts',
            'Épinards', 'Poireaux', 'Céleri', 'Navets', 'Chou', 'Concombre',
            'Avocat', 'Maïs', 'Petits pois',

            // Herbes et épices
            'Basilic', 'Persil', 'Ciboulette', 'Thym', 'Romarin', 'Laurier',
            'Origan', 'Estragon', 'Menthe', 'Cumin', 'Paprika', 'Curry',
            'Cannelle', 'Gingembre', 'Safran', 'Muscade',

            // Produits laitiers
            'Beurre', 'Crème fraîche', 'Lait', 'Fromage râpé', 'Mozzarella',
            'Parmesan', 'Yaourt nature', 'Crème de lait',

            // Féculents et pain
            'Farine', 'Riz', 'Pâtes', 'Pain de mie', 'Chapelure',

            // Condiments et liquides
            'Huile d\'olive', 'Vinaigre balsamique', 'Sauce soja', 'Vin blanc',
            'Vin rouge', 'Bouillon de volaille', 'Concentré de tomate',
            'Moutarde', 'Ketchup', 'Mayonnaise', 'Sel', 'Poivre',
            'Sucre', 'Cassonade', 'Miel',

            // Fruits
            'Citron', 'Orange', 'Fraises', 'Myrtilles', 'Bananes', 'Pommes',
            'Poires', 'Cerises', 'Raisin',

            // Noix et graines
            'Amandes', 'Noix', 'Noisettes', 'Graines de tournesol', 'Sésame',
        ];

        foreach ($ingredients as $name) {
            Ingredient::firstOrCreate(['name' => $name]);
        }
    }
}
