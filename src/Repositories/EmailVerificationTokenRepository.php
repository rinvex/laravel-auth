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

use Rinvex\Fort\Contracts\CanVerifyEmailContract;
use Rinvex\Fort\Contracts\EmailVerificationTokenRepositoryContract;

class EmailVerificationTokenRepository extends AbstractTokenRepository implements EmailVerificationTokenRepositoryContract
{
    /**
     * {@inheritdoc}
     */
    public function create(CanVerifyEmailContract $user)
    {
        $email = $user->getEmailForVerification();
        $agent = request()->server('HTTP_USER_AGENT');
        $ip = request()->ip();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link for verification. Then we will insert a record in database
        // so that we can verify the token within the actual verification.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($email, $token, $agent, $ip));

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(CanVerifyEmailContract $user, $token)
    {
        $email = $user->getEmailForVerification();

        $token = (array) $this->getTable()->where('email', $email)->where('token', $token)->first();

        return $token && ! $this->tokenExpired($token);
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteExisting(CanVerifyEmailContract $user)
    {
        return $this->getTable()->where('email', $user->getEmailForVerification())->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getData(CanVerifyEmailContract $user, $token)
    {
        $email = $user->getEmailForVerification();

        return (array) $this->getTable()->where('email', $email)->where('token', $token)->first();
    }
}
