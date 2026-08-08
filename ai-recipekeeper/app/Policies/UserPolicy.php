<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $user, User $resource): bool
    {
        return $user->isAdmin() || $user->id === $resource->id;
    }

    public function update(User $user, User $resource): bool
    {
        return $user->isAdmin() || $user->id === $resource->id;
    }

    public function delete(User $user, User $resource): bool
    {
        return $user->isAdmin() || $user->id === $resource->id;
    }
}
