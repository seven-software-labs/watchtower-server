<?php

namespace App\Models;

use App\Services\ServiceTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use ServiceTrait, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services';

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
        'settings_schema',
    ];

    /**
     * The relationships that are automatically loaded.
     */
    protected $with = [
        // ...
    ];

    /**
     * The relationship counts that are automatically appended.
     */
    protected $withCount = [
        // ...
    ];

    /**
     * Get the channel that belongs to the service.
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
