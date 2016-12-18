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

class AuthorizationException extends Exception
{
    /**
     * The exception ability.
     *
     * @var string
     */
    public $ability;

    /**
     * The exception arguments.
     *
     * @var array
     */
    public $arguments;

    /**
     * Create a new authorization exception.
     *
     * @param string $message
     * @param string $ability
     * @param array  $arguments
     */
    public function __construct($message = 'This action is unauthorized.', $ability = null, $arguments = [])
    {
        parent::__construct($message);

        $this->ability = $ability;
        $this->arguments = $arguments;
    }
}
