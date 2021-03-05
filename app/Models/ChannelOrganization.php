<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelOrganization extends Model
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
        'department_id',
        'is_active',
        'organization_id',
        'settings',
    ];

    /**
     * The relationships that are automatically loaded.
     * 
     * @var array
     */
    protected $with = [
        'department',
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
     * Get the settings attribute in a formatted collection.
     */
    public function getSettingsAttribute($value)
    {
        return collect(json_decode($value, true));
    }
}
