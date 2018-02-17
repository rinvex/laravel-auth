<?php

declare(strict_types=1);

namespace Rinvex\Auth\Contracts;

use Illuminate\Contracts\Auth\PasswordBroker;

interface PasswordResetBrokerContract extends PasswordBroker
{
    /**
     * Constant representing an expired token.
     *
     * @var string
     */
    const EXPIRED_TOKEN = 'passwords.expired';
}
