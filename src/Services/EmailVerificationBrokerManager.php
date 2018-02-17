<?php

declare(strict_types=1);

namespace Rinvex\Auth\Services;

use InvalidArgumentException;
use Rinvex\Auth\Contracts\EmailVerificationBrokerContract;
use Rinvex\Auth\Contracts\EmailVerificationBrokerFactoryContract;

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
     * @return \Rinvex\Auth\Contracts\EmailVerificationBrokerContract
     */
    public function broker($name = null): EmailVerificationBrokerContract
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
     * @return \Rinvex\Auth\Contracts\EmailVerificationBrokerContract
     */
    protected function resolve($name): EmailVerificationBrokerContract
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Email verification broker [{$name}] is not defined.");
        }

        return new EmailVerificationBroker(
            $this->app['auth']->createUserProvider($config['provider']),
            $this->app['config']['app.key'],
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
    protected function getConfig($name): array
    {
        return $this->app['config']["rinvex.auth.emailverification.{$name}"];
    }

    /**
     * Get the default email verification broker name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->app['config']['rinvex.auth.emailverification.broker'];
    }

    /**
     * Set the default email verification broker name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultDriver($name): void
    {
        $this->app['config']['rinvex.auth.emailverification.broker'] = $name;
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
