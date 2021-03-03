<?php

namespace App\Models;

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
        'content',
        'message_type_id',
        'ticket_id',
        'user_id',
        'source_id',
        'source_created_at',
    ];

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
     * Get the user that this ticket belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
