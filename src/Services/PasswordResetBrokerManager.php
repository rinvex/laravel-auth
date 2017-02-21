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

namespace Rinvex\Fort\Services;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Contracts\Auth\PasswordBrokerFactory;
use Rinvex\Fort\Repositories\PasswordResetTokenRepository;

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
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return isset($this->brokers[$name])
                    ? $this->brokers[$name]
                    : $this->brokers[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given broker.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        // The password broker uses a token repository to validate tokens and send user
        // password e-mails, as well as validating that password reset process as an
        // aggregate service of sorts providing a convenient interface for resets.
        return new PasswordResetBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'])
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param array $config
     *
     * @return \Rinvex\Fort\Contracts\PasswordResetTokenRepositoryContract
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = isset($config['connection']) ? $config['connection'] : null;

        return new PasswordResetTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
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
    protected function getConfig($name)
    {
        return $this->app['config']["auth.passwords.{$name}"];
    }

    /**
     * Get the default password reset broker name.
     *
     * @return string
     */
    public function getDefaultDriver()
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
    public function setDefaultDriver($name)
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
