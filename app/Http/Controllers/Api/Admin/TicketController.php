<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeTicketStatusRequest;
use App\Http\Requests\ReplyTicketRequest;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with(['user', 'assignee']);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($tickets);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $ticket->load(['user', 'assignee', 'messages.user']);

        return response()->json([
            'ticket' => $ticket,
        ]);
    }

    public function assign(Request $request, Ticket $ticket): JsonResponse
    {
        $ticket->update([
            'assigned_to' => $request->user()->id,
            'status' => 'in_progress',
        ]);

        return response()->json([
            'message' => 'Ticket atribuído com sucesso.',
            'ticket' => $ticket->fresh(['user', 'assignee']),
        ]);
    }

    public function reply(ReplyTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'author_type' => 'admin',
            'message' => $request->message,
        ]);

        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        $message->load('user');

        return response()->json([
            'message' => 'Resposta enviada com sucesso.',
            'ticket_message' => $message,
        ], 201);
    }

    public function changeStatus(ChangeTicketStatusRequest $request, Ticket $ticket): JsonResponse
    {
        $data = ['status' => $request->status];

        if ($request->status === 'resolved' && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }

        if ($request->status === 'closed' && !$ticket->closed_at) {
            $data['closed_at'] = now();
        }

        $ticket->update($data);

        return response()->json([
            'message' => 'Status do ticket alterado com sucesso.',
            'ticket' => $ticket,
        ]);
    }
}
