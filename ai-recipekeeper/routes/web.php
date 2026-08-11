<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AvisWebController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriWebController;
use App\Http\Controllers\GenerationWebController;
use App\Http\Controllers\RecipeWebController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('recipes', RecipeWebController::class)->except(['show']);
    Route::get('/recipes/{recipe}', [RecipeWebController::class, 'show'])->name('recipes.show');

    Route::get('/generations/create', [GenerationWebController::class, 'create'])->name('generations.create');
    Route::post('/generations', [GenerationWebController::class, 'store'])->name('generations.store');
    Route::get('/generations/{generation}', [GenerationWebController::class, 'show'])->name('generations.show');

    Route::get('/favorites', [FavoriWebController::class, 'index'])->name('favorites.index');
    Route::post('/favorites', [FavoriWebController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{favori}', [FavoriWebController::class, 'destroy'])->name('favorites.destroy');

    Route::post('/recipes/{recipe}/reviews', [AvisWebController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{avis}', [AvisWebController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{avis}', [AvisWebController::class, 'destroy'])->name('reviews.destroy');

    Route::middleware('admin')->group(function () {
        Route::get('/admin', fn () => view('admin.index'))->name('admin.index');

        Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::get('/admin/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
        Route::post('/admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    });
});
