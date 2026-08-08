<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenerationIa extends Model
{
    use HasFactory;

    protected $table = 'generation_ia';

    protected $fillable = ['user_id', 'prompt', 'response', 'model_used', 'tokens_used'];

    protected function casts(): array
    {
        return [
            'tokens_used' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
