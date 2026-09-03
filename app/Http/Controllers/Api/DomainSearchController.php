<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DomainSearchController extends Controller
{
    private string $apiUrl = 'http://domain.angohost.ao:4000';
    private int $timeout = 10;

    public function search(Request $request): JsonResponse
    {
        $domain = $request->input('domain');

        if (!$domain) {
            return response()->json(['message' => 'Domínio é obrigatório.'], 422);
        }

        $domain = strtolower(trim($domain));

        $cacheKey = "domain_search_" . md5($domain);
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withoutVerifying()
                ->get("{$this->apiUrl}/search", ['domain' => $domain]);

            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, 300);
                return response()->json($data);
            }

            return response()->json([
                'domain' => $domain,
                'available' => null,
                'error' => 'Não foi possível verificar o domínio.',
                'source' => 'fallback'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'domain' => $domain,
                'available' => null,
                'error' => 'Serviço de pesquisa indisponível.',
                'message' => $e->getMessage(),
                'source' => 'fallback'
            ]);
        }
    }

    public function whois(Request $request): JsonResponse
    {
        $domain = $request->input('domain');

        if (!$domain) {
            return response()->json(['message' => 'Domínio é obrigatório.'], 422);
        }

        $domain = strtolower(trim($domain));

        $cacheKey = "domain_whois_" . md5($domain);
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withoutVerifying()
                ->get("{$this->apiUrl}/whois", ['domain' => $domain]);

            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, 600);
                return response()->json($data);
            }

            return response()->json([
                'domain' => $domain,
                'available' => null,
                'error' => 'Não foi possível consultar WHOIS.',
                'source' => 'fallback'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'domain' => $domain,
                'available' => null,
                'error' => 'Serviço WHOIS indisponível.',
                'message' => $e->getMessage(),
                'source' => 'fallback'
            ]);
        }
    }
}
