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

use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;
use Rinvex\Fort\Contracts\AuthenticatableContract;
use Rinvex\Fort\Contracts\TwoFactorProviderContract;
use Rinvex\Fort\Contracts\TwoFactorSmsTokenContract;
use Rinvex\Fort\Contracts\TwoFactorPhoneTokenContract;

class TwoFactorAuthyProvider implements TwoFactorProviderContract, TwoFactorSmsTokenContract, TwoFactorPhoneTokenContract
{
    /**
     * Array containing configuration data.
     *
     * @var array
     */
    private $config;

    /**
     * Create a new Authy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $mode                    = config('rinvex.fort.twofactor.authy.mode');
        $this->config['api_key'] = config('rinvex.fort.twofactor.authy.keys.'.$mode);
        $this->config['api_url'] = $mode === 'sandbox' ? 'http://sandbox-api.authy.com' : 'https://api.authy.com';
    }

    /**
     * {@inheritdoc}
     */
    public function sendSmsToken(AuthenticatableContract $user, $force = true)
    {
        try {
            // Fire the Two-Factor phone sms start event
            event('rinvex.fort.twofactor.phone.sms.start', [$user]);

            // Prepare required data
            $force        = $force ? '&force=true' : '';
            $twoFactorSms = array_get($user->getTwoFactor(), 'phone');
            $authyId      = $twoFactorSms['authy_id'];
            $apiKey       = $this->config['api_key'];
            $url          = $this->config['api_url'].'/protected/json/sms/'.$authyId.'?api_key='.$apiKey.$force;

            // Send SMS auth token
            if (($response = json_decode((new HttpClient())->get($url)->getBody(), true)) && $response['success']) {
                // Fire the Two-Factor phone sms success event
                event('rinvex.fort.twofactor.phone.sms.success', [$user, $response]);

                return true;
            }

            // Fire the Two-Factor phone sms failed event
            event('rinvex.fort.twofactor.phone.sms.failed', [$user, $response]);

            // Log failed response
            Log::alert(json_encode($response));

            return false;
        } catch (Exception $e) {
            // Log exceptions & fatal errors
            Log::alert($e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendPhoneCallToken(AuthenticatableContract $user, $force = true)
    {
        try {
            // Fire the Two-Factor phone call start event
            event('rinvex.fort.twofactor.phone.call.start', [$user]);

            // Prepare required data
            $force        = $force ? '&force=true' : '';
            $twoFactorSms = array_get($user->getTwoFactor(), 'phone');
            $authyId      = $twoFactorSms['authy_id'];
            $apiKey       = $this->config['api_key'];
            $url          = $this->config['api_url'].'/protected/json/call/'.$authyId.'?force=true&api_key='.$apiKey.$force;

            // Send SMS auth token
            if (($response = json_decode((new HttpClient())->get($url)->getBody(), true)) && $response['success']) {
                // Fire the Two-Factor phone call success event
                event('rinvex.fort.twofactor.phone.call.success', [$user, $response]);

                return true;
            }

            // Fire the Two-Factor phone call failed event
            event('rinvex.fort.twofactor.phone.call.failed', [$user, $response]);

            // Log failed response
            Log::alert(json_encode($response));

            return false;
        } catch (Exception $e) {
            // Log exceptions & fatal errors
            Log::alert($e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register(AuthenticatableContract $user)
    {
        try {
            // Fire the Two-Factor register start event
            event('rinvex.fort.twofactor.phone.register.start', [$user]);

            // Register given user with authy and get response
            $url      = $this->config['api_url'].'/protected/json/users/new?api_key='.$this->config['api_key'];
            $response = json_decode((new HttpClient())->post($url, [
                'form_params' => [
                    'user' => [
                        'email'        => $user->getEmailForTwoFactorAuth(),
                        'cellphone'    => preg_replace('/[^0-9]/', '', $user->getPhoneForTwoFactorAuth()),
                        'country_code' => $user->getCountryCodeForTwoFactorAuth(),
                    ],
                ],
            ])->getBody(), true);

            if ($response['success'] && $response['user']['id']) {
                // Prepare required data
                $settings                 = $user->getTwoFactor();
                $twoFactorSms             = array_get($settings, 'phone');
                $twoFactorSms['authy_id'] = $response['user']['id'];

                array_set($settings, 'phone', $twoFactorSms);

                // Update user account
                app('rinvex.fort.user')->update($user->id, [
                    'two_factor' => $settings,
                ]);

                // Fire the Two-Factor register success event
                event('rinvex.fort.twofactor.phone.register.success', [$user, $response]);

                return true;
            }

            // Fire the Two-Factor register failed event
            event('rinvex.fort.twofactor.phone.register.failed', [$user, $response]);

            // Log failed response
            Log::alert(json_encode($response));

            return false;
        } catch (Exception $e) {
            // Log exceptions & fatal errors
            Log::alert($e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function tokenIsValid(AuthenticatableContract $user, $token, $force = true)
    {
        try {
            // Fire the Two-Factor phone verify start event
            event('rinvex.fort.twofactor.phone.verify.start', [$user, $token]);

            // Prepare required data
            $force        = $force ? '&force=true' : '';
            $twoFactorSms = array_get($user->getTwoFactor(), 'phone');
            $authyId      = $twoFactorSms['authy_id'];
            $apiKey       = $this->config['api_key'];
            $url          = $this->config['api_url'].'/protected/json/verify/'.$token.'/'.$authyId.'?force=true&api_key='.$apiKey.$force;

            // Send SMS auth token
            $response = json_decode((new HttpClient())->get($url)->getBody(), true);

            // Authy API returns 'true' as a string not a boolean only at this endpoint!
            if ($response['success'] === 'true') {
                app('rinvex.fort.user')->update($user->id, [
                    'phone_verified'    => true,
                    'phone_verified_at' => new Carbon(),
                ]);

                // Fire the Two-Factor phone verify success event
                event('rinvex.fort.twofactor.phone.verify.success', [$user, $token]);

                return true;
            }

            // Fire the Two-Factor phone verify failed event
            event('rinvex.fort.twofactor.phone.verify.failed', [$user, $token]);

            // Log failed response
            Log::alert(json_encode($response));

            return false;
        } catch (Exception $e) {
            // Log exceptions & fatal errors
            Log::alert($e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AuthenticatableContract $user)
    {
        try {
            // Fire the Two-Factor phone delete start event
            event('rinvex.fort.twofactor.phone.delete.start', [$user]);

            // Prepare required data
            $settings     = $user->getTwoFactor();
            $twoFactorSms = array_get($settings, 'phone');
            $authyId      = $twoFactorSms['authy_id'];
            $apiKey       = $this->config['api_key'];
            $url          = $this->config['api_url'].'/protected/json/users/delete/'.$authyId.'?api_key='.$apiKey;

            // Send SMS auth token
            $response = json_decode((new HttpClient())->post($url)->getBody(), true);

            array_set($settings, 'phone', []);

            if ($response['success']) {
                // Update user account
                app('rinvex.fort.user')->update($user->id, [
                    'two_factor' => $settings,
                ]);

                // Fire the Two-Factor phone delete success event
                event('rinvex.fort.twofactor.phone.delete.success', [$user, $response]);

                return true;
            }

            // Fire the Two-Factor delete failed event
            event('rinvex.fort.twofactor.phone.delete.failed', [$user, $response]);

            // Log failed response
            Log::alert(json_encode($response));

            return false;
        } catch (Exception $e) {
            // Log exceptions & fatal errors
            Log::alert($e->getMessage());

            return false;
        }
    }
}
