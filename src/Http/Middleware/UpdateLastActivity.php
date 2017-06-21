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
        $user = $request->user();
        $user->timestamps = false;
        $user->fill(['last_activity' => new Carbon()])->forceSave();
    }
}
