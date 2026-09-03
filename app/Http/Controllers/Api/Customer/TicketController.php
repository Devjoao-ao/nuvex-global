<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tickets = $request->user()
            ->tickets()
            ->with(['assignee'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($tickets);
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = Ticket::create([
            'number' => 'TKT-' . strtoupper(uniqid()),
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority ?? 'medium',
            'status' => 'open',
            'description' => $request->description,
            'related_service_type' => $request->related_service_type,
            'related_service_id' => $request->related_service_id,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'author_type' => 'customer',
            'message' => $request->description,
        ]);

        $ticket->load('messages.user');

        return response()->json([
            'message' => 'Ticket criado com sucesso.',
            'ticket' => $ticket,
        ], 201);
    }

    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Ticket não encontrado.',
            ], 404);
        }

        $ticket->load(['assignee', 'messages.user' => function ($query) {
            $query->where('author_type', '!=', 'internal');
        }]);

        return response()->json([
            'ticket' => $ticket,
        ]);
    }

    public function reply(ReplyTicketRequest $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Ticket não encontrado.',
            ], 404);
        }

        if (in_array($ticket->status, ['closed', 'resolved'])) {
            return response()->json([
                'message' => 'Não é possível responder a um ticket fechado ou resolvido.',
            ], 422);
        }

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'author_type' => 'customer',
            'message' => $request->message,
        ]);

        if ($ticket->status === 'waiting') {
            $ticket->update(['status' => 'open']);
        }

        $message->load('user');

        return response()->json([
            'message' => 'Resposta enviada com sucesso.',
            'ticket_message' => $message,
        ], 201);
    }

    public function close(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Ticket não encontrado.',
            ], 404);
        }

        if (in_array($ticket->status, ['closed'])) {
            return response()->json([
                'message' => 'O ticket já está fechado.',
            ], 422);
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Ticket fechado com sucesso.',
            'ticket' => $ticket,
        ]);
    }
}
