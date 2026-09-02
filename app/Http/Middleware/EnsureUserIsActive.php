<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            Auth::guard('web')->logout();

            return redirect()->route('login')
                ->withErrors(['mobile' => 'حساب شما فعال نیست.']);
        }

        return $next($request);
    }
}
