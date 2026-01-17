<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Order Model
 *
 * Represents an order placed by a dealer.
 *
 * @property int $id
 * @property int $organization_id
 * @property int $dealer_id
 * @property string $order_number
 * @property float $subtotal
 * @property float $discount_amount
 * @property float $taxable_amount
 * @property float $cgst_amount
 * @property float $sgst_amount
 * @property float $igst_amount
 * @property float $grand_total
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Order statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DISPATCHED = 'dispatched';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'dealer_id',
        'order_number',
        'subtotal',
        'discount_amount',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'grand_total',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'cgst_amount' => 'decimal:2',
            'sgst_amount' => 'decimal:2',
            'igst_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    /**
     * Get the organization that owns the order.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the dealer that placed the order.
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the dispatches for the order.
     */
    public function dispatches(): HasMany
    {
        return $this->hasMany(Dispatch::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the returns for the order.
     */
    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }

    /**
     * Get total paid amount from completed payments.
     */
    public function getPaidAmountAttribute(): float
    {
        return $this->payments()
            ->where('status', Payment::STATUS_COMPLETED)
            ->sum('amount');
    }

    /**
     * Get remaining amount to be paid.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->grand_total - $this->paid_amount);
    }

    /**
     * Check if order is fully paid.
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_amount <= 0;
    }
}

