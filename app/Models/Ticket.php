<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel_id',
        'department_id',
        'organization_id',
        'priority_id',
        'status_id',
        'subject',
        'ticket_type_id',
        'user_id',
        'last_replied_at',
    ];

    /**
     * The relationships that are automatically loaded.
     */
    protected $with = [
        'ticketType',
        'channel',
        'department',
        'priority',
        'user.organization',
        'status',
    ];
    
    /**
     * Get the user that this ticket belongs to.
     */
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
    
    /**
     * Get the department that this ticket belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    /**
     * Get the channel that this ticket belongs to.
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
    
    /**
     * Get the organization that this ticket belongs to.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    /**
     * Get the priority that this ticket belongs to.
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
    
    /**
     * Get the user that this ticket belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the status that this ticket belongs to.
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the messages that belongs to this ticket.
     */
    public function messages()
    {
        return $this->hasMany(Message::class)
            ->orderBy('source_created_at')
            ->orderBy('created_at');
    }
}
