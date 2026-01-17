<?php

namespace App\Policies;

use App\Models\Dispatch;
use App\Models\User;

class DispatchPolicy
{
    /**
     * Determine if the user can view any dispatches.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'dispatch_officer', 'sales_officer']);
    }

    /**
     * Determine if the user can view the dispatch.
     */
    public function view(User $user, Dispatch $dispatch): bool
    {
        return $user->organization_id === $dispatch->order->organization_id
            && $user->hasAnyRole(['admin', 'dispatch_officer', 'sales_officer']);
    }

    /**
     * Determine if the user can create dispatches.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'dispatch_officer']);
    }

    /**
     * Determine if the user can update the dispatch.
     */
    public function update(User $user, Dispatch $dispatch): bool
    {
        return $user->organization_id === $dispatch->order->organization_id
            && $user->hasAnyRole(['admin', 'dispatch_officer']);
    }

    /**
     * Determine if the user can delete the dispatch.
     */
    public function delete(User $user, Dispatch $dispatch): bool
    {
        return $user->organization_id === $dispatch->order->organization_id
            && $user->hasRole('admin');
    }
}

