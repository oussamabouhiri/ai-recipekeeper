<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Etape extends Model
{
    use HasFactory;

    protected $fillable = ['recette_id', 'step_number', 'instruction'];

    protected function casts(): array
    {
        return [
            'step_number' => 'integer',
        ];
    }

    public function recette(): BelongsTo
    {
        return $this->belongsTo(Recette::class);
    }
}
