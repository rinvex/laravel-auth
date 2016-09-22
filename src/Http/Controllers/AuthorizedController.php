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

namespace Rinvex\Fort\Http\Controllers;

class AuthorizedController extends AbstractController
{
    /**
     * Create a new manage persistence controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->getAuthMiddleware(), ['except' => $this->middlewareWhitelist]);
    }
}
