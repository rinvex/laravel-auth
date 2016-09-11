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

namespace Rinvex\Fort\Repositories;

use Illuminate\Support\Str;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Foundation\Application;
use Rinvex\Fort\Contracts\UserRepositoryContract;
use Rinvex\Fort\Contracts\AuthenticatableContract;
use Rinvex\Repository\Repositories\EloquentRepository;

class UserRepository extends EloquentRepository implements UserRepositoryContract
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * create a new user repository instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct(Application $app, Hasher $hasher)
    {
        $this->setContainer($app)
             ->setHasher($hasher)
             ->setRepositoryId('rinvex.fort.user')
             ->setModel($app['config']['rinvex.fort.models.user']);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username)
    {
        return $this->findBy('username', $username);
    }

    /**
     * {@inheritdoc}
     */
    public function findByToken($identifier, $token)
    {
        return $this->where($this->getAuthIdentifierName(), $identifier)
                    ->where($this->getRememberTokenName(), $token)
                    ->findFirst();
    }

    /**
     * {@inheritdoc}
     */
    public function findByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $model = $this;

        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, 'password')) {
                $model = $model->where($key, $value);
            }
        }

        return $model->findFirst();
    }

    /**
     * {@inheritdoc}
     */
    public function updateRememberToken(AuthenticatableContract $user, $token)
    {
        $this->update($user, [$this->getRememberTokenName() => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function validateCredentials(AuthenticatableContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->getHasher()->check($plain, $user->getAuthPassword());
    }

    /**
     * Gets the hasher implementation.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }

    /**
     * Sets the hasher implementation.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     *
     * @return $this
     */
    public function setHasher(Hasher $hasher)
    {
        $this->hasher = $hasher;

        return $this;
    }
}
