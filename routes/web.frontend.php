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

/*
|--------------------------------------------------------------------------
| Rinvex Fort Routes
|--------------------------------------------------------------------------
|
| Here you can see all authentication, authorization, and verification
| routes. It's automatically loaded in the package's service provider.
|
*/

Route::group([
    'middleware' => 'web',
    'as'         => 'rinvex.fort.frontend.',
    'namespace'  => 'Rinvex\Fort\Http\Controllers\Frontend',
], function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'auth.', 'prefix' => 'auth'], function () {

        /*
        |--------------------------------------------------------------------------
        | Login Routes
        |--------------------------------------------------------------------------
        */

        Route::get('login', ['as' => 'login', 'uses' => 'AuthenticationController@showLogin']);
        Route::post('login', ['as' => 'login.post', 'uses' => 'AuthenticationController@processLogin']);
        Route::post('logout', ['as' => 'logout', 'uses' => 'AuthenticationController@logout']);

        /*
        |--------------------------------------------------------------------------
        | Registration Routes
        |--------------------------------------------------------------------------
        */

        Route::get('register', ['as' => 'register', 'uses' => 'RegistrationController@showRegisteration']);
        Route::post('register', ['as' => 'register.post', 'uses' => 'RegistrationController@processRegisteration']);

        /*
        |--------------------------------------------------------------------------
        | Social Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::get('github', ['as' => 'social.github', 'uses' => 'SocialAuthenticationController@redirectToGithub']);
        Route::get('github/callback', ['as' => 'social.github.callback', 'uses' => 'SocialAuthenticationController@handleGithubCallback']);
    });


    /*
    |--------------------------------------------------------------------------
    | User Account Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'user.', 'prefix' => 'user'], function () {

        /*
        |--------------------------------------------------------------------------
        | Account Page Routes
        |--------------------------------------------------------------------------
        */

        Route::get('settings', ['as' => 'settings', 'uses' => 'UserSettingsController@edit']);
        Route::post('settings', ['as' => 'settings.update', 'uses' => 'UserSettingsController@update']);

        /*
        |--------------------------------------------------------------------------
        | Sessions Manipulation Routes
        |--------------------------------------------------------------------------
        */

        Route::get('sessions', ['as' => 'sessions', 'uses' => 'UserSessionsController@index']);
        Route::post('sessions', ['as' => 'sessions.flush', 'uses' => 'UserSessionsController@flush']);

        /*
        |--------------------------------------------------------------------------
        | Two-Factor Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::group(['as' => 'twofactor.', 'prefix' => 'twofactor'], function () {

            /*
            |--------------------------------------------------------------------------
            | Two-Factor TOTP Routes
            |--------------------------------------------------------------------------
            */

            Route::group(['as' => 'totp.', 'prefix' => 'totp'], function () {
                Route::get('enable', ['as' => 'enable', 'uses' => 'TwoFactorSettingsController@enableTotp']);
                Route::post('update', ['as' => 'update', 'uses' => 'TwoFactorSettingsController@updateTotp']);
                Route::get('disable', ['as' => 'disable', 'uses' => 'TwoFactorSettingsController@disableTotp']);
                Route::get('backup', ['as' => 'backup', 'uses' => 'TwoFactorSettingsController@backupTotp']);
            });

            /*
            |--------------------------------------------------------------------------
            | Two-Factor Phone Routes
            |--------------------------------------------------------------------------
            */

            Route::group(['as' => 'phone.', 'prefix' => 'phone'], function () {
                Route::get('enable', ['as' => 'enable', 'uses' => 'TwoFactorSettingsController@enablePhone']);
                Route::get('disable', ['as' => 'disable', 'uses' => 'TwoFactorSettingsController@disablePhone']);
            });
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Password Reset Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'passwordreset.', 'prefix' => 'passwordreset'], function () {
        Route::get('request', ['as' => 'request', 'uses' => 'PasswordResetController@request']);
        Route::post('send', ['as' => 'send', 'uses' => 'PasswordResetController@send']);
        Route::get('reset', ['as' => 'reset', 'uses' => 'PasswordResetController@reset']);
        Route::post('process', ['as' => 'process', 'uses' => 'PasswordResetController@process']);
    });


    /*
    |--------------------------------------------------------------------------
    | Verification Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'verification.', 'prefix' => 'verification'], function () {

        /*
        |--------------------------------------------------------------------------
        | Phone Verification Routes
        |--------------------------------------------------------------------------
        */

        Route::group(['as' => 'phone.', 'prefix' => 'phone'], function () {
            Route::get('request', ['as' => 'request', 'uses' => 'PhoneVerificationController@request']);
            Route::post('send', ['as' => 'send', 'uses' => 'PhoneVerificationController@send']);
            Route::get('verify', ['as' => 'verify', 'uses' => 'PhoneVerificationController@verify']);
            Route::post('process', ['as' => 'process', 'uses' => 'PhoneVerificationController@process']);
        });

        /*
        |--------------------------------------------------------------------------
        | Email Verification Routes
        |--------------------------------------------------------------------------
        */

        Route::group(['as' => 'email.', 'prefix' => 'email'], function () {
            Route::get('request', ['as' => 'request', 'uses' => 'EmailVerificationController@request']);
            Route::post('send', ['as' => 'send', 'uses' => 'EmailVerificationController@send']);
            Route::get('verify', ['as' => 'verify', 'uses' => 'EmailVerificationController@verify']);
        });
    });
});
