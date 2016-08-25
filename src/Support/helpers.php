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

if (! function_exists('get_login_field')) {
    /**
     * Get the login field to be used.
     *
     * @param string $loginfield
     *
     * @return string
     */
    function get_login_field($loginfield)
    {
        return ! $loginfield || filter_var($loginfield, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }
}
