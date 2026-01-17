<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DiscountSlab Model
 *
 * Represents discount slabs based on order value.
 *
 * @property int $id
 * @property float $min_amount
 * @property float|null $max_amount
 * @property float $discount_percent
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class DiscountSlab extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'min_amount',
        'max_amount',
        'discount_percent',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the discount percent for a given amount.
     *
     * @param float $amount
     * @return float
     */
    public static function getDiscountPercent(float $amount): float
    {
        $slab = self::where('is_active', true)
            ->where('min_amount', '<=', $amount)
            ->where(function ($query) use ($amount) {
                $query->whereNull('max_amount')
                    ->orWhere('max_amount', '>=', $amount);
            })
            ->orderBy('min_amount', 'desc')
            ->first();

        return $slab ? $slab->discount_percent : 0;
    }
}

