<?php

namespace App\Policies;

use App\Models\Favori;
use App\Models\User;

class FavoriPolicy
{
    public function delete(User $user, Favori $favori): bool
    {
        return $user->isAdmin() || $user->id === $favori->user_id;
    }
}
