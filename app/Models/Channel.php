<?php

namespace App\Models;

use App\Channels\ChannelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use ChannelTrait, HasFactory, SoftDeletes;

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
        'organization_id',
        'is_active',
    ];

    /**
     * The relationships that are automatically loaded.
     */
    protected $with = [
        // 'service',
        'department',
    ];

    /**
     * The relationship counts that are automatically appended.
     */
    protected $withCount = ['tickets'];
    
    /**
     * Get the tickets that belong to this channel.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
