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
    public function index(Request $request): AnonymousResourceCollection
    {
        $tickets = $this->organization->tickets()
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
            ->when($request->filled('organization_id'), function($query) use($request) {
                $query->where('organization_id', $request->get('organization_id'));
            })        
            ->when($request->filled('user_id'), function($query) use($request) {
                $query->where('user_id', $request->get('user_id'));
            })        
            ->orderBy('last_replied_at', 'desc')
            ->paginate(50);

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketRequest $request): TicketResource
    {
        $ticket = Ticket::create($request->validated());

        return new TicketResource($ticket);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Ticket $ticket): TicketResource
    {
        $ticket = $this->organization->tickets()
            ->with(['user'])
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
    public function destroy(DeleteTicketRequest $request, Ticket $ticket): bool
    {
        return $ticket->delete();
    }
}
