<?php

namespace App\Http\Middleware;

use App\Models\Company\UserApiSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateApiLastActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // Authenticated API user

        if ($user) {
            UserApiSession::where('token_id', $request->bearerToken())
                ->update(['last_active_at' => now()]);
        }

        return $next($request);
    }
}
