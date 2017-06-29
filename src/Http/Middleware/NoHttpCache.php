<?php

declare(strict_types=1);

namespace Rinvex\Fort\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class NoHttpCache
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // This step is only needed if you are returning
        // a view in your Controller or elsewhere, because
        // when returning a view `$next($request)` returns
        // a View object, not a Response object, so we need
        // to wrap the View back in a Response.
        if (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        $response->header('Pragma', 'no-cache')
                 ->header('Expires', 'Sat, 01-Jan-2000 00:00:00 GMT')
                 ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0, s-maxage=0');

        return $response;
    }
}
