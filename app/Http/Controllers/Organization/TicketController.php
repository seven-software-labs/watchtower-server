<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Organization;
use App\Http\Resources\TicketResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $tickets = $organization->tickets()
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
            ->paginate(15);

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
