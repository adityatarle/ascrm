<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * State Model
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string|null $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class State extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_state_master';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'fld_state_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fld_name',
        'fld_country_id',
        'fld_updated_by',
        'fld_updated_date',
        'fld_created_by',
        'fld_created_date',
        'fld_isdeleted',
        'fld_system_date',
    ];

    /**
     * Get the name attribute (maps to fld_name).
     */
    public function getNameAttribute()
    {
        return $this->attributes['fld_name'] ?? null;
    }

    /**
     * Set the name attribute (maps to fld_name).
     */
    public function setNameAttribute($value)
    {
        $this->attributes['fld_name'] = $value;
    }

    /**
     * Get the country_id attribute (maps to fld_country_id).
     */
    public function getCountryIdAttribute()
    {
        return $this->attributes['fld_country_id'] ?? null;
    }

    /**
     * Set the country_id attribute (maps to fld_country_id).
     */
    public function setCountryIdAttribute($value)
    {
        $this->attributes['fld_country_id'] = $value;
    }

    /**
     * Get the country that the state belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the cities for the state.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get the zones for the state.
     */
    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    /**
     * Get the product state rates for the state.
     */
    public function productStateRates(): HasMany
    {
        return $this->hasMany(ProductStateRate::class);
    }

    /**
     * Get the regions for the state.
     */
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    /**
     * Get the districts for the state.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'fld_state_id', 'fld_state_id');
    }

    /**
     * Scope a query to only include active (non-deleted) states.
     */
    public function scopeActive($query)
    {
        return $query->where('fld_isdeleted', 0);
    }
}

