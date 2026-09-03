<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->active) {
            return response()->json(['message' => 'Conta desativada. Contacte o suporte.'], 403);
        }

        return $next($request);
    }
}
