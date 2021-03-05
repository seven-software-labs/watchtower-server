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
        'name',
        'channel_id',
        'is_active',
        'organization_id',
        'settings',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'channel_organization_id',
    ];

    /**
     * Get the organization that belongs to the pivot row.
     */
    public function channel()
    {
        return $this->belongsTo(\App\Models\Channel::class);
    }

    /**
     * Get the department that belongs to the pivot row.
     */
    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    /**
     * Get the organization that belongs to the pivot row.
     */
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    /**
     * Get the row id in a readable format.
     */
    public function getChannelOrganizationIdAttribute()
    {
        return $this->id;
    }

    /**
     * Get the name attribute with a default value.
     */
    public function getNameAttribute($value = null)
    {
        if(!$value) {
            return 'Undefined Nickname';
        }

        return $value;
    }

    /**
     * Get the settings attribute in a formatted collection.
     */
    public function getSettingsAttribute($value)
    {
        return collect(json_decode($value, true));
    }
}
