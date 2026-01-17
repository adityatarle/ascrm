<?php

namespace App\Policies;

use App\Models\Dealer;
use App\Models\User;

class DealerPolicy
{
    /**
     * Determine if the user can view any dealers.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'sales_officer', 'accountant']);
    }

    /**
     * Determine if the user can view the dealer.
     */
    public function view(User $user, Dealer $dealer): bool
    {
        return $user->hasAnyRole(['admin', 'sales_officer', 'accountant']);
    }

    /**
     * Determine if the user can create dealers.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'sales_officer']);
    }

    /**
     * Determine if the user can update the dealer.
     */
    public function update(User $user, Dealer $dealer): bool
    {
        return $user->hasAnyRole(['admin', 'sales_officer']);
    }

    /**
     * Determine if the user can delete the dealer.
     */
    public function delete(User $user, Dealer $dealer): bool
    {
        return $user->hasRole('admin');
    }
}

