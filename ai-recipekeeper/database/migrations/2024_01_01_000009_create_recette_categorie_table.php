<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recette_categorie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recette_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['recette_id', 'categorie_id']);
            $table->index('recette_id');
            $table->index('categorie_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recette_categorie');
    }
};
