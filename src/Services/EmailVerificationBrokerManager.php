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
use Rinvex\Fort\Repositories\EmailVerificationTokenRepository;
use Rinvex\Fort\Contracts\EmailVerificationBrokerFactoryContract;

class EmailVerificationBrokerManager implements EmailVerificationBrokerFactoryContract
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
     * Create a new EmailVerificationBroker manager instance.
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
     * @return \Rinvex\Fort\Contracts\EmailVerificationBrokerContract
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Email verification broker [{$name}] is not defined.");
        }

        // The email verification broker uses a token repository to validate tokens and send email verification e-mails
        return new EmailVerificationBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'])
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param array $config
     *
     * @return \Rinvex\Fort\Contracts\EmailVerificationTokenRepositoryContract
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = isset($config['connection']) ? $config['connection'] : null;

        return new EmailVerificationTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $this->app['config']['rinvex.fort.tables.email_verifications'],
            $key,
            $config['expire']
        );
    }

    /**
     * Get the email verification broker configuration.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["rinvex.fort.emailverification.{$name}"];
    }

    /**
     * Get the default email verification broker name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['rinvex.fort.emailverification.broker'];
    }

    /**
     * Set the default email verification broker name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['rinvex.fort.emailverification.broker'] = $name;
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
