<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\Domain;
use App\Models\User;

class NotificationService
{
    public function sendToUser(User $user, string $type, string $event, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'event' => $event,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'read' => false,
        ]);
    }

    public function sendOrderConfirmation(Order $order): Notification
    {
        return $this->sendToUser(
            $order->user,
            'order',
            'order.created',
            'Order Confirmed',
            "Your order {$order->number} has been confirmed. Total: {$order->currency} {$order->total}",
            ['order_id' => $order->id, 'order_number' => $order->number]
        );
    }

    public function sendPaymentConfirmed(Order $order): Notification
    {
        return $this->sendToUser(
            $order->user,
            'payment',
            'payment.confirmed',
            'Payment Confirmed',
            "Payment for order {$order->number} has been confirmed. Amount: {$order->currency} {$order->total}",
            ['order_id' => $order->id, 'order_number' => $order->number]
        );
    }

    public function sendServiceActivated(Service $service): Notification
    {
        return $this->sendToUser(
            $service->user,
            'service',
            'service.activated',
            'Service Activated',
            "Your service {$service->name} ({$service->number}) has been activated.",
            ['service_id' => $service->id, 'service_number' => $service->number]
        );
    }

    public function sendServiceSuspended(Service $service): Notification
    {
        return $this->sendToUser(
            $service->user,
            'service',
            'service.suspended',
            'Service Suspended',
            "Your service {$service->name} ({$service->number}) has been suspended. Please contact support.",
            ['service_id' => $service->id, 'service_number' => $service->number]
        );
    }

    public function sendServiceExpired(Service $service): Notification
    {
        return $this->sendToUser(
            $service->user,
            'service',
            'service.expired',
            'Service Expired',
            "Your service {$service->name} ({$service->number}) has expired. Please renew to continue.",
            ['service_id' => $service->id, 'service_number' => $service->number]
        );
    }

    public function sendTicketCreated(Ticket $ticket): Notification
    {
        return $this->sendToUser(
            $ticket->user,
            'ticket',
            'ticket.created',
            'Ticket Created',
            "Your support ticket {$ticket->number} has been created: {$ticket->subject}",
            ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->number]
        );
    }

    public function sendTicketReplied(Ticket $ticket): Notification
    {
        return $this->sendToUser(
            $ticket->user,
            'ticket',
            'ticket.replied',
            'Ticket Replied',
            "There is a new reply on your ticket {$ticket->number}: {$ticket->subject}",
            ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->number]
        );
    }

    public function sendTicketClosed(Ticket $ticket): Notification
    {
        return $this->sendToUser(
            $ticket->user,
            'ticket',
            'ticket.closed',
            'Ticket Closed',
            "Your ticket {$ticket->number} has been closed. {$ticket->subject}",
            ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->number]
        );
    }

    public function sendDomainExpiring(Domain $domain, int $daysLeft): Notification
    {
        return $this->sendToUser(
            $domain->user,
            'domain',
            'domain.expiring',
            'Domain Expiring',
            "Your domain {$domain->name} will expire in {$daysLeft} days. Please renew it.",
            ['domain_id' => $domain->id, 'domain_name' => $domain->name, 'days_left' => $daysLeft]
        );
    }

    public function markAsRead(Notification $notification): Notification
    {
        $notification->update([
            'read' => true,
            'read_at' => now(),
        ]);

        return $notification->fresh();
    }

    public function markAllAsRead(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
    }
}
