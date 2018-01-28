<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Notifications\PhoneVerificationNotification;

trait CanVerifyPhone
{
    /**
     * {@inheritdoc}
     */
    public function getPhoneForVerification(): ?string
    {
        return $this->phone;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryForVerification(): ?string
    {
        return $this->country_code ? country($this->country_code)->getCallingCode() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function isPhoneVerified(): bool
    {
        return (bool) $this->phone_verified;
    }

    /**
     * {@inheritdoc}
     */
    public function sendPhoneVerificationNotification($method, $force): void
    {
        $this->notify(new PhoneVerificationNotification($method, $force));
    }
}
