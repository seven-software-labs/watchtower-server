<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
        'master_organization_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The relationships that are automatically loaded.
     * 
     * @var array
     */
    protected $with = [
        // ...
    ];    

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'is_customer',
    ];

    /**
     * Get the organization that belong to the user.
     */
    public function getIsCustomerAttribute()
    {
        return $this->hasRole('customer') || (!$this->hasRole(['administrator', 'operator']));
    }

    /**
     * Get the profile photo attribute.
     */
    public function getProfilePhotoUrlAttribute()
    {
        return "";
    }
    
    /**
     * Get the channels that belong to the user.
     */
    public function channels()
    {
        return $this->belongsToMany(Channel::class)
            ->using(Pivot\ChannelUser::class);
    }

    /**
     * Get the organization that belong to the user.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the master organization that belong to the user.
     */
    public function masterOrganization()
    {
        return $this->belongsTo(Organization::class, 'master_organization_id');
    }

    /**
     * Get the tickets that belong to the user.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Override Laravel Passport's function to find the
     * user that's trying to authenticate.
     */
    public function findForPassport($credentials)
    {
        // Parse the organization subdomain and the email.
        $credentials = explode(":", $credentials);

        // Find the organization with the given subdomain.
        $organization = Organization::where('subdomain', $credentials[0])->first();

        // Find the user with the email in the subdomain.
        return self::where('email', $credentials[1])
            ->where('master_organization_id', $organization->getKey())
            ->first();
    }
}
