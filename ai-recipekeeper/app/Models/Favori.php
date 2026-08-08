<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favori extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'recette_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recette(): BelongsTo
    {
        return $this->belongsTo(Recette::class);
    }
}
