<?php

declare(strict_types=1);

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
