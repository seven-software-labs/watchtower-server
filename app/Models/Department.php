<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color',
        'organization_id',
        'is_default',
        'is_removeable',
    ];

    /**
     * The relationship counts that are automatically appended.
     */
    protected $withCount = [
        'channels',
        'tickets', 
    ];

    /**
     * Get the organization that belong to the department.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    /**
     * Get the tickets that belong to this department.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    /**
     * Get the channels that belong to this department.
     */
    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
}
