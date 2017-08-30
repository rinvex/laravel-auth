<?php

declare(strict_types=1);

namespace Rinvex\Fort\Services;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Auth\Access\Gate;
use Illuminate\Auth\Access\Response;
use Rinvex\Fort\Exceptions\AuthorizationException;

class AccessGate extends Gate
{
    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param string      $ability
     * @param array|mixed $arguments
     *
     * @throws \Rinvex\Fort\Exceptions\AuthorizationException
     *
     * @return \Illuminate\Auth\Access\Response
     */
    public function authorize($ability, $arguments = [])
    {
        $result = $this->raw($ability, $arguments);

        if ($result instanceof Response) {
            return $result;
        }

        if ($result) {
            return $this->allow();
        } else {
            $message = $ability === 'null'
                ? 'Requested resource must be authorized.'
                : 'Sorry, you do not have access to the requested resource!';

            throw new AuthorizationException($message, $ability, $arguments);
        }
    }

    /**
     * Define a new ability.
     *
     * @param string          $ability
     * @param callable|string $callback
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function define($ability, $callback)
    {
        if (is_callable($callback)) {
            $this->abilities[$ability] = $callback;
        } elseif (is_string($callback) && Str::contains($callback, '@')) {
            $this->abilities[$ability] = $this->buildCustomAbilityCallback($callback, $ability);
        } else {
            throw new InvalidArgumentException("Callback must be a callable or a 'Class@method' string.");
        }

        return $this;
    }

    /**
     * Create the ability callback for a callback string.
     *
     * @param string $callback
     *
     * @return \Closure
     */
    protected function buildCustomAbilityCallback($callback, $ability)
    {
        return function () use ($callback, $ability) {
            list($class, $method) = explode('@', $callback);

            return $this->resolvePolicy($class)->{$method}($ability, ...func_get_args());
        };
    }
}
