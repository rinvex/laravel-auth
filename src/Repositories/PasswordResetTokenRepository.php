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

use Rinvex\Fort\Contracts\CanResetPasswordContract;
use Rinvex\Fort\Contracts\PasswordResetTokenRepositoryContract;

class PasswordResetTokenRepository extends AbstractTokenRepository implements PasswordResetTokenRepositoryContract
{
    /**
     * {@inheritdoc}
     */
    public function create(CanResetPasswordContract $user)
    {
        $email = $user->getEmailForPasswordReset();
        $agent = request()->server('HTTP_USER_AGENT');
        $ip = request()->ip();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($email, $token, $agent, $ip));

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $email = $user->getEmailForPasswordReset();

        $token = (array) $this->getTable()->where('email', $email)->where('token', $token)->first();

        return $token && ! $this->tokenExpired($token);
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return $this->getTable()->where('email', $user->getEmailForPasswordReset())->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getData(CanResetPasswordContract $user, $token)
    {
        $email = $user->getEmailForPasswordReset();

        return (array) $this->getTable()->where('email', $email)->where('token', $token)->first();
    }
}
