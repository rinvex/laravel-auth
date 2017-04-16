<?php

declare(strict_types=1);

namespace Rinvex\Fort\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            return intend([
                'url' => route('frontend.auth.login'),
                'withErrors' => ['rinvex.fort.session.expired' => trans('messages.auth.session.required')],
            ], 401);
        }

        return $next($request);
    }
}
