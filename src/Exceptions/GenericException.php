<?php

declare(strict_types=1);

namespace Rinvex\Fort\Exceptions;

use Exception;

class GenericException extends Exception
{
    /**
     * The exception inputs.
     *
     * @var array
     */
    protected $inputs;

    /**
     * The exception redirection.
     *
     * @var string
     */
    protected $redirection;

    /**
     * Create a new authorization exception.
     *
     * @param string $message
     * @param array  $redirection
     */
    public function __construct($message = 'This action is unauthorized.', $redirection = null, array $inputs = null)
    {
        parent::__construct($message);

        $this->inputs = $inputs;
        $this->redirection = $redirection;
    }

    /**
     * Gets the Exception redirection.
     *
     * @return string
     */
    final public function getRedirection()
    {
        return $this->redirection;
    }

    /**
     * Gets the Exception inputs.
     *
     * @return array
     */
    final public function getInputs()
    {
        return $this->inputs;
    }
}
