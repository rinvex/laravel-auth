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

use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\Authy\AuthyChannel;
use Rinvex\Fort\Contracts\AuthenticatableContract;
use Rinvex\Fort\Contracts\TwoFactorProviderContract;
use NotificationChannels\Authy\Exceptions\InvalidConfiguration;

class TwoFactorAuthyProvider implements TwoFactorProviderContract
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * The Authy service key.
     *
     * @var string
     */
    protected $key;

    /**
     * The Authy service API endpoint.
     *
     * @var string
     */
    protected $api;

    /**
     * Create a new Authy instance.
     *
     * @param \GuzzleHttp\Client $http
     *
     * @throws \NotificationChannels\Authy\Exceptions\InvalidConfiguration
     *
     * @return void
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;

        // Prepare required data
        $mode      = config('services.authy.mode');
        $this->key = config('services.authy.keys.'.$mode);
        $this->api = $mode === 'sandbox' ? AuthyChannel::API_ENDPOINT_SANDBOX : AuthyChannel::API_ENDPOINT_PRODUCTION;

        // Check configuration
        if (! $mode || ! $this->key) {
            throw new InvalidConfiguration();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register(AuthenticatableContract $user)
    {
        // Fire the Two-Factor register start event
        event('rinvex.fort.twofactor.phone.register.start', [$user]);

        // Register Authy user
        $url      = $this->api.'/protected/json/users/new?api_key='.$this->key;
        $response = json_decode($this->http->post($url, [
            'form_params' => [
                'user' => [
                    'email'        => $user->getEmailForVerification(),
                    'cellphone'    => preg_replace('/[^0-9]/', '', $user->getPhoneForVerification()),
                    'country_code' => $user->getCountryForVerification(),
                ],
            ],
        ])->getBody(), true);

        if (! $authyId = array_get($response, 'user.id') || ! isset($response['success']) || ! $response['success']) {
            // Fire the Two-Factor register failed event
            event('rinvex.fort.twofactor.phone.register.failed', [$user, $response]);

            // Registration failed!
            return false;
        }

        // Prepare required variables
        $settings = $user->getTwoFactor();

        // Update user account
        array_set($settings, 'phone', [
            'enabled'  => true,
            'authy_id' => $authyId,
        ]);
        app('rinvex.fort.user')->update($user, ['two_factor' => $settings]);

        // Fire the Two-Factor register success event
        event('rinvex.fort.twofactor.phone.register.success', [$user, $response]);

        // Return Authy Id
        return $authyId;
    }

    /**
     * {@inheritdoc}
     */
    public function tokenIsValid(AuthenticatableContract $user, $token, $force = true)
    {
        // Fire the Two-Factor verify start event
        event('rinvex.fort.twofactor.phone.verify.start', [$user, $token]);

        // Prepare required variables
        $force   = $force ? '&force=true' : '';
        $settings = $user->getTwoFactor();
        $authyId  = array_get($settings, 'phone.authy_id');
        $url      = $this->api.'/protected/json/verify/'.$token.'/'.$authyId.'?api_key='.$this->key.$force;

        // Verify Authy token
        $response = json_decode($this->http->get($url)->getBody(), true);

        // Authy API returns 'true' as a string, not boolean only at this endpoint
        if (! isset($response['success']) || $response['success'] != 'true') {
            // Fire the Two-Factor verify failed event
            event('rinvex.fort.twofactor.phone.verify.failed', [$user, $response]);

            // Invalid token
            return false;
        }

        // Update user account
        app('rinvex.fort.user')->update($user, [
            'phone_verified'    => true,
            'phone_verified_at' => new Carbon(),
        ]);

        // Fire the Two-Factor verify success event
        event('rinvex.fort.twofactor.phone.verify.success', [$user, $token, $response]);

        // Return true
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AuthenticatableContract $user)
    {
        // Fire the Two-Factor delete start event
        event('rinvex.fort.twofactor.phone.delete.start', [$user]);

        // Prepare required variables
        $settings = $user->getTwoFactor();
        $authyId  = array_get($settings, 'phone.authy_id');
        $url      = $this->api.'/protected/json/users/'.$authyId.'/delete?api_key='.$this->key;

        // Delete Authy user
        $response = json_decode($this->http->post($url)->getBody(), true);

        if (! isset($response['success']) || ! $response['success']) {
            // Fire the Two-Factor verify failed event
            event('rinvex.fort.twofactor.phone.delete.failed', [$user, $response]);

            // Invalid token
            return false;
        }

        // Update user account
        array_set($settings, 'phone', []);
        app('rinvex.fort.user')->update($user, ['two_factor' => $settings]);

        // Fire the Two-Factor delete success event
        event('rinvex.fort.twofactor.phone.delete.success', [$user, $response]);

        // Return true
        return true;
    }
}
