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
        'class',
        'slug',
        'is_active',
    ];

    /**
     * The relationships that are automatically loaded.
     */
    protected $with = [
        'channelSettings',
    ];

    /**
     * The relationship counts that are automatically appended.
     */
    protected $withCount = ['tickets'];
    
    /**
     * Get the channel settings that belong to this channel.
     */
    public function channelSettings()
    {
        return $this->hasMany(ChannelSetting::class);
    }
    
    /**
     * Get the tickets that belong to this channel.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the organizations that belong to the channel.
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('id', 'is_active');
    }
}
