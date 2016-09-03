# Rinvex Fort [WIP]

**Rinvex Fort** is a powerful authentication, authorization and verification package built on top of Laravel. It provides developers with Role Based Access Control, Two-Factor Authentication, Social Authentication, compatible with Laravel’s standard API and fully featured all-in-one solution out of the box.

[![Packagist](https://img.shields.io/packagist/v/rinvex/fort.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/rinvex/fort)
[![License](https://img.shields.io/packagist/l/rinvex/fort.svg?label=License&style=flat-square)](https://github.com/rinvex/fort/blob/develop/LICENSE)
[![VersionEye Dependencies](https://img.shields.io/versioneye/d/php/rinvex:fort.svg?label=Dependencies&style=flat-square)](https://www.versioneye.com/php/rinvex:fort/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/rinvex/fort.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/rinvex/fort/)
[![Code Climate](https://img.shields.io/codeclimate/github/rinvex/fort.svg?label=CodeClimate&style=flat-square)](https://codeclimate.com/github/rinvex/fort)
[![StyleCI](https://styleci.io/repos/66008159/shield)](https://styleci.io/repos/66008159)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/e361e7c2-c0ae-469d-8a53-6a2433e7aaad.svg?label=SensioLabs&style=flat-square)](https://insight.sensiolabs.com/projects/e361e7c2-c0ae-469d-8a53-6a2433e7aaad)


## Quick Installation Guide

1. Install through `composer require rinvex/fort`
2. Open `app/Exceptions/Handler.php` and do the following:

    ```php
    // Replace this:
    if ($request->expectsJson()) {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    return redirect()->guest('login');
    
    // With this:
    return intend([
        'intended'   => route('rinvex.fort.auth.login'),
        'withErrors' => ['rinvex.fort.session.required' => Lang::get('rinvex.fort::message.auth.session.required')],
    ], 401);
    ```
    
    In the same file `app/Exceptions/Handler.php`:
    ```php
    // Search for the following line:
    return parent::render($request, $exception);
 
    // Add above it directly the following code:
    if ($exception instanceof \Rinvex\Fort\Exceptions\InvalidPersistenceException) {
        return intend([
            'intended'   => route('rinvex.fort.auth.login'),
            'withErrors' => ['rinvex.fort.session.expired' => Lang::get('rinvex.fort::message.auth.session.expired')],
        ], 401);
    }
    ```

2. Open `app/Http/Kernel.php` and do the following:

    ```php
    // Replace this:
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    
    // With this:
    'guest' => \Rinvex\Fort\Http\Middleware\RedirectIfAuthenticated::class,
    ```

    In the same file `app/Http/Kernel.php`:
    ```php
    // Add this to the end of '$routeMiddleware' array
    'abilities' => \Rinvex\Fort\Http\Middleware\Abilities::class,
    ```

3. Open `config/app.php` and add the following line to the end of `providers` array:

    ```php
    Rinvex\Fort\Providers\FortServiceProvider::class,
    ```

4. Execute migrations:

    ```shell
    php artisan migrate --path="vendor/rinvex/fort/database/migrations"
    ```

5. Done!
    **If you know anyway to make installation simpler or avoid some steps, please send a PR.**


## Quick Walk-Through

### Login

**Route URI:** `/auth/login`

**Route name:** `rinvex.fort.auth.login` 

```php
\Rinvex\Fort\Http\Controllers\AuthenticationController::showLogin
````
![Rinvex Fort - Login](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/login.png)

### Logout

**Route URI:** `/auth/logout`

**Route name:** `rinvex.fort.auth.logout` 

```php
\Rinvex\Fort\Http\Controllers\AuthenticationController::logout
```

### Register

**Route URI:** `/auth/register`

**Route name:** `rinvex.fort.auth.register` 

```php
\Rinvex\Fort\Http\Controllers\AuthenticationController::showRegisteration
```
![Rinvex Fort - Register](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/register.png)

### User Account

**Route URI:** `/account/page`

**Route name:** `rinvex.fort.account.page` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::showAccountUpdate
```
![Rinvex Fort - Profile](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/profile.png)

### Active Sessions

**Route URI:** `/account/sessions`

**Route name:** `rinvex.fort.account.sessions` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::showAccountSessions
```
![Rinvex Fort - Active Sessions](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/sessions.png)

### Flush Selected Session

**Route URI:** `/account/sessions/flush/{token}`

**Route name:** `rinvex.fort.account.sessions.flush` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::processSessionFlush
```
### Flush All Active Sessions

**Route URI:** `/account/sessions/flushall`

**Route name:** `rinvex.fort.account.sessions.flushall` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::processSessionFlush
```
![Rinvex Fort - Flush All Sessions](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/flushall-sessions.png)

### Enable Two-Factor TOTP

**Route URI:** `/account/twofactor/totp/enable`

**Route name:** `rinvex.fort.account.twofactor.totp.enable` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::showTwoFactorTotpEnable
```
![Rinvex Fort - Two-Factor TOTP](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/twofactor-totp.png)

### Disable Two-Factor TOTP

**Route URI:** `/account/twofactor/totp/disable`

**Route name:** `rinvex.fort.account.twofactor.totp.disable` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::processTwoFactorTotpDisable
```
### Backup Two-Factor TOTP

**Route URI:** `/account/twofactor/totp/backup`

**Route name:** `rinvex.fort.account.twofactor.totp.backup` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::processTwoFactorTotpBackup
```
![Rinvex Fort - Two-Factor Backup](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/twofactor-backup.png)

### Enable Two-Factor Phone

**Route URI:** `/account/twofactor/phone/enable`

**Route name:** `rinvex.fort.account.twofactor.phone.enable` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::processTwoFactorPhoneEnable
```
### Disable Two-Factor Phone

**Route URI:** `/account/twofactor/phone/disable`

**Route name:** `rinvex.fort.account.twofactor.phone.disable` 

```php
\Rinvex\Fort\Http\Controllers\AccountController::processTwoFactorPhoneDisable
```
### Request Password Reset

**Route URI:** `/password/request`

**Route name:** `rinvex.fort.password.request` 

```php
\Rinvex\Fort\Http\Controllers\ResetterController::showPasswordResetRequest
```
![Rinvex Fort - Request Password Reset](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/request-password-reset.png)

### Password Reset

**Route URI:** `/password/reset`

**Route name:** `rinvex.fort.password.reset` 

```php
\Rinvex\Fort\Http\Controllers\ResetterController::showPasswordReset
```
![Rinvex Fort - Password Reset](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/password-reset.png)

### Request Email Verification

**Route URI:** `/verification/email`

**Route name:** `rinvex.fort.verification.email` 

```php
\Rinvex\Fort\Http\Controllers\VerificationController::showEmailVerificationRequest
```
![Rinvex Fort - Request Email Verification](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/request-email-verification.png)

### Email Verification

**Route URI:** `/verification/email/verify`

**Route name:** `rinvex.fort.verification.email.verify` 

```php
\Rinvex\Fort\Http\Controllers\VerificationController::processEmailVerification
```

### Request Phone Verification

**Route URI:** `/verification/phone`

**Route name:** `rinvex.fort.verification.phone` 

```php
\Rinvex\Fort\Http\Controllers\VerificationController::showPhoneVerificationRequest
```
![Rinvex Fort - Request Phone Verification](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/request-phone-verification.png)

### Phone Verification

**Route URI:** `/verification/phone/verify`

**Route name:** `rinvex.fort.verification.phone.verify` 

```php
\Rinvex\Fort\Http\Controllers\VerificationController::showPhoneVerification
```
![Rinvex Fort - Phone Verification](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/phone-verification.png)

### Alert Messages

![Rinvex Fort - Success Alert](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/alert-success.png)

![Rinvex Fort - Warning Alert](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/alert-warning.png)

![Rinvex Fort - Failure Alert](https://rinvex.com/assets/frontend/layout/img/products/rinvex-fort/alert-failure.png)


## Features

- **Default Laravel API**
- Multiple login columns
- Single/Multiple sessions
- Create Users, Roles, Abilities
- Request password reset via email
- Grant/Revoke Abilities to Users/Roles
- Assign/Remove Roles to Users (multiple roles allowed)
- Save Intended URL for later redirect after login/register
- Request phone verification via SMS or automated phone call
- Alert messages with every completed action (success/warning/failure)
- Complete Solution out of the box, views, routes, controllers, migrations, ..etc
- Listen to any triggered events at any process (with almost 65+ listened to events)
- Use any of Google Authenticator, Duo Mobile, Authy, or Windows Phone Authenticator for Two-Factor TOTP Authentication
- Social login using Facebook, Google, Twitter, LinkedIn, Github, Bitbucket
- Automatically logout user if his session has been tampered
- Customizable table database table and model names
- Database stored role based access control
- Uses Laravel 5.3 Notifications System

- Registration
    - Require email verification
    - Enable/Disable registrations
    - Set default role for new registrations
    - Moderate new registrations

- Authentication
    - **Uses default Laravel authentication mechanisms**
    - Login using username or email
    - Simple registration process
    - Two-Factor Authentication
    - Login throttling
    - Social Authentication via Socialite
    - Time-based One-time Password Authentication
    - SMS based Two-Factor Authentication via Authy/Twilio

- Authorization
    - **Uses default Laravel Abilities & Gate authorization**
    - Role Based Access Control (RBAC)
    - Assign Abilities to Roles and/or Users
    - Restrict access for certain areas to specific roles/users/abilities

- Verification
    - Email Verification through email message
    - Phone Verification through SMS or automated phone call

- Email Notifications
    - Send welcome email after registration
    - Send success email after verification
    - Send notification email after lockout

- User Profile
    - Manage account active sessions through persistence managment console
    - Manage profile data (first name, middle name, last name, username, email, country, phone, ..etc)


## Usage

### Authentication

While this package complies almost in every way with default Laravel authentication techniques, and considered to be fully compatible with the standard API, it provides some extra features on top of it which we'll spot the light on here, but first we must review [Laravel Authentication Documentation](https://laravel.com/docs/5.3/authentication) as it's the core foundation.

- This package overrides the default `session` guard with another instance in the **same name**, that's why it's done automatically behind scenes without changing any config or code manually.
- Also this package overrides the default eloquent user provider with a custom `eloquent` provider, again with the **same name** so it's been replaced implicitely to take effect on default Laravel installations.
- The new `session` guard shipped with this package checks user's state, if it's moderated then it won't be able to login. Reference: `\Rinvex\Fort\Guards\SessionGuard::login`
- You've two persistence modes, `single` and `multiple` that you can set in the config options. If it's `single` then users won't be able to login through multiple devices at the same time, since the last login will invalidate all other active sessions.
- Every login attempt is checked for verified email address, if it's not verified then it fails. Also it checks if Two-Factor authentication is enabled or not, if enabled it will redirect back to Two-Factor authentication form that's required before proceeding.

### Authorization

Just like authentication, the authorization part is almost identical to the default Laravel one with an extra layer of additional features builds on the standard API, so it's mandatory to review first [Laravel Authorization Documentation](https://laravel.com/docs/5.3/authorization) as it's the core foundation.

- This package extends Laravel's gate to allow you to save abilities and roles in a database, and thus you get full benefit of using the intutive and powerful Laravel authorization techniches while still having the luxury of saving your ACL dynamically in a database.
- While Laravel doesn't provide the concept of user roles out of the box, this package adds this important dimention to the equation for a better user management system through a proper Role Based Access Control.
- You can create abilities, which is stored in the database, grant these abilities to roles, or to users directly, assign roles to users, and set certain level or access on specific areas so that users that don't have appropriate abilities to access it, won't be able to go through.

### Verification

#### Email Verification

- Email verification could be enabled or disabled through config options, and when enabled users must verify their emails before being able to login.
- If the user changed email address, the previous email verification will be invalidated, and another validation process has to be gone though; Otherwise it won't be possible to re-login again after session expires without such email re-verification.

#### Phone Verification

- Phone verification is optional unless the user wants to activate Two-Factor phone authentication, in such case it's required and mandatory.
- If the user changed profile's country or phone number, the Two-Factor phone authentication will be automatically disabled, and his previous phone verification will be invalidated, so it's required to verify phone again, and re-enable Two-Factor phone authentication manually.


## Notifications Sent

- Email Verification
- Authentication Lockout
- Email Verification Success
- Password Reset Request
- Registration Success


## Config Options

Reading through the configuration options will make it much clear for you how this package works, and what various features you've at your fingertips. While it's shipped with the defaults to plug-and-play, you still able to customize whatever you want as you like:

```php
use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Rinvex\Fort\Models\Ability;
use Rinvex\Fort\Models\Socialite;
use Rinvex\Fort\Models\Persistence;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication and Authorization Models
    |--------------------------------------------------------------------------
    */

    'models' => [

        /*
        |--------------------------------------------------------------------------
        | Ability Model
        |--------------------------------------------------------------------------
        |
        | Specify your Eloquent model that should be used to retrieve your abilities.
        | Of course, it is often just the "Ability" model but you may use whatever
        | you like. The model you want to use as an ability model must extend
        | the `Rinvex\Fort\Models\Ability` base model.
        |
        | Default: Rinvex\Fort\Models\Ability::class
        |
        */

        'ability' => Ability::class,

        /*
        |--------------------------------------------------------------------------
        | Role Model
        |--------------------------------------------------------------------------
        |
        | Specify your Eloquent model that should be used to retrieve your roles.
        | Of course, it is often just the "Role" model but you may use whatever
        | you like. The model you want to use as a role model must extend
        | the `Rinvex\Fort\Models\Role` base model.
        |
        | Default: Rinvex\Fort\Models\Role::class
        |
        */

        'role' => Role::class,

        /*
        |--------------------------------------------------------------------------
        | User Model
        |--------------------------------------------------------------------------
        |
        | Specify your Eloquent model that should be used to retrieve your users.
        | Of course, it is often just the "User" model but you may use whatever
        | you like. The model you want to use as a user model must extend
        | the `Rinvex\Fort\Models\User` base model.
        |
        | Default: Rinvex\Fort\Models\User::class
        |
        */

        'user' => User::class,

        /*
        |--------------------------------------------------------------------------
        | Persistence Model
        |--------------------------------------------------------------------------
        |
        | Specify your Eloquent model that should be used to retrieve user persistences.
        | Of course, it is often just the "Persistence" model but you may use whatever
        | you like. The model you want to use as a persistence model must extend
        | the `Rinvex\Fort\Models\Persistence` base model.
        |
        | Default: Rinvex\Fort\Models\Persistence::class
        |
        */

        'persistence' => Persistence::class,

        /*
        |--------------------------------------------------------------------------
        | Socialite Model
        |--------------------------------------------------------------------------
        |
        | Specify your Eloquent model that should be used to retrieve user socialites.
        | Of course, it is often just the "Socialite" model but you may use whatever
        | you like. The model you want to use as a socialite model must extend
        | the `Rinvex\Fort\Models\Socialite` base model.
        |
        | Default: Rinvex\Fort\Models\Socialite::class
        |
        */

        'socialite' => Socialite::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication and Authorization Database Tables
    |--------------------------------------------------------------------------
    */

    'tables' => [

        /*
        |--------------------------------------------------------------------------
        | Abilities Table
        |--------------------------------------------------------------------------
        |
        | Specify your database table name that should be used to store
        | your abilities. You may use whatever you like.
        |
        | Default: "rinvex_fort_abilities"
        |
        */

        'abilities' => 'rinvex_fort_abilities',

        /*
        |--------------------------------------------------------------------------
        | Roles Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store
        | your roles. You may use whatever you like.
        |
        | Default: "rinvex_fort_roles"
        |
        */

        'roles' => 'rinvex_fort_roles',

        /*
        |--------------------------------------------------------------------------
        | Users Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store
        | your users. You may use whatever you like.
        |
        | Default: "rinvex_fort_users"
        |
        */

        'users' => 'rinvex_fort_users',

        /*
        |--------------------------------------------------------------------------
        | User Abilities Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store the relation
        | between "users" and "abilities". You may use whatever you like.
        |
        | Default: "rinvex_fort_ability_user"
        |
        */

        'ability_user' => 'rinvex_fort_ability_user',

        /*
        |--------------------------------------------------------------------------
        | User Roles Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store the relation
        | between "users" and "roles". You may use whatever you like.
        |
        | Default: "rinvex_fort_role_user"
        |
        */

        'role_user' => 'rinvex_fort_role_user',


        /*
        |--------------------------------------------------------------------------
        | Role Abilities Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store the relation
        | between "roles" and "abilities". You may use  whatever you like.
        |
        | Default: "rinvex_fort_ability_role"
        |
        */

        'ability_role' => 'rinvex_fort_ability_role',

        /*
        |--------------------------------------------------------------------------
        | Verifications Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store the
        | verification tokens. You may use whatever you like.
        |
        | Default: "rinvex_fort_verifications"
        |
        */

        'verifications' => 'rinvex_fort_verifications',

        /*
        |--------------------------------------------------------------------------
        | Reset Password Tokens Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store the
        | reset password tokens. You may use whatever you like.
        |
        | Default: "rinvex_fort_resets"
        |
        */

        'resets' => 'rinvex_fort_resets',

        /*
        |--------------------------------------------------------------------------
        | Persistences Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store the
        | user persistences. You may use whatever you like.
        |
        | Default: "rinvex_fort_persistences"
        |
        */

        'persistences' => 'rinvex_fort_persistences',

        /*
        |--------------------------------------------------------------------------
        | Socialite Table
        |--------------------------------------------------------------------------
        |
        | Specify database table name that should be used to store the
        | user social accounts. You may use whatever you like.
        |
        | Default: "rinvex_fort_socialite"
        |
        */

        'socialite' => 'rinvex_fort_socialite',

    ],

    /*
    |--------------------------------------------------------------------------
    | User Registration
    |--------------------------------------------------------------------------
    |
    */

    'registration' => [

        /*
        |--------------------------------------------------------------------------
        | User Registration
        |--------------------------------------------------------------------------
        |
        | This determines whether to allow user registration or not.
        |
        | Supported: true, false
        |
        | Default: true
        |
        */

        'enabled' => true,

        /*
        |--------------------------------------------------------------------------
        | User Registration Moderation
        |--------------------------------------------------------------------------
        |
        | This determines whether to moderate new user registrations or not.
        | When moderated, new registrations set as 'moderated' until admin approval.
        |
        | Supported: true, false
        |
        | Default: true
        |
        */

        'moderated' => false,

        /*
        |--------------------------------------------------------------------------
        | Default User Registration Role
        |--------------------------------------------------------------------------
        |
        | You may specify here default role to be assigned for newly registered users.
        |
        | Default: 'registered'
        |
        */

        'default_role' => 'registered',

        /*
        |--------------------------------------------------------------------------
        | Send Welcome Email
        |--------------------------------------------------------------------------
        |
        | Send welcome email to users upon registration success.
        |
        */

        'welcome_email' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Session Persistence
    |--------------------------------------------------------------------------
    |
    | This determines session persistence mode. Single persistence means
    | user can NOT login more than once in multiple browsers at the
    | same time. Recent login is the only kept active session.
    |
    | Supported: "single", "multiple"
    |
    | Default: "multiple"
    |
    */

    'persistence' => 'multiple',

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Here you may set the options for resetting passwords including the view
    | that is your password reset e-mail. You may also set the name of the
    | table that maintains all of the reset tokens for your application.
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'reset' => [

        /*
        |--------------------------------------------------------------------------
        | Password Reset Default Broker
        |--------------------------------------------------------------------------
        |
        | This option controls the default reset password broker for your
        | application. You may change this default as required, but
        | they're a perfect start for most applications.
        |
        | Specify here reset password broker used to manage password resets.
        |
        | Supported: "users"
        |
        */

        'broker' => 'users',

        /*
        |--------------------------------------------------------------------------
        | Password Reset Broker Configuration
        |--------------------------------------------------------------------------
        |
        | Here you may configure password reset broker.
        |
        */

        'users' => [
            'provider' => 'users',
            'expire'   => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    'verification' => [

        /*
        |--------------------------------------------------------------------------
        | User Verification Requirement
        |--------------------------------------------------------------------------
        |
        | Here you may determine whether user verification required or not.
        |
        | Supported: true, false
        |
        | Default: true
        |
        */

        'required' => true,

        /*
        |--------------------------------------------------------------------------
        | Verification Default Broker
        |--------------------------------------------------------------------------
        |
        | This option controls the default verification broker for your
        | application. You may change this default as required, but
        | they're a perfect start for most applications.
        |
        | Specify here verification broker used to manage verifications.
        |
        | Supported: "users"
        |
        */

        'broker' => 'users',

        /*
        |--------------------------------------------------------------------------
        | Email Verification Broker Configuration
        |--------------------------------------------------------------------------
        |
        | Here you may configure email verification broker.
        |
        */

        'users' => [
            'provider' => 'users',
            'expire'   => 60,
        ],

        /*
        |--------------------------------------------------------------------------
        | Send Verification Success Email
        |--------------------------------------------------------------------------
        |
        | Send verification success email to users upon completing email verification successfully.
        |
        */

        'success_email' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Login Throttling
    |--------------------------------------------------------------------------
    */

    'throttle' => [

        /*
        |--------------------------------------------------------------------------
        | Login Throttling Enabled
        |--------------------------------------------------------------------------
        |
        | Specify whether login throttling enabled or not.
        |
        | Default: true
        |
        */

        'enabled' => true,

        /*
        |--------------------------------------------------------------------------
        | Maximum Login Attempts
        |--------------------------------------------------------------------------
        |
        | Maximum number of login attempts for delaying further attempts.
        |
        | Default: 5
        |
        */

        'max_login_attempts' => 5,

        /*
        |--------------------------------------------------------------------------
        | Lockout Time
        |--------------------------------------------------------------------------
        |
        | Number of minutes to delay further login attempts.
        |
        | Default: 1
        |
        */

        'lockout_time' => 1,

        /*
        |--------------------------------------------------------------------------
        | Send Lockout Email
        |--------------------------------------------------------------------------
        |
        | Send lockout email to users upon multiple failed login attempts.
        |
        */

        'lockout_email' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor authentication
    |--------------------------------------------------------------------------
    */

    'twofactor' => [

        /*
        |--------------------------------------------------------------------------
        | Two-Factor authentication issuer
        |--------------------------------------------------------------------------
        |
        | Every QR code generated for users enabling Two-Factor authentication via
        | the app must have issuer name, which is company's or project's name.
        |
        */

        'issuer' => 'Rinvex',

        /*
        |--------------------------------------------------------------------------
        | Default Two-Factor Providers
        |--------------------------------------------------------------------------
        |
        | The Rinvex Fort supports a variety of Two-Factor back-ends through unified
        | API, giving you convenient access to each back-end using the same syntax
        | for each one. Here you may set the active Two-Factor auth providers.
        |
        | Supported: "totp", "phone"
        |
        */

        'providers' => [

            'totp',
            'phone',

        ],

        /*
        |--------------------------------------------------------------------------
        | Two-Factor Providers
        |--------------------------------------------------------------------------
        |
        | Here you may configure as many Two-Factor "providers" as you wish, and you
        | may even configure multiple providers of the same provider. Defaults have
        | been setup for each provider as an example of the required options.
        |
        */

        'authy' => [

            'mode' => env('AUTHY_MODE', 'live'),

            'keys' => [
                'live'    => env('AUTHY_KEYS_LIVE', ''),
                'sandbox' => env('AUTHY_KEYS_SANDBOX', ''),
            ],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Social Authentication
    |--------------------------------------------------------------------------
    */

    'social' => [

        /*
        |--------------------------------------------------------------------------
        | Third Party Services
        |--------------------------------------------------------------------------
        |
        | This section is for storing the credentials for third party authentication
        | services such as Github, Facebook, Twitter, and others. This section
        | provides a sane default location for this type of information.
        |
        */

        'services' => [

            'facebook' => [
                'client_id'     => env('FACEBOOK_ID'),
                'client_secret' => env('FACEBOOK_SECRET'),
                'redirect'      => env('FACEBOOK_URL'),
            ],

            'google' => [
                'client_id'     => env('GOOGLE_ID'),
                'client_secret' => env('GOOGLE_SECRET'),
                'redirect'      => env('GOOGLE_URL'),
            ],

            'linkedin' => [
                'client_id'     => env('LINKEDIN_ID'),
                'client_secret' => env('LINKEDIN_SECRET'),
                'redirect'      => env('LINKEDIN_URL'),
            ],

            'twitter' => [
                'client_id'     => env('TWITTER_ID'),
                'client_secret' => env('TWITTER_SECRET'),
                'redirect'      => env('TWITTER_URL'),
            ],

            'github' => [
                'client_id'     => env('GITHUB_ID'),
                'client_secret' => env('GITHUB_SECRET'),
                'redirect'      => env('GITHUB_URL'),
            ],

            'bitbucket' => [
                'client_id'     => env('BITBUCKET_ID'),
                'client_secret' => env('BITBUCKET_SECRET'),
                'redirect'      => env('BITBUCKET_URL'),
            ],

        ],

    ],

];
```


## Inspired By

- 
- 
- 


## Changelog

Refer to the [Changelog](CHANGELOG.md) for a full history of the project.


## Support

The following support channels are available at your fingertips:

- [Chat on Slack](http://chat.rinvex.com)
- [Help on Email](mailto:help@rinvex.com)
- [Follow on Twitter](https://twitter.com/rinvex)


## Contributing & Protocols

Thank you for considering contributing to this project! The contribution guide can be found in [CONTRIBUTING.md](CONTRIBUTING.md).

Bug reports, feature requests, and pull requests are very welcome.

- [Versioning](CONTRIBUTING.md#versioning)
- [Support Policy](CONTRIBUTING.md#support-policy)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Pull Requests](CONTRIBUTING.md#pull-requests)


## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to help@rinvex.com. All security vulnerabilities will be promptly addressed.


## About Rinvex

Rinvex is a software solutions startup, specialized in integrated enterprise solutions for SMEs established in Alexandria, Egypt since June 2016. We believe that our drive The Value, The Reach, and The Impact is what differentiates us and unleash the endless possibilities of our philosophy through the power of software. We like to call it Innovation At The Speed Of Life. That’s how we do our share of advancing humanity.


## License

This software is released under [The MIT License (MIT)](LICENSE).

(c) 2016 Rinvex LLC, Some rights reserved.
