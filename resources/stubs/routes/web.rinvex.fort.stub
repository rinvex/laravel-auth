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

Route::namespace('Frontend')->name('frontend.')->middleware('web')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */

    Route::name('auth.')->prefix('auth')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Login Routes
        |--------------------------------------------------------------------------
        */

        Route::get('login')->name('login')->uses('AuthenticationController@form');
        Route::post('login')->name('login.process')->uses('AuthenticationController@login');
        Route::post('logout')->name('logout')->uses('AuthenticationController@logout');

        /*
        |--------------------------------------------------------------------------
        | Registration Routes
        |--------------------------------------------------------------------------
        */

        Route::get('register')->name('register')->uses('RegistrationController@form');
        Route::post('register')->name('register.process')->uses('RegistrationController@register');

        /*
        |--------------------------------------------------------------------------
        | Social Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::get('github')->name('social.github')->uses('SocialAuthenticationController@redirectToGithub');
        Route::get('github/callback')->name('social.github.callback')->uses('SocialAuthenticationController@handleGithubCallback');
    });

    /*
    |--------------------------------------------------------------------------
    | User Account Routes
    |--------------------------------------------------------------------------
    */

    Route::name('account.')->prefix('account')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Account Page Routes
        |--------------------------------------------------------------------------
        */

        Route::get('settings')->name('settings')->uses('AccountSettingsController@edit');
        Route::post('settings')->name('settings.update')->uses('AccountSettingsController@update');

        /*
        |--------------------------------------------------------------------------
        | Sessions Manipulation Routes
        |--------------------------------------------------------------------------
        */

        Route::get('sessions')->name('sessions')->uses('AccountSessionsController@index');
        Route::delete('sessions/{token?}')->name('sessions.flush')->uses('AccountSessionsController@flush');

        /*
        |--------------------------------------------------------------------------
        | Two-Factor Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::name('twofactor.')->prefix('twofactor')->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Two-Factor TOTP Routes
            |--------------------------------------------------------------------------
            */

            Route::name('totp.')->prefix('totp')->group(function () {
                Route::get('enable')->name('enable')->uses('TwoFactorSettingsController@enableTotp');
                Route::post('update')->name('update')->uses('TwoFactorSettingsController@updateTotp');
                Route::get('disable')->name('disable')->uses('TwoFactorSettingsController@disableTotp');
                Route::get('backup')->name('backup')->uses('TwoFactorSettingsController@backupTotp');
            });

            /*
            |--------------------------------------------------------------------------
            | Two-Factor Phone Routes
            |--------------------------------------------------------------------------
            */

            Route::name('phone.')->prefix('phone')->group(function () {
                Route::get('enable')->name('enable')->uses('TwoFactorSettingsController@enablePhone');
                Route::get('disable')->name('disable')->uses('TwoFactorSettingsController@disablePhone');
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Password Reset Routes
    |--------------------------------------------------------------------------
    */

    Route::name('passwordreset.')->prefix('passwordreset')->group(function () {
        Route::get('request')->name('request')->uses('PasswordResetController@request');
        Route::post('send')->name('send')->uses('PasswordResetController@send');
        Route::get('reset')->name('reset')->uses('PasswordResetController@reset');
        Route::post('process')->name('process')->uses('PasswordResetController@process');
    });

    /*
    |--------------------------------------------------------------------------
    | Verification Routes
    |--------------------------------------------------------------------------
    */

    Route::name('verification.')->prefix('verification')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Phone Verification Routes
        |--------------------------------------------------------------------------
        */

        Route::name('phone.')->prefix('phone')->group(function () {
            Route::get('request')->name('request')->uses('PhoneVerificationController@request');
            Route::post('send')->name('send')->uses('PhoneVerificationController@send');
            Route::get('verify')->name('verify')->uses('PhoneVerificationController@verify');
            Route::post('process')->name('process')->uses('PhoneVerificationController@process');
        });

        /*
        |--------------------------------------------------------------------------
        | Email Verification Routes
        |--------------------------------------------------------------------------
        */

        Route::name('email.')->prefix('email')->group(function () {
            Route::get('request')->name('request')->uses('EmailVerificationController@request');
            Route::post('send')->name('send')->uses('EmailVerificationController@send');
            Route::get('verify')->name('verify')->uses('EmailVerificationController@verify');
        });
    });
});


Route::namespace('Backend')->name('backend.')->prefix('backend')->middleware(['web', 'can:access-dashboard'])->group(function () {
    Route::get('/')->name('dashboard.home')->uses('DashboardController@home');

    /*
    |--------------------------------------------------------------------------
    | Abilities Routes
    |--------------------------------------------------------------------------
    */

    Route::name('abilities.')->prefix('abilities')->group(function () {
        Route::get('/')->name('index')->uses('AbilitiesController@index');
        Route::get('create')->name('create')->uses('AbilitiesController@create');
        Route::post('create')->name('store')->uses('AbilitiesController@store');
        Route::get('{ability}')->name('edit')->uses('AbilitiesController@edit')->where('ability', '[0-9]+');
        Route::put('{ability}')->name('update')->uses('AbilitiesController@update')->where('ability', '[0-9]+');
        Route::delete('{ability}')->name('delete')->uses('AbilitiesController@delete')->where('ability', '[0-9]+');
    });

    /*
    |--------------------------------------------------------------------------
    | Roles Routes
    |--------------------------------------------------------------------------
    */

    Route::name('roles.')->prefix('roles')->group(function () {
        Route::get('/')->name('index')->uses('RolesController@index');
        Route::get('create')->name('create')->uses('RolesController@create');
        Route::post('create')->name('store')->uses('RolesController@store');
        Route::get('{role}')->name('edit')->uses('RolesController@edit')->where('role', '[0-9]+');
        Route::put('{role}')->name('update')->uses('RolesController@update')->where('role', '[0-9]+');
        Route::delete('{role}')->name('delete')->uses('RolesController@delete')->where('role', '[0-9]+');
    });

    /*
    |--------------------------------------------------------------------------
    | Users Routes
    |--------------------------------------------------------------------------
    */

    Route::name('users.')->prefix('users')->group(function () {
        Route::get('/')->name('index')->uses('UsersController@index');
        Route::get('create')->name('create')->uses('UsersController@create');
        Route::post('create')->name('store')->uses('UsersController@store');
        Route::get('{user}')->name('edit')->uses('UsersController@edit')->where('user', '[0-9]+');
        Route::put('{user}')->name('update')->uses('UsersController@update')->where('user', '[0-9]+');
        Route::delete('{user}')->name('delete')->uses('UsersController@delete')->where('user', '[0-9]+');
    });
});
