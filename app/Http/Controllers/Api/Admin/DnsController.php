<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDnsRequest;
use App\Http\Requests\UpdateIpRequest;
use App\Http\Requests\UpdateNsRequest;
use App\Models\Domain;
use Illuminate\Http\JsonResponse;

class DnsController extends Controller
{
    public function updateNameservers(UpdateNsRequest $request, Domain $domain): JsonResponse
    {
        $domain->update([
            'ns1' => $request->ns1,
            'ns2' => $request->ns2,
            'ns3' => $request->ns3,
            'ns4' => $request->ns4,
        ]);

        return response()->json([
            'message' => 'Nameservers atualizados com sucesso.',
            'domain' => $domain,
        ]);
    }

    public function updateIp(UpdateIpRequest $request, Domain $domain): JsonResponse
    {
        $domain->update(['ip' => $request->ip]);

        return response()->json([
            'message' => 'IP atualizado com sucesso.',
            'domain' => $domain,
        ]);
    }

    public function updateDns(UpdateDnsRequest $request, Domain $domain): JsonResponse
    {
        $domain->update(['dns_provider' => $request->dns_provider]);

        return response()->json([
            'message' => 'Provedor DNS atualizado com sucesso.',
            'domain' => $domain,
        ]);
    }
}
