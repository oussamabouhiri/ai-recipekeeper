<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recette extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'image_path',
        'statut',
        'user_id',
        'is_ai_generated',
    ];

    protected function casts(): array
    {
        return [
            'is_ai_generated' => 'boolean',
            'prep_time' => 'integer',
            'cook_time' => 'integer',
            'servings' => 'integer',
        ];
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if ($user?->isAdmin()) {
            return $query;
        }

        $query->where(function (Builder $q) use ($user) {
            $q->where('statut', 'published');

            if ($user !== null) {
                $q->orWhere(function (Builder $owner) use ($user) {
                    $owner->where('statut', 'hidden')
                        ->where('user_id', $user->id);
                });
            }
        });

        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'recette_categorie', 'recette_id', 'categorie_id');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recette_ingredient', 'recette_id', 'ingredient_id')
            ->withPivot('quantity', 'unit');
    }

    public function etapes(): HasMany
    {
        return $this->hasMany(Etape::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(Favori::class);
    }
}
