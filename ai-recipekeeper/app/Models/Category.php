<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function recettes(): BelongsToMany
    {
        return $this->belongsToMany(Recette::class, 'recette_categorie', 'categorie_id', 'recette_id');
    }
}
