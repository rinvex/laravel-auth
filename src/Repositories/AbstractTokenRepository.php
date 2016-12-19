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

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\ConnectionInterface;

abstract class AbstractTokenRepository
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /**
     * The number of minutes a token should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * Create a new token repository instance.
     *
     * @param \Illuminate\Database\ConnectionInterface $connection
     * @param string                                   $table
     * @param string                                   $hashKey
     * @param int                                      $expires
     */
    public function __construct(ConnectionInterface $connection, $table, $hashKey, $expires = 60)
    {
        $this->table = $table;
        $this->hashKey = $hashKey;
        $this->expires = $expires;
        $this->connection = $connection;
    }

    /**
     * Build the record payload for the table.
     *
     * @param string $email
     * @param string $token
     * @param string $token
     * @param string $ip
     *
     * @return array
     */
    protected function getPayload($email, $token, $agent, $ip)
    {
        return [
            'email'      => $email,
            'token'      => $token,
            'agent'      => $agent,
            'ip'         => $ip,
            'created_at' => new Carbon(),
        ];
    }

    /**
     * Determine if the given token has expired.
     *
     * @param array $token
     *
     * @return bool
     */
    protected function tokenExpired($token)
    {
        $expiresAt = Carbon::parse($token['created_at'])->addMinutes($this->expires);

        return $expiresAt->isPast();
    }

    /**
     * Delete the given token.
     *
     * @param string $token
     *
     * @return void
     */
    public function delete($token)
    {
        $this->getTable()->where('token', $token)->delete();
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subMinutes($this->expires);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    /**
     * Create a new token.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    /**
     * Get a fresh query builder instance for the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Get the database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the token expiration in minutes.
     *
     * @return int
     */
    public function getExpiration()
    {
        return $this->expires;
    }
}
