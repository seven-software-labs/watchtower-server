<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'channels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'department_id',
        'organization_id',
        'service_id',
        'is_active',
        'settings',
    ];

    /**
     * The relationships that are automatically loaded.
     */
    protected $with = [
        'service',
        'department',
    ];

    /**
     * The relationship counts that are automatically appended.
     */
    protected $withCount = ['tickets'];

    /**
     * Get the channel's settings attribute.
     */
    public function getSettingsAttribute($value)
    {
        return collect(json_decode($value));
    }
    
    /**
     * Get the department that this channel belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }    

    /**
     * Get the organization that belongs to the priority.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    /**
     * Get the service that this channel belongs to.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }    
    
    /**
     * Get the tickets that belong to this channel.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the users that belong to the channel.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(Pivot\ChannelUser::class);
    }
}
