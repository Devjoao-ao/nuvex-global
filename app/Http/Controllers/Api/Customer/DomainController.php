<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDnsRequest;
use App\Http\Requests\UpdateNsRequest;
use App\Models\Domain;
use App\Models\Request as ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $domains = $request->user()
            ->domains()
            ->with(['service'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($domains);
    }

    public function show(Request $request, Domain $domain): JsonResponse
    {
        if ($domain->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Domínio não encontrado.',
            ], 404);
        }

        $domain->load('service');

        return response()->json([
            'domain' => $domain,
        ]);
    }

    public function updateNs(UpdateNsRequest $request, Domain $domain): JsonResponse
    {
        if ($domain->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Domínio não encontrado.',
            ], 404);
        }

        $serviceRequest = ServiceRequest::create([
            'number' => 'REQ-' . strtoupper(uniqid()),
            'user_id' => $request->user()->id,
            'type' => 'ns_change',
            'service_type' => 'domain',
            'service_id' => $domain->service_id,
            'status' => 'pending',
            'data' => [
                'domain_name' => $domain->name,
                'current_ns1' => $domain->ns1,
                'current_ns2' => $domain->ns2,
                'new_ns1' => $request->ns1,
                'new_ns2' => $request->ns2,
                'new_ns3' => $request->ns3,
                'new_ns4' => $request->ns4,
            ],
            'reason' => 'Solicitação de alteração de nameservers pelo cliente.',
        ]);

        return response()->json([
            'message' => 'Solicitação de alteração de nameservers criada com sucesso.',
            'request' => $serviceRequest,
        ], 201);
    }

    public function updateDns(UpdateDnsRequest $request, Domain $domain): JsonResponse
    {
        if ($domain->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Domínio não encontrado.',
            ], 404);
        }

        $serviceRequest = ServiceRequest::create([
            'number' => 'REQ-' . strtoupper(uniqid()),
            'user_id' => $request->user()->id,
            'type' => 'dns_change',
            'service_type' => 'domain',
            'service_id' => $domain->service_id,
            'status' => 'pending',
            'data' => [
                'domain_name' => $domain->name,
                'current_dns_provider' => $domain->dns_provider,
                'new_dns_provider' => $request->dns_provider,
            ],
            'reason' => 'Solicitação de alteração de provedor DNS pelo cliente.',
        ]);

        return response()->json([
            'message' => 'Solicitação de alteração de DNS criada com sucesso.',
            'request' => $serviceRequest,
        ], 201);
    }
}
