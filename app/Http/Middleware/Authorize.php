<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth_token = $request->cookie('AUTH-TOKEN');
        $user = User::where('auth_token', $auth_token)->first();
        $request['user'] = $user;
        return $user && $user->acc_status === 'ENABLED' ? $next($request) : abort(401, 'The user is unauthorized.');
    }
}
