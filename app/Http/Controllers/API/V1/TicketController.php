<?php

namespace App\Http\Controllers\API\V1;

use App\Actions\TicketCreateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Tickets
 *
 * API endpoints for managing tickets.
 */
class TicketController extends Controller
{
    public function index(SearchRequest $request): AnonymousResourceCollection
    {
        $tickets = Ticket::with([
            'user',
            'lane',
            'attachments'
        ])->filter();

        return TicketResource::collection($tickets);
    }

    public function store(TicketStoreRequest $request, TicketCreateAction $action): JsonResponse
    {
        $ticket = $action->execute($request->validated());

        return response()->json($ticket, Response::HTTP_CREATED);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $ticket->load([
            'user',
            'lane',
            'attachments'
        ]);

        return response()->json(TicketResource::make($ticket));
    }

    public function update(TicketRequest $request, Ticket $ticket): JsonResponse
    {
        $ticket->update($request->validated());

        return response()->json($ticket);
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
