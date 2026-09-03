<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\EmailService;
use App\Models\EmailAccount;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Models\ServiceTransfer;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(User $user, array $items, array $paymentData): Order
    {
        return DB::transaction(function () use ($user, $items, $paymentData) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $subtotal += $item['price'] * ($item['quantity'] ?? 1);
                $orderItems[] = $item;
            }

            $order = Order::create([
                'number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'currency' => 'AOA',
                'status' => 'pending',
                'payment_method' => $paymentData['method'] ?? 'reference',
                'payment_reference' => $paymentData['reference'] ?? null,
                'description' => collect($items)->pluck('name')->implode(' + '),
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create([
                    'plan_id' => $item['plan_id'] ?? null,
                    'type' => $item['type'],
                    'name' => $item['name'],
                    'domain_name' => $item['domain_name'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'] ?? 1,
                    'metadata' => $item['metadata'] ?? null,
                ]);
            }

            Invoice::create([
                'number' => $this->generateInvoiceNumber(),
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $subtotal,
                'currency' => 'AOA',
                'description' => $order->description,
                'method' => $paymentData['method'] ?? null,
                'reference' => $paymentData['reference'] ?? null,
                'status' => 'pending',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
            ]);

            return $order;
        });
    }

    public function confirmPayment(Order $order, array $paymentData): Order
    {
        return DB::transaction(function () use ($order, $paymentData) {
            $order->update([
                'status' => 'processing',
                'paid_at' => now(),
                'payment_reference' => $paymentData['reference'] ?? $order->payment_reference,
            ]);

            $payment = Payment::create([
                'number' => $this->generatePaymentNumber(),
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'amount' => $order->total,
                'currency' => $order->currency,
                'method' => $order->payment_method,
                'reference' => $paymentData['reference'] ?? null,
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            $order->invoices()->update([
                'status' => 'paid',
                'payment_id' => $payment->id,
            ]);

            return $order;
        });
    }

    public function processOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'processing',
                'processed_at' => now(),
            ]);

            foreach ($order->items as $item) {
                $this->createServiceFromItem($order, $item);
            }

            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $order;
        });
    }

    protected function createServiceFromItem(Order $order, OrderItem $item): Service
    {
        $service = Service::create([
            'number' => $this->generateServiceNumber(),
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'plan_id' => $item->plan_id,
            'type' => $item->type,
            'name' => $item->name,
            'status' => 'pending',
            'start_date' => now(),
            'expiry_date' => now()->addYear(),
        ]);

        switch ($item->type) {
            case 'domain':
                Domain::create([
                    'service_id' => $service->id,
                    'user_id' => $order->user_id,
                    'name' => $item->name,
                    'status' => 'pending',
                    'registration_date' => now()->toDateString(),
                    'expiry_date' => now()->addYear()->toDateString(),
                    'registrar' => 'NUVEX',
                    'dns_provider' => 'Cloudflare',
                ]);
                break;

            case 'hosting':
                $plan = $item->plan_id ? \App\Models\Plan::find($item->plan_id) : null;
                $features = $plan->features ?? [];
                Hosting::create([
                    'service_id' => $service->id,
                    'user_id' => $order->user_id,
                    'domain_name' => $item->domain_name ?? $item->name,
                    'plan_name' => $item->name,
                    'status' => 'pending',
                    'start_date' => now()->toDateString(),
                    'expiry_date' => now()->addYear()->toDateString(),
                    'storage' => $features['storage'] ?? null,
                    'bandwidth' => $features['bandwidth'] ?? null,
                    'max_emails' => $features['emails'] ?? null,
                    'max_databases' => $features['databases'] ?? null,
                ]);
                break;

            case 'email':
                $domainName = $item->domain_name ?? ($item->metadata['domain'] ?? '');
                $plan = $item->plan_id ? \App\Models\Plan::find($item->plan_id) : null;
                $features = $plan->features ?? [];
                $emailService = EmailService::create([
                    'service_id' => $service->id,
                    'user_id' => $order->user_id,
                    'domain_name' => $domainName,
                    'plan_name' => $item->name,
                    'status' => 'pending',
                    'start_date' => now()->toDateString(),
                    'expiry_date' => now()->addYear()->toDateString(),
                    'storage' => $features['storage'] ?? null,
                    'server' => 'mail.nuvex.ao',
                    'max_accounts' => $features['accounts'] ?? 1,
                ]);

                if (!empty($item->metadata['addresses'])) {
                    foreach ($item->metadata['addresses'] as $address) {
                        EmailAccount::create([
                            'email_service_id' => $emailService->id,
                            'user_id' => $order->user_id,
                            'address' => $address,
                            'status' => 'pending',
                            'webmail_url' => 'webmail.nuvex.ao',
                        ]);
                    }
                }
                break;
        }

        return $service;
    }

    public function activateService(Service $service): Service
    {
        $service->update(['status' => 'active', 'activated_at' => now()]);

        $service->domain?->update(['status' => 'active']);
        $service->hosting?->update(['status' => 'active']);
        $service->emailService?->update(['status' => 'active']);

        if ($service->emailService) {
            $service->emailService->accounts()->update(['status' => 'active']);
        }

        return $service;
    }

    public function transferService(Service $service, User $fromUser, User $toUser, User $admin, ?string $reason = null): ServiceTransfer
    {
        return DB::transaction(function () use ($service, $fromUser, $toUser, $admin, $reason) {
            $transfer = ServiceTransfer::create([
                'service_id' => $service->id,
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
                'admin_id' => $admin->id,
                'reason' => $reason,
            ]);

            $service->update(['user_id' => $toUser->id]);
            $service->domain?->update(['user_id' => $toUser->id]);
            $service->hosting?->update(['user_id' => $toUser->id]);
            $service->emailService?->update(['user_id' => $toUser->id]);

            if ($service->emailService) {
                $service->emailService->accounts()->update(['user_id' => $toUser->id]);
            }

            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'transferred',
                'entity_type' => 'Service',
                'entity_id' => $service->id,
                'entity_name' => $service->name,
                'old_values' => ['user_id' => $fromUser->id, 'user_name' => $fromUser->name],
                'new_values' => ['user_id' => $toUser->id, 'user_name' => $toUser->name],
                'description' => "Service transferred from {$fromUser->name} to {$toUser->name}",
            ]);

            return $transfer;
        });
    }

    public function suspendService(Service $service): Service
    {
        $service->update(['status' => 'suspended']);
        $service->domain?->update(['status' => 'suspended']);
        $service->hosting?->update(['status' => 'suspended']);
        $service->emailService?->update(['status' => 'suspended']);

        return $service;
    }

    public function cancelService(Service $service): Service
    {
        $service->update(['status' => 'cancelled']);
        $service->domain?->update(['status' => 'cancelled']);
        $service->hosting?->update(['status' => 'cancelled']);
        $service->emailService?->update(['status' => 'cancelled']);

        return $service;
    }

    protected function generateOrderNumber(): string
    {
        $year = date('Y');
        $count = Order::whereYear('created_at', $year)->count() + 1;
        return "ORD-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    protected function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $count = Invoice::whereYear('created_at', $year)->count() + 1;
        return "INV-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    protected function generatePaymentNumber(): string
    {
        $year = date('Y');
        $count = Payment::whereYear('created_at', $year)->count() + 1;
        return "PAY-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    protected function generateServiceNumber(): string
    {
        $year = date('Y');
        $count = Service::whereYear('created_at', $year)->count() + 1;
        return "SRV-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
