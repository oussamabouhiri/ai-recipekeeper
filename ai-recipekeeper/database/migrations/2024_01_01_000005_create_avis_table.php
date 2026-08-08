<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recette_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('recette_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
