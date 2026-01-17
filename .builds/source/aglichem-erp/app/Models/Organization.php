<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Organization Model
 *
 * Represents an organization/company in the ERP system.
 * Each organization has its own users, products, and orders.
 *
 * @property int $id
 * @property string $name
 * @property string|null $gstin
 * @property string|null $address
 * @property int|null $state_id
 * @property int|null $city_id
 * @property string|null $pincode
 * @property string|null $phone
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Organization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'gstin',
        'address',
        'state_id',
        'city_id',
        'pincode',
        'phone',
        'email',
    ];

    /**
     * Get the state that the organization belongs to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that the organization belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the users for the organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the products for the organization.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the orders for the organization.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

