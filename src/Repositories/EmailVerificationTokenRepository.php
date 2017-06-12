<?php

declare(strict_types=1);

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
        $record = (array) $this->getTable()->where(
            'email', $user->getEmailForVerification()
        )->first();

        return $record &&
               ! $this->tokenExpired($record['created_at']) &&
               $this->hasher->check($token, $record['token']);
    }

    /**
     * Delete tokens of the given user.
     *
     * @param \Rinvex\Fort\Contracts\CanVerifyEmailContract $user
     *
     * @return void
     */
    public function delete(CanVerifyEmailContract $user)
    {
        $this->deleteExisting($user);
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
    public function getData(CanVerifyEmailContract $user)
    {
        return (array) $this->getTable()->where('email', $user->getEmailForVerification())->first();
    }
}
