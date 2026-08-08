<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recette_ingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recette_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->string('quantity')->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();

            $table->unique(['recette_id', 'ingredient_id']);
            $table->index('recette_id');
            $table->index('ingredient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recette_ingredient');
    }
};
