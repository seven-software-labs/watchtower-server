<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'content',
        'message_type_id',
        'ticket_id',
        'source_id',
        'source_created_at',
        'sender_user_id',
        'recipient_user_id',
        'is_sent',
        'is_delivered',
    ];
    
    /**
     * The relationships that are automatically loaded.
     */
    protected $with = [
        'sender',
        'recipient',
    ];

    /**
     * Get the source created at attribute.
     */
    public function getSourceCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    /**
     * Get the ticket that this message belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the message type that this message belongs to.
     */
    public function messageType()
    {
        return $this->belongsTo(MessageType::class);
    }
    
    /**
     * Get the sender that this message belongs to.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * Get the recipient that this message blongs to.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
