<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * OrderItem Model
 *
 * Represents an item in an order.
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property float $rate
 * @property float $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_size_id',
        'quantity',
        'dispatched_quantity',
        'rate',
        'subtotal',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'dispatched_quantity' => 'integer',
            'rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function getRemainingQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->dispatched_quantity);
    }

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product size for the item.
     */
    public function productSize(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class);
    }

    /**
     * Get the dispatch items for this order item.
     */
    public function dispatchItems(): HasMany
    {
        return $this->hasMany(DispatchItem::class);
    }
}

