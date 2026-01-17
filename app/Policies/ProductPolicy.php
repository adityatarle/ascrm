<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->organization_id === $product->organization_id;
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'sales_officer']);
    }

    /**
     * Determine if the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->organization_id === $product->organization_id
            && $user->hasAnyRole(['admin', 'sales_officer']);
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->organization_id === $product->organization_id
            && $user->hasRole('admin');
    }
}

