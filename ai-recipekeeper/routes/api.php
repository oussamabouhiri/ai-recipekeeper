<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriController;
use App\Http\Controllers\RecetteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/recipes', [RecetteController::class, 'index'])->name('api.recipes.index');
Route::get('/recipes/{recipe}', [RecetteController::class, 'show'])->name('api.recipes.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('api.categories.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user())->name('api.user');

    Route::post('/tokens', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token = $request->user()->createToken($request->name);

        return response()->json([
            'token' => $token->plainTextToken,
        ], 201);
    })->name('api.tokens.create');

    Route::post('/recipes', [RecetteController::class, 'store'])->name('api.recipes.store');
    Route::put('/recipes/{recipe}', [RecetteController::class, 'update'])->name('api.recipes.update');
    Route::delete('/recipes/{recipe}', [RecetteController::class, 'destroy'])->name('api.recipes.destroy');

    Route::post('/categories', [CategoryController::class, 'store'])->name('api.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('api.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('api.categories.destroy');

    Route::get('/favorites', [FavoriController::class, 'index'])->name('api.favorites.index');
    Route::post('/favorites', [FavoriController::class, 'store'])->name('api.favorites.store');
    Route::delete('/favorites/{favori}', [FavoriController::class, 'destroy'])->name('api.favorites.destroy');
});
