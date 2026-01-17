<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * City Model
 *
 * @property int $id
 * @property int $state_id
 * @property string $name
 * @property int|null $zone_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state_id',
        'name',
        'zone_id',
    ];

    /**
     * Get the state that the city belongs to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the zone that the city belongs to.
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
}

