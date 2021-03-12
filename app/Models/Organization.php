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
        return $this->hasMany(Channel::class);
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
        return $this->hasMany(Department::class);
    }

    /**
     * Get the priorities that belong to the organization.
     */
    public function priorities()
    {
        return $this->hasMany(Priority::class);
    }

    /**
     * Get the default priority for the organization.
     */
    public function getDefaultPriorityAttribute()
    {
        return $this->priorities()->where('is_default', true)->first();
    }

    /**
     * Get the statuses that belong to the organization.
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * Get the default status for the organization.
     */
    public function getDefaultStatusAttribute()
    {
        return $this->statuses()->where('is_default', true)->first();
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
        return $this->hasMany(User::class);
    }    

    /**
     * Perform the initial setup for an organization.
     */
    public function setupOrganization()
    {
        // Create Departments
        $departments = [
            [
                'name' => 'Client Success',
                'color' => 'gray',
                'is_default' => true,
            ],
        ];

        foreach($departments as $department) {
            $this->departments()->create($department);
        }

        // Create Priorities
        $priorities = [
            [
                'name' => 'Low',
                'color' => 'gray',
                'is_default' => false,
            ],
            [
                'name' => 'Medium',
                'color' => 'blue',
                'is_default' => true,
            ],
            [
                'name' => 'High',
                'color' => 'yellow',
                'is_default' => false,
            ],
            [
                'name' => 'Critical',
                'color' => 'red',
                'is_default' => false,
            ],
        ];

        foreach ($priorities as $priority) {
            $this->priorities()->create($priority);
        }

        // Create Statuses
        $statuses = [
            [
                'name' => 'Open',
                'color' => 'green',
                'is_default' => true,
            ],
            [
                'name' => 'Pending',
                'color' => 'yellow',
                'is_default' => false,
            ],
            [
                'name' => 'Closed',
                'color' => 'gray',
                'is_default' => false,
            ],
        ];

        foreach($statuses as $status) {
            $this->statuses()->create($status);
        }        
    }
}
