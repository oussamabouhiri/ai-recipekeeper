<?php

namespace App\Policies;

use App\Models\Avis;
use App\Models\User;

class AvisPolicy
{
    public function update(User $user, Avis $avis): bool
    {
        return $user->isAdmin() || $user->id === $avis->user_id;
    }

    public function delete(User $user, Avis $avis): bool
    {
        return $user->isAdmin() || $user->id === $avis->user_id;
    }
}
