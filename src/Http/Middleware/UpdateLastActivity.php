<?php

declare(strict_types=1);

namespace Rinvex\Fort\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param \Illuminate\Http\Request                   $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    public function terminate($request, $response)
    {
        if ($user = $request->user()) {
            // We are using database queries rather than eloquent, to bypass triggering events.
            // Triggering update events flush cache and costs us more queries, which we don't need.
            $userModel = config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');
            (new $userModel())->where('id', $user->id)->update(['last_activity' => new Carbon()]);
        }
    }
}
