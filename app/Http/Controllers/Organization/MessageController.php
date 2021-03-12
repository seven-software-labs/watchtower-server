<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Organization;
use App\Models\Ticket;
use App\Http\Resources\MessageResource;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\DeleteMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization): AnonymousResourceCollection
    {
        return MessageResource::collection($organization->messages()->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateMessageRequest $request): MessageResource
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

        if($request->get('message_type_id') == \App\Models\MessageType::REPLY) {
            $ticket->channel->sendMessage($ticket->channel, $message);
        }

        return new MessageResource($message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message): MessageResource
    {
        return new MessageResource($message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, Message $message): MessageResource
    {
        $message->update($request->validated());

        return new MessageResource($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteMessageRequest $request, Message $message): bool
    {
        return $message->delete();
    }
}
