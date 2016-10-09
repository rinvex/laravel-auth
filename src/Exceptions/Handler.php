<?php

/*
 * NOTICE OF LICENSE
 *
 * Part of the Rinvex Fort Package.
 *
 * This source file is subject to The MIT License (MIT)
 * that is bundled with this package in the LICENSE file.
 *
 * Package: Rinvex Fort Package
 * License: The MIT License (MIT)
 * Link:    https://rinvex.com
 */

namespace Rinvex\Fort\Exceptions;

use Exception;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\Handler as ExceptionHandler;
use Rinvex\Repository\Exceptions\EntityNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof InvalidPersistenceException) {
            return intend([
                'route'      => 'rinvex.fort.frontend.auth.login',
                'withErrors' => ['rinvex.fort.session.expired' => Lang::get('rinvex.fort::frontend/messages.auth.session.expired')],
            ], 401);
        } else if ($exception instanceof EntityNotFoundException) {
            $single = strtolower(trim(strrchr($exception->getModel(), '\\'), '\\'));
            $plural = str_plural($single);

            return intend([
                'route'      => 'rinvex.fort.backend.'.$plural.'.index',
                'withErrors' => ['rinvex.fort.'.$single.'.not_found' => trans('rinvex.fort::backend/messages.'.$single.'.not_found', [$single => $exception->getId()])],
            ]);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request                 $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return intend([
            'route'      => 'rinvex.fort.frontend.auth.login',
            'withErrors' => ['rinvex.fort.session.required' => Lang::get('rinvex.fort::frontend/messages.auth.session.required')],
        ], 401);
    }
}
