<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user && $user->isStaff() && $user->is_active, 403, 'Staff access only.');

        return $next($request);
    }
}
