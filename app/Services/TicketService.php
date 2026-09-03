<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function createTicket(User $user, array $data): Ticket
    {
        return DB::transaction(function () use ($user, $data) {
            $ticket = Ticket::create([
                'number' => $this->generateTicketNumber(),
                'user_id' => $user->id,
                'subject' => $data['subject'],
                'category' => $data['category'] ?? 'general',
                'priority' => $data['priority'] ?? 'medium',
                'status' => 'open',
                'service_id' => $data['service_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
            ]);

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $data['message'],
                'is_internal' => false,
            ]);

            return $ticket->fresh();
        });
    }

    public function replyToTicket(Ticket $ticket, User $user, string $message, bool $isInternal = false): TicketMessage
    {
        $ticketMessage = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $message,
            'is_internal' => $isInternal,
        ]);

        $ticket->update([
            'status' => $ticket->status === 'closed' ? 'open' : $ticket->status,
        ]);

        return $ticketMessage;
    }

    public function assignTicket(Ticket $ticket, User $admin): Ticket
    {
        $ticket->update([
            'assigned_to' => $admin->id,
            'status' => $ticket->status === 'open' ? 'in_progress' : $ticket->status,
        ]);

        return $ticket->fresh();
    }

    public function changeStatus(Ticket $ticket, string $status): Ticket
    {
        $update = ['status' => $status];

        if ($status === 'in_progress' && is_null($ticket->started_at)) {
            $update['started_at'] = now();
        }

        if ($status === 'resolved') {
            $update['resolved_at'] = now();
        }

        if ($status === 'closed') {
            $update['closed_at'] = now();
            if (is_null($update['resolved_at'] ?? null)) {
                $update['resolved_at'] = now();
            }
        }

        $ticket->update($update);

        return $ticket->fresh();
    }

    public function closeTicket(Ticket $ticket): Ticket
    {
        $ticket->update([
            'status' => 'closed',
            'resolved_at' => $ticket->resolved_at ?? now(),
            'closed_at' => now(),
        ]);

        return $ticket->fresh();
    }

    protected function generateTicketNumber(): string
    {
        $year = date('Y');
        $count = Ticket::whereYear('created_at', $year)->count() + 1;
        return "TKT-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
