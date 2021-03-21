<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Http\Requests\Ticket\DeleteTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class TicketController extends Controller
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
    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $tickets = $organization->tickets()
            ->with(['channel', 'department', 'priority', 'status', 'user.organization'])
            ->when($request->filled('status_id'), function($query) use($request) {
                $query->where('status_id', $request->get('status_id'));
            })
            ->when($request->filled('priority_id'), function($query) use($request) {
                $query->where('priority_id', $request->get('priority_id'));
            })
            ->when($request->filled('department_id'), function($query) use($request) {
                $query->where('department_id', $request->get('department_id'));
            })        
            ->orderBy('last_replied_at', 'desc')
            ->paginate(50);

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketRequest $request, Organization $organization): TicketResource
    {
        $ticket = Ticket::create($request->validated());

        return new TicketResource($ticket);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $organization, Ticket $ticket): TicketResource
    {
        $ticket = $organization->tickets()
            ->with('messages.user.roles')
            ->find($ticket->getKey());

        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): TicketResource
    {
        $ticket->update($request->validated());

        return new TicketResource($ticket->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteTicketRequest $request, Organization $organization, Ticket $ticket): bool
    {
        return $ticket->delete();
    }
}
