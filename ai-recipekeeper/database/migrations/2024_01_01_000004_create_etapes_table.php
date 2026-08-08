<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recette_id')->constrained()->cascadeOnDelete();
            $table->integer('step_number');
            $table->longText('instruction');
            $table->timestamps();

            $table->index('recette_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapes');
    }
};
