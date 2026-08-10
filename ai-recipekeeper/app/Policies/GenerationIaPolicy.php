<?php

namespace App\Policies;

use App\Models\GenerationIa;
use App\Models\User;

class GenerationIaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GenerationIa $generation): bool
    {
        return $user->isAdmin() || $user->id === $generation->user_id;
    }
}
