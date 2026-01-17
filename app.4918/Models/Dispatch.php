<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Dispatch Model
 *
 * Represents a dispatch/shipment for an order.
 *
 * @property int $id
 * @property int $order_id
 * @property string $dispatch_number
 * @property string|null $lr_number
 * @property string|null $transporter_name
 * @property string|null $vehicle_number
 * @property \Illuminate\Support\Carbon|null $dispatched_at
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Dispatch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Dispatch statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_DISPATCHED = 'dispatched';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'dispatch_number',
        'lr_number',
        'transporter_name',
        'vehicle_number',
        'dispatched_at',
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
            'dispatched_at' => 'datetime',
        ];
    }

    /**
     * Get the order that owns the dispatch.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the dispatch items for the dispatch.
     */
    public function items(): HasMany
    {
        return $this->hasMany(DispatchItem::class);
    }
}

