<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RequestService
{
    public function createRequest(User $user, array $data): ServiceRequest
    {
        return ServiceRequest::create([
            'number' => $this->generateRequestNumber(),
            'user_id' => $user->id,
            'service_id' => $data['service_id'] ?? null,
            'type' => $data['type'],
            'subject' => $data['subject'],
            'description' => $data['description'] ?? null,
            'status' => 'pending',
            'data' => $data['data'] ?? null,
        ]);
    }

    public function handleRequest(ServiceRequest $request, User $admin, string $status, ?string $notes = null): ServiceRequest
    {
        $request->update([
            'status' => $status,
            'handled_by' => $admin->id,
            'handled_at' => now(),
            'admin_notes' => $notes,
        ]);

        if ($status === 'approved') {
            $this->processRequest($request);
        }

        return $request->fresh();
    }

    protected function processRequest(ServiceRequest $request): void
    {
        switch ($request->type) {
            case 'dns_change':
                $this->processDnsChange($request);
                break;
            case 'ns_change':
                $this->processNsChange($request);
                break;
        }
    }

    public function processDnsChange(ServiceRequest $request): void
    {
        $domain = Domain::find($request->data['domain_id'] ?? null);
        if (!$domain) {
            return;
        }

        $domain->update([
            'dns_records' => $request->data['dns_records'] ?? $domain->dns_records,
        ]);
    }

    public function processNsChange(ServiceRequest $request): void
    {
        $domain = Domain::find($request->data['domain_id'] ?? null);
        if (!$domain) {
            return;
        }

        $domain->update([
            'nameservers' => $request->data['nameservers'] ?? $domain->nameservers,
        ]);
    }

    protected function generateRequestNumber(): string
    {
        $year = date('Y');
        $count = ServiceRequest::whereYear('created_at', $year)->count() + 1;
        return "REQ-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
