<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isCustomer();
    }

    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->isCustomer() && $transaction->user_id === $user->id;
    }
}
