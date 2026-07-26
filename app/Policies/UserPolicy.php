<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isSuperAdmin() && $target->isCustomer();
    }

    public function resetPassword(User $user, User $target): bool
    {
        return $user->isSuperAdmin() && $target->isCustomer();
    }
}
