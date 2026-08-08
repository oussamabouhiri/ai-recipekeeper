<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avis extends Model
{
    use HasFactory;

    protected $fillable = ['recette_id', 'user_id', 'rating', 'comment'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function recette(): BelongsTo
    {
        return $this->belongsTo(Recette::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
