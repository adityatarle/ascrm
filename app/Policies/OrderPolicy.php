<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'sales_officer', 'accountant']);
    }

    /**
     * Determine if the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->organization_id === $order->organization_id
            && $user->hasAnyRole(['admin', 'sales_officer', 'accountant']);
    }

    /**
     * Determine if the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'sales_officer']);
    }

    /**
     * Determine if the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->organization_id === $order->organization_id
            && $user->hasAnyRole(['admin', 'sales_officer'])
            && $order->status === Order::STATUS_PENDING;
    }

    /**
     * Determine if the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->organization_id === $order->organization_id
            && $user->hasRole('admin')
            && $order->status === Order::STATUS_PENDING;
    }
}

