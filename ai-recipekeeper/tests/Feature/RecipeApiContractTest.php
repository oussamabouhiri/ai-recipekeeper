<?php

namespace Tests\Feature;

use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RecipeApiContractTest extends TestCase
{
    use RefreshDatabase;

    private const RECIPE_FIELDS = [
        'id',
        'title',
        'description',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'image_path',
        'statut',
        'is_ai_generated',
        'user_id',
        'created_at',
        'updated_at',
        'user',
        'etapes',
        'ingredients',
        'categories',
    ];

    public function test_show_response_exposes_exactly_the_mld_fields(): void
    {
        $recipe = Recette::factory()->create();
        $recipe->etapes()->create(['step_number' => 1, 'instruction' => 'Étape']);

        $response = $this->getJson(route('api.recipes.show', $recipe))->assertOk();

        $this->assertSame(
            self::RECIPE_FIELDS,
            array_keys($response->json('data')),
            'Recette response must expose exactly the MLD fields, never `instructions`.'
        );
    }

    public function test_instructions_never_appears_in_listing_or_show_responses(): void
    {
        $recipe = Recette::factory()->create();

        $listing = $this->getJson(route('api.recipes.index'))->assertOk();
        $show = $this->getJson(route('api.recipes.show', $recipe))->assertOk();

        $this->assertKeysAbsent($listing, ['instructions']);
        $this->assertKeysAbsent($show, ['instructions']);
    }

    public function test_instructions_payload_is_ignored_and_not_stored(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'instructions' => 'Ne doit pas être utilisé.',
        ])
            ->assertCreated();

        $this->assertKeysAbsent($response, ['instructions']);
        $this->assertDatabaseHas('recettes', [
            'title' => 'Recette',
            'instructions' => null,
        ]);
    }

    private function assertKeysAbsent(TestResponse $response, array $keys): void
    {
        $stack = [$response->json()];

        while ($stack !== []) {
            $value = array_pop($stack);

            if (! is_array($value)) {
                continue;
            }

            foreach ($value as $key => $child) {
                if (in_array($key, $keys, true)) {
                    $this->fail("Response must not contain the key `{$key}`.");
                }

                $stack[] = $child;
            }
        }

        $this->assertTrue(true);
    }
}
