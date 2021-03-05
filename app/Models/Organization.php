<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organizations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The relationship counts that are automatically appended.
     */
    protected $withCount = ['users'];

    /**
     * Get the channels that belong to the organization.
     */
    public function channels()
    {
        return $this->hasManyThrough(Channel::class, ChannelOrganization::class, 'channel_id', 'id')
            ->with('channel_organization');
            // ->using(Pivot\ChannelOrganization::class)
            // ->withPivot('id', 'name', 'is_active', 'settings', 'department_id');
    }    

    /**
     * Get the channels that belong to the organization.
     */
    public function organizations()
    {
        return $this->hasMany(self::class, 'parent_organization_id');
    }    

    /**
     * Get the departments that belong to the organization.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class)
            ->using(Pivot\OrganizationDepartment::class)
            ->withPivot('is_default');
    }

    /**
     * Get the priorities that belong to the organization.
     */
    public function priorities()
    {
        return $this->belongsToMany(Priority::class)
            ->using(Pivot\OrganizationPriority::class)
            ->withPivot('is_default');
    }

    /**
     * Get the statuses that belong to the organization.
     */
    public function statuses()
    {
        return $this->belongsToMany(Status::class)
            ->using(Pivot\OrganizationStatus::class)
            ->withPivot('is_default');
    }

    /**
     * Get the tickets that belong to the organization.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the users that belong to the organization.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(Pivot\OrganizationUser::class);
    }    
}
