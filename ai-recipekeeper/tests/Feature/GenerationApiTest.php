<?php

namespace Tests\Feature;

use App\Jobs\GenerateRecipeJob;
use App\Models\Category;
use App\Models\GenerationIa;
use App\Models\Ingredient;
use App\Models\Recette;
use App\Models\User;
use App\Services\OpenRouterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class GenerationApiTest extends TestCase
{
    use RefreshDatabase;

    private array $validPayload = [
        'ingredients' => [
            ['name' => 'chicken breast', 'quantity' => '2', 'unit' => 'pieces'],
            ['name' => 'garlic', 'quantity' => '3', 'unit' => 'cloves'],
        ],
        'preferences' => 'Quick weeknight dinner',
        'servings' => 4,
        'difficulty' => 'easy',
    ];

    private function aiSuccessResult(): array
    {
        return [
            'recipe' => [
                'title' => 'Garlic Chicken Stir-Fry',
                'description' => 'A quick and healthy stir-fry.',
                'prep_time' => 10,
                'cook_time' => 15,
                'servings' => 4,
                'difficulty' => 'easy',
                'ingredients' => [
                    ['name' => 'chicken breast', 'quantity' => '2', 'unit' => 'pieces'],
                    ['name' => 'garlic', 'quantity' => '3', 'unit' => 'cloves'],
                    ['name' => 'olive oil', 'quantity' => '2', 'unit' => 'tbsp'],
                ],
                'categories' => ['Quick Meals'],
                'etapes' => [
                    ['step_number' => 1, 'instruction' => 'Slice chicken into strips.'],
                    ['step_number' => 2, 'instruction' => 'Heat oil and cook chicken.'],
                ],
            ],
            'model_used' => 'openai/gpt-4o-mini',
            'tokens_used' => 150,
        ];
    }

    private function mockOpenRouterService(array $result): void
    {
        $mock = Mockery::mock(OpenRouterService::class);
        $mock->shouldReceive('generateRecipe')->once()->andReturn($result);
        $this->app->instance(OpenRouterService::class, $mock);
    }

    private function mockOpenRouterServiceFailure(string $errorMessage): void
    {
        $mock = Mockery::mock(OpenRouterService::class);
        $mock->shouldReceive('generateRecipe')->once()->andThrow(
            new \RuntimeException($errorMessage)
        );
        $this->app->instance(OpenRouterService::class, $mock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_unauthenticated_generation_request_returns_401(): void
    {
        $this->postJson(route('api.generations.store'), $this->validPayload)
            ->assertUnauthorized();
    }

    public function test_valid_generation_request_returns_202_with_pending_status(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        $response = $this->actingAs($user)->postJson(
            route('api.generations.store'),
            $this->validPayload
        );

        $response->assertStatus(202)
            ->assertJsonPath('data.status', GenerationIa::STATUS_PENDING)
            ->assertJsonStructure([
                'data' => ['id', 'status', 'created_at'],
            ]);
    }

    public function test_generation_ia_record_created_with_correct_attributes(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        $this->actingAs($user)->postJson(
            route('api.generations.store'),
            $this->validPayload
        );

        $this->assertDatabaseHas('generation_ia', [
            'user_id' => $user->id,
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        $generation = GenerationIa::first();
        $this->assertNotNull($generation->prompt);
        $this->assertIsString($generation->prompt);

        $prompt = json_decode($generation->prompt, true);
        $this->assertArrayHasKey('ingredients', $prompt);
        $this->assertCount(2, $prompt['ingredients']);
    }

    public function test_generation_job_dispatched_to_generations_queue(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        $this->actingAs($user)->postJson(
            route('api.generations.store'),
            $this->validPayload
        );

        Queue::assertPushed(GenerateRecipeJob::class, function ($job) {
            return $job->queue === 'generations';
        });
    }

    public function test_successful_job_creates_recipe_with_ai_generated_flag(): void
    {
        $user = User::factory()->create();
        $this->mockOpenRouterService($this->aiSuccessResult());

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode($this->validPayload),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        GenerateRecipeJob::dispatchSync($generation);

        $this->assertDatabaseHas('recettes', [
            'user_id' => $user->id,
            'is_ai_generated' => true,
            'statut' => 'published',
            'title' => 'Garlic Chicken Stir-Fry',
        ]);

        $generation->refresh();
        $this->assertEquals(GenerationIa::STATUS_COMPLETED, $generation->status);
        $this->assertNotNull($generation->completed_at);
        $this->assertNotNull($generation->model_used);
        $this->assertEquals(150, $generation->tokens_used);
    }

    public function test_recipe_has_correct_etapes_ingredients_and_categories(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Quick Meals']);
        $this->mockOpenRouterService($this->aiSuccessResult());

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode(array_merge($this->validPayload, [
                'categories' => [$category->id],
            ])),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        GenerateRecipeJob::dispatchSync($generation);

        $recipe = $user->recettes()->first();

        $this->assertNotNull($recipe);
        $this->assertEquals('Garlic Chicken Stir-Fry', $recipe->title);

        $this->assertDatabaseCount('etapes', 2);
        $this->assertDatabaseHas('etapes', [
            'recette_id' => $recipe->id,
            'step_number' => 1,
            'instruction' => 'Slice chicken into strips.',
        ]);

        $this->assertDatabaseHas('recette_ingredient', [
            'recette_id' => $recipe->id,
            'quantity' => '2',
            'unit' => 'pieces',
        ]);

        $this->assertDatabaseHas('recette_categorie', [
            'recette_id' => $recipe->id,
            'categorie_id' => $category->id,
        ]);
    }

    public function test_ingredients_are_created_with_first_or_create(): void
    {
        $user = User::factory()->create();
        $this->mockOpenRouterService($this->aiSuccessResult());

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode($this->validPayload),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        GenerateRecipeJob::dispatchSync($generation);

        $this->assertDatabaseHas('ingredients', ['name' => 'chicken breast']);
        $this->assertDatabaseHas('ingredients', ['name' => 'garlic']);
        $this->assertDatabaseHas('ingredients', ['name' => 'olive oil']);

        $this->assertEquals(3, Ingredient::count());
    }

    public function test_generation_status_endpoint_returns_correct_data(): void
    {
        $user = User::factory()->create();
        $generation = GenerationIa::factory()->completed()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson(
            route('api.generations.show', $generation)
        );

        $response->assertOk()
            ->assertJsonPath('data.id', $generation->id)
            ->assertJsonPath('data.status', GenerationIa::STATUS_COMPLETED);
    }

    public function test_completed_generation_response_includes_recipe(): void
    {
        $user = User::factory()->create();
        $this->mockOpenRouterService($this->aiSuccessResult());

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode($this->validPayload),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        GenerateRecipeJob::dispatchSync($generation);

        $response = $this->actingAs($user)->getJson(
            route('api.generations.show', $generation)
        );

        $response->assertOk()
            ->assertJsonPath('data.status', GenerationIa::STATUS_COMPLETED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'status',
                    'recipe' => [
                        'id',
                        'title',
                        'etapes',
                        'ingredients',
                        'categories',
                    ],
                ],
            ]);
    }

    public function test_non_owner_cannot_view_generation(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $generation = GenerationIa::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->getJson(
            route('api.generations.show', $generation)
        )->assertNotFound();
    }

    public function test_admin_can_view_any_generation(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $owner = User::factory()->create();
        $generation = GenerationIa::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($admin)->getJson(
            route('api.generations.show', $generation)
        )->assertOk();
    }

    public function test_user_lists_only_own_generations(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $mine = GenerationIa::factory()->create(['user_id' => $user->id]);
        $theirs = GenerationIa::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->getJson(
            route('api.generations.index')
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $mine->id])
            ->assertJsonMissing(['id' => $theirs->id]);
    }

    public function test_admin_lists_all_generations(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $gen1 = GenerationIa::factory()->create(['user_id' => $user1->id]);
        $gen2 = GenerationIa::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($admin)->getJson(
            route('api.generations.index')
        );

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_invalid_generation_request_returns_422(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('api.generations.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ingredients']);

        $this->actingAs($user)->postJson(route('api.generations.store'), [
            'ingredients' => [],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['ingredients']);

        $this->actingAs($user)->postJson(route('api.generations.store'), [
            'ingredients' => [
                ['name' => 'chicken'],
            ],
            'difficulty' => 'invalid',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['difficulty']);
    }

    public function test_openrouter_failure_marks_generation_as_failed(): void
    {
        $user = User::factory()->create();
        $this->mockOpenRouterServiceFailure('OpenRouter API request failed');

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode($this->validPayload),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        try {
            GenerateRecipeJob::dispatchSync($generation);
        } catch (\Throwable $e) {
            // Expected
        }

        $generation->refresh();
        $this->assertEquals(GenerationIa::STATUS_FAILED, $generation->status);
        $this->assertNotNull($generation->error_message);
    }

    public function test_invalid_ai_json_marks_generation_as_failed(): void
    {
        $user = User::factory()->create();
        $this->mockOpenRouterServiceFailure('Invalid JSON in AI response');

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode($this->validPayload),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        try {
            GenerateRecipeJob::dispatchSync($generation);
        } catch (\Throwable $e) {
            // Expected
        }

        $generation->refresh();
        $this->assertEquals(GenerationIa::STATUS_FAILED, $generation->status);
        $this->assertDatabaseCount('recettes', 0);
    }

    public function test_database_transaction_rollback_on_recipe_creation_failure(): void
    {
        $user = User::factory()->create();

        $mock = Mockery::mock(OpenRouterService::class);
        $mock->shouldReceive('generateRecipe')->once()->andReturn([
            'recipe' => [
                'title' => 'Test',
                'description' => 'Test',
                'prep_time' => 10,
                'cook_time' => 10,
                'servings' => 4,
                'difficulty' => 'easy',
                'ingredients' => [
                    ['name' => 'test ingredient', 'quantity' => '1', 'unit' => 'cup'],
                ],
                'etapes' => [
                    ['step_number' => 1, 'instruction' => 'Do something'],
                ],
            ],
            'model_used' => 'test',
            'tokens_used' => 100,
        ]);
        $this->app->instance(OpenRouterService::class, $mock);

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode([
                'ingredients' => [['name' => 'test']],
            ]),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        GenerateRecipeJob::dispatchSync($generation);

        $recipe = Recette::first();
        $this->assertNotNull($recipe);
        $this->assertTrue($recipe->is_ai_generated);

        $generation->refresh();
        $this->assertEquals(GenerationIa::STATUS_COMPLETED, $generation->status);
    }

    public function test_job_retry_behavior_on_transient_failure(): void
    {
        $user = User::factory()->create();

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode($this->validPayload),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        $job = new GenerateRecipeJob($generation);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals([10, 30, 60], $job->backoff);
    }

    public function test_api_key_is_never_exposed_in_responses(): void
    {
        $user = User::factory()->create();
        $this->mockOpenRouterService($this->aiSuccessResult());

        $generation = GenerationIa::factory()->create([
            'user_id' => $user->id,
            'prompt' => json_encode($this->validPayload),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        GenerateRecipeJob::dispatchSync($generation);

        $response = $this->actingAs($user)->getJson(
            route('api.generations.show', $generation)
        );

        $response->assertOk();
        $content = $response->json();
        $jsonString = json_encode($content);
        $this->assertStringNotContainsString('sk-or-', $jsonString);
    }

    public function test_unauthenticated_generation_status_returns_401(): void
    {
        $generation = GenerationIa::factory()->create();

        $this->getJson(route('api.generations.show', $generation))
            ->assertUnauthorized();
    }

    public function test_unauthenticated_generation_list_returns_401(): void
    {
        $this->getJson(route('api.generations.index'))
            ->assertUnauthorized();
    }
}
