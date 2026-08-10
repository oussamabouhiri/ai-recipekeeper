<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenerationIa extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $table = 'generation_ia';

    protected $fillable = ['user_id', 'prompt', 'response', 'model_used', 'tokens_used', 'status', 'job_id', 'error_message', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'tokens_used' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRecette(): ?Recette
    {
        $recipeId = $this->getRecipeId();

        if ($recipeId === null) {
            return null;
        }

        return Recette::find($recipeId);
    }

    public function getRecipeId(): ?int
    {
        if ($this->response === null) {
            return null;
        }

        $data = json_decode($this->response, true);

        return $data['recipe_id'] ?? null;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
