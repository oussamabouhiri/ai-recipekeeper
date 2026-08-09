<?php

namespace App\Policies;

use App\Models\Recette;
use App\Models\User;

class RecettePolicy
{
    public function update(User $user, Recette $recette): bool
    {
        return $user->isAdmin() || $user->id === $recette->user_id;
    }

    public function delete(User $user, Recette $recette): bool
    {
        return $user->isAdmin() || $user->id === $recette->user_id;
    }
}
