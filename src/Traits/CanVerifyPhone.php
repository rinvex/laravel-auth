<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

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
        return $this->phone_verified;
    }

    /**
     * {@inheritdoc}
     */
    public function sendPhoneVerificationNotification($method, $force): void
    {
        ! $this->phoneVerificationNotificationClass
        || $this->notify(new $this->phoneVerificationNotificationClass($method, $force));
    }
}
