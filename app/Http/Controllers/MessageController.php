<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Resources\MessageResource;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMessageRequest $request)
    {
        // Find the ticket.
        $ticket = Ticket::findOrFail($request->get('ticket_id'));

        // Create the message for the ticket.
        $message = Message::create([
            'content' => $request->get('content'),
            'message_type_id' => $request->get('message_type_id'),
            'ticket_id' => $ticket->getKey(),
            'user_id' => $request->get('user_id'),
            'source_created_at' => now(),
        ]);

        if($request->get('message_type_id') == 1) {
            $ticket->channel->sendMessage($message);
        }

        return new MessageResource($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
