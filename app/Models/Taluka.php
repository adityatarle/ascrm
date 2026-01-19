<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Taluka extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_taluka_master';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'fld_taluka_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fld_name',
        'fld_code',
        'fld_state_id',
        'fld_disc_id',
        'fld_country_id',
        'fld_sequence',
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
     * Get the state that the taluka belongs to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'fld_state_id', 'fld_state_id');
    }

    /**
     * Get the district that the taluka belongs to.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'fld_disc_id', 'fld_dist_id');
    }

    /**
     * Scope a query to only include active (non-deleted) talukas.
     */
    public function scopeActive($query)
    {
        return $query->where('fld_isdeleted', 0);
    }
}
