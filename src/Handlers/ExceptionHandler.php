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
            $single = mb_strtolower(trim(mb_strrchr($exception->getModel(), '\\'), '\\'));
            $plural = str_plural($single);

            return intend([
                'url' => route('backend.'.$plural.'.index'),
                'withErrors' => ['rinvex.fort.'.$single.'.not_found' => trans('messages.'.$single.'.not_found')],
            ]);
        } elseif ($exception instanceof AuthorizationException) {
            return intend([
                'url' => '/',
                'withErrors' => ['rinvex.fort.unauthorized' => $exception->getMessage()],
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
