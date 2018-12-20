<?php

declare(strict_types=1);

namespace Rinvex\Auth\Traits;

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
    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * {@inheritdoc}
     */
    public function sendPhoneVerificationNotification(string $method, bool $force): void
    {
        ! $this->phoneVerificationNotificationClass
        || $this->notify(new $this->phoneVerificationNotificationClass($method, $force));
    }
}
