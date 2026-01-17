<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'description',
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
     * Get the organization that owns the discount slab.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the discount percent for a given amount.
     *
     * @param float $amount
     * @param int|null $organizationId
     * @return float
     */
    public static function getDiscountPercent(float $amount, ?int $organizationId = null): float
    {
        $query = self::where('is_active', true)
            ->where('min_amount', '<=', $amount)
            ->where(function ($q) use ($amount) {
                $q->whereNull('max_amount')
                    ->orWhere('max_amount', '>=', $amount);
            });

        if ($organizationId) {
            $query->where(function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId)
                    ->orWhereNull('organization_id'); // Global slabs
            });
        } else {
            $query->whereNull('organization_id'); // Only global slabs
        }

        $slab = $query->orderBy('min_amount', 'desc')->first();

        return $slab ? $slab->discount_percent : 0;
    }
}

