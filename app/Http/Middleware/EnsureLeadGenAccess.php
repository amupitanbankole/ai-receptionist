<?php

namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Symfony\\Component\\HttpFoundation\\Response;

class EnsureLeadGenAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $allowedIds = config('leadgen.allowed_user_ids', []);

        // Fail closed until authentication and an explicit operator allowlist are configured.
        if (!$user || empty($allowedIds) || !in_array((int) $user->getAuthIdentifier(), $allowedIds, true)) {
            abort(403, 'Unclaimed-funds lead generation access is not enabled for this account.');
        }

        return $next($request);
    }
}
