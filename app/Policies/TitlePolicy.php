<?php

namespace App\Policies;

use App\Models\Title;
use App\Models\User;

class TitlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isCustomer();
    }

    public function view(User $user, Title $title): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isCustomer();
    }

    public function update(User $user, Title $title): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Title $title): bool
    {
        return $user->isSuperAdmin();
    }
}
