<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSize extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'unit_id',
        'size_value',
        'size_label',
        'base_price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'size_value' => 'decimal:3',
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the product that owns this size.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the unit for this size.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the state rates for this product size.
     */
    public function stateRates(): HasMany
    {
        return $this->hasMany(ProductStateRate::class);
    }

    /**
     * Get the display label for the size.
     */
    public function getDisplayLabelAttribute(): string
    {
        if ($this->size_label) {
            return $this->size_label;
        }
        
        if ($this->unit && $this->size_value) {
            return $this->size_value . $this->unit->symbol;
        }
        
        return (string) $this->size_value;
    }
}
