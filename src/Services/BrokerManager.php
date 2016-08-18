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
use Rinvex\Fort\Contracts\BrokerManagerContract;

class BrokerManager implements BrokerManagerContract
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The type of this broker.
     *
     * @var array
     */
    protected $type;

    /**
     * The array of created brokers.
     *
     * @var array
     */
    protected $brokers = [];

    /**
     * Create a new broker manager instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @param  string                             $type
     *
     * @return void
     */
    public function __construct($app, $type)
    {
        $this->app  = $app;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function broker($name = null)
    {
        $name = $name ?: $this->getDefaultBroker();

        return isset($this->brokers[$name]) ? $this->brokers[$name] : $this->brokers[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given broker.
     *
     * @param  string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \Rinvex\Fort\Contracts\ResetBrokerContract|\Rinvex\Fort\Contracts\VerificationBrokerContract
     */
    protected function resolve($name)
    {
        $config      = $this->getConfig($name);
        $type        = ucfirst(strtolower($this->type));
        $brokerClass = "Rinvex\\Fort\\Services\\{$type}Broker";

        if (is_null($config)) {
            throw new InvalidArgumentException("{$type} broker [{$name}] is not defined.");
        }

        // The broker uses a token repository to validate tokens and send user
        // e-mails, as well as validating that the process as an aggregate
        // service of sorts providing a convenient interface.
        return new $brokerClass($this->createTokenRepository($config), $this->app['auth']->createUserProvider($config['provider']), $this->app['mailer'], $config['email']);
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array $config
     *
     * @return \Rinvex\Fort\Contracts\ResetTokenRepositoryContract|\Rinvex\Fort\Contracts\VerificationTokenRepositoryContract
     */
    protected function createTokenRepository(array $config)
    {
        $key        = $this->app['config']['app.key'];
        $type       = ucfirst(strtolower($this->type));
        $table      = str_plural(strtolower($this->type));
        $tokenClass = "Rinvex\\Fort\\Repositories\\{$type}TokenRepository";

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return new $tokenClass($this->app['db']->connection(), $this->app['config']["rinvex.fort.tables.{$table}"], $key, $config['expire']);
    }

    /**
     * Get the broker configuration.
     *
     * @param  string $name
     *
     * @return array
     */
    protected function getConfig($name)
    {
        $type = strtolower($this->type);

        return $this->app['config']["rinvex.fort.{$type}.{$name}"];
    }

    /**
     * Get the default broker name.
     *
     * @return string
     */
    public function getDefaultBroker()
    {
        $type = strtolower($this->type);

        return $this->app['config']["rinvex.fort.{$type}.broker"];
    }

    /**
     * Set the default broker name.
     *
     * @param  string $name
     *
     * @return void
     */
    public function setDefaultBroker($name)
    {
        $type = strtolower($this->type);

        $this->app['config']["rinvex.fort.{$type}.broker"] = $name;
    }

    /**
     * Dynamically call the default broker instance.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->broker(), $method], $parameters);
    }
}
