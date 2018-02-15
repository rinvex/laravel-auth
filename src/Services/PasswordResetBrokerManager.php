<?php

declare(strict_types=1);

namespace Rinvex\Auth\Services;

use InvalidArgumentException;
use Illuminate\Contracts\Auth\PasswordBrokerFactory;
use Rinvex\Auth\Contracts\PasswordResetBrokerContract;

class PasswordResetBrokerManager implements PasswordBrokerFactory
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $brokers = [];

    /**
     * Create a new PasswordResetBroker manager instance.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Attempt to get the broker from the local cache.
     *
     * @param string $name
     *
     * @return \Rinvex\Auth\Contracts\PasswordResetBrokerContract
     */
    public function broker($name = null): PasswordResetBrokerContract
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->brokers[$name] ?? $this->brokers[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given broker.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \Rinvex\Auth\Contracts\PasswordResetBrokerContract
     */
    protected function resolve($name): PasswordResetBrokerContract
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        return new PasswordResetBroker(
            $this->app['auth']->createUserProvider($config['provider']),
            $this->app['config']['app.key'],
            $config['expire']
        );
    }

    /**
     * Get the password reset broker configuration.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($name): array
    {
        return $this->app['config']["auth.passwords.{$name}"];
    }

    /**
     * Get the default password reset broker name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->app['config']['auth.defaults.passwords'];
    }

    /**
     * Set the default password reset broker name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultDriver($name): void
    {
        $this->app['config']['auth.defaults.passwords'] = $name;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->broker()->{$method}(...$parameters);
    }
}
