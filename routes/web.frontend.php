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

    Route::group(['as' => 'account.', 'prefix' => 'account'], function () {

        /*
        |--------------------------------------------------------------------------
        | Account Page Routes
        |--------------------------------------------------------------------------
        */

        Route::get('page', ['as' => 'page', 'uses' => 'ProfileUpdateController@showProfileUpdate']);
        Route::post('page', ['as' => 'page.post', 'uses' => 'ProfileUpdateController@processProfileUpdate']);

        /*
        |--------------------------------------------------------------------------
        | Sessions Manipulation Routes
        |--------------------------------------------------------------------------
        */

        Route::get('sessions', ['as' => 'sessions', 'uses' => 'ManagePersistenceController@showPersistence']);
        Route::get('sessions/flush/{token}', ['as' => 'sessions.flush', 'uses' => 'ManagePersistenceController@processPersistenceFlush'])->where('token', '[0-9a-zA-Z]+');
        Route::get('sessions/flushall', ['as' => 'sessions.flushall', 'uses' => 'ManagePersistenceController@processPersistenceFlush']);

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
                Route::get('enable', ['as' => 'enable', 'uses' => 'TwoFactorUpdateController@showTwoFactorTotpEnable']);
                Route::post('enable', ['as' => 'enable.post', 'uses' => 'TwoFactorUpdateController@processTwoFactorTotpEnable']);
                Route::get('disable', ['as' => 'disable', 'uses' => 'TwoFactorUpdateController@processTwoFactorTotpDisable']);
                Route::get('backup', ['as' => 'backup', 'uses' => 'TwoFactorUpdateController@processTwoFactorTotpBackup']);
            });

            /*
            |--------------------------------------------------------------------------
            | Two-Factor Phone Routes
            |--------------------------------------------------------------------------
            */

            Route::group(['as' => 'phone.', 'prefix' => 'phone'], function () {
                Route::get('enable', ['as' => 'enable', 'uses' => 'TwoFactorUpdateController@processTwoFactorPhoneEnable']);
                Route::get('disable', ['as' => 'disable', 'uses' => 'TwoFactorUpdateController@processTwoFactorPhoneDisable']);
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
