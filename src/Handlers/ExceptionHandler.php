<?php

declare(strict_types=1);

namespace Rinvex\Fort\Handlers;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Rinvex\Fort\Exceptions\AuthorizationException;
use App\Exceptions\Handler as BaseExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExceptionHandler extends BaseExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $isBackend = mb_strpos($request->route()->getName(), 'backend') !== false;
            $single = mb_strtolower(mb_substr($exception->getModel(), mb_strrpos($exception->getModel(), '\\') + 1));
            $plural = str_plural($single);

            return intend([
                'url' => $isBackend ? route("backend.{$plural}.index") : route('frontend.home'),
                'with' => ['warning' => trans('messages.resource_not_found', ['resource' => $single, 'id' => $request->route()->parameter($single)])],
            ], 404);
        } elseif ($exception instanceof AuthorizationException) {
            return intend([
                'url' => '/',
                'with' => ['warning' => $exception->getMessage()],
            ], 403);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return intend([
            'url' => route('frontend.auth.login'),
            'withErrors' => ['rinvex.fort.session.required' => trans('messages.auth.session.required')],
        ], 401);
    }
}
