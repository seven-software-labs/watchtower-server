<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ChannelOrganization extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'channel_organization';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel_id',
        'is_active',
        'organization_id',
        'settings',
    ];

    /**
     * Get the organization that belongs to the pivot row.
     */
    public function channel()
    {
        return $this->belongsTo(\App\Models\Channel::class);
    }

    /**
     * Get the organization that belongs to the pivot row.
     */
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    /**
     * Get the settings attribute in a formatted collection.
     */
    public function getSettingsAttribute($value)
    {
        return collect(json_decode($value, true));
    }
}
