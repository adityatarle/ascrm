<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Crop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'name',
        'image',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Boot the model.
     * Auto-generate unique_id if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($crop) {
            if (empty($crop->unique_id)) {
                $crop->unique_id = static::generateUniqueId();
            }
        });
    }

    /**
     * Generate a unique ID for the crop.
     */
    protected static function generateUniqueId(): string
    {
        do {
            $uniqueId = 'CROP-' . strtoupper(Str::random(8));
        } while (static::where('unique_id', $uniqueId)->exists());

        return $uniqueId;
    }

    /**
     * Get the products assigned to this crop.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'crop_product')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
