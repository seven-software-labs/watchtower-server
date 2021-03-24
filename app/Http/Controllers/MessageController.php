<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\DeleteMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Models\Message;
use App\Models\Organization;
use App\Models\Ticket;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * The organization that holds the resources.
     */
    private Organization $organization;

    /**
     * Create a new ChannelController instance.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->organization = auth()->user()->masterOrganization;
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $messages = Message::where('ticket_id', $request->get('ticket_id'))
            ->paginate(15);

        return MessageResource::collection($messages);
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
            'subject' => $ticket->subject,
            'content' => $request->get('content'),
            'message_type_id' => $request->get('message_type_id'),
            'ticket_id' => $ticket->getKey(),
            'sender_user_id' => $request->get('user_id'),
            'source_created_at' => now(),
        ]);

        if($request->get('message_type_id') == \App\Models\MessageType::REPLY) {
            $ticket->channel->service->sendMessage($ticket->channel, $message);
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
