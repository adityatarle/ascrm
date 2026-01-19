<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_dist_master';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'fld_dist_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fld_dist_name',
        'fld_state_id',
        'fld_country_id',
        'fld_updated_by',
        'fld_updated_date',
        'fld_created_by',
        'fld_created_date',
        'fld_isdeleted',
        'fld_system_date',
    ];

    /**
     * Get the name attribute (maps to fld_dist_name).
     */
    public function getNameAttribute()
    {
        return $this->attributes['fld_dist_name'] ?? null;
    }

    /**
     * Set the name attribute (maps to fld_dist_name).
     */
    public function setNameAttribute($value)
    {
        $this->attributes['fld_dist_name'] = $value;
    }

    /**
     * Get the state that the district belongs to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'fld_state_id', 'fld_state_id');
    }

    /**
     * Get the talukas for the district.
     */
    public function talukas(): HasMany
    {
        return $this->hasMany(Taluka::class, 'fld_disc_id', 'fld_dist_id');
    }

    /**
     * Scope a query to only include active (non-deleted) districts.
     */
    public function scopeActive($query)
    {
        return $query->where('fld_isdeleted', 0);
    }
}
