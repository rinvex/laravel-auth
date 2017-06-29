<?php

declare(strict_types=1);

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
