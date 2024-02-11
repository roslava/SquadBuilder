<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStaticBearerToken
{
    private const VALID_TOKEN = 'Bearer SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=';

    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('Authorization');

        // Use strict comparison for better accuracy
        if ($authorizationHeader !== self::VALID_TOKEN) {
            return response()->json(['message' => 'Unauthorized'],   401);
        }

        return $next($request);
    }
}
