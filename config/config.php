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

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication and Authorization Models
    |--------------------------------------------------------------------------
    |
    | Specify your Eloquent models that should be used to retrieve your
    | resources. A sensible defaults has been defined for you, but
    | you may use whatever you like. The model you want to use
    | must extend one of the default base models.
    |
    | Note: User model is defined in the default Laravel configuration file:
    |       config/auth.php (Check 'model' key inside the providers array)
    |
    | Defaults:
    | - Rinvex\Fort\Models\Ability::class
    | - Rinvex\Fort\Models\Role::class
    | - Rinvex\Fort\Models\User::class
    | - Rinvex\Fort\Models\Persistence::class
    | - Rinvex\Fort\Models\Socialite::class
    |
    */

    'models' => [

        'ability' => Rinvex\Fort\Models\Ability::class,
        'role' => Rinvex\Fort\Models\Role::class,
        'persistence' => Rinvex\Fort\Models\Persistence::class,
        'socialite' => Rinvex\Fort\Models\Socialite::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication and Authorization Database Tables
    |--------------------------------------------------------------------------
    |
    | Specify your database tables that should be used to store your
    | resources. A sensible defaults has been defined for you, but
    | you may use whatever you like. The table you want to use
    | must have the same structure as of the default ones.
    |
    | Defaults:
    | - abilities
    | - roles
    | - users
    | - ability_user
    | - role_user
    | - ability_role
    | - email_verifications
    | - password_resets
    | - persistences
    | - socialites
    |
    */

    'tables' => [

        'abilities' => 'abilities',
        'roles' => 'roles',
        'users' => 'users',
        'ability_user' => 'ability_user',
        'role_user' => 'role_user',
        'ability_role' => 'ability_role',
        'email_verifications' => 'email_verifications',
        'password_resets' => 'password_resets',
        'persistences' => 'persistences',
        'socialites' => 'socialites',

    ],


    /*
    |--------------------------------------------------------------------------
    | User Registration
    |--------------------------------------------------------------------------
    |
    | You may specify user registration options here. You can enable or
    | disable new registrations, moderate or activate new users, set
    | default registration role, and send welcome email on success
    |
    */

    'registration' => [

        // Enable User Registration
        'enabled' => true,

        // Moderate New User Registrations (Admin Approval Required)
        'moderated' => false,

        // Default Role For New User Registrations (slug)
        'default_role' => 'registered',

        // Send Welcome Email Upon Registration Success
        'welcome_email' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | The broker option controls the default reset password broker for
    | your application. You may change this default as required,
    | but they're a perfect start for most applications.
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

    'passwordreset' => [

        // Password Reset Default Broker
        'broker' => 'users',

        // Password Reset Broker Configuration(s)
        'users' => [
            'provider' => 'users',
            'expire'   => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    |
    | The broker option controls the default email verification broker for
    | your application. You may change this default as required,
    | but they're a perfect start for most applications.
    |
    | You may specify multiple email verification configurations if you have more
    | than one user table or model in the application and you want to have
    | separate email verification settings based on the specific user types.
    |
    | The expire time is the number of minutes that the email verification token
    | should be considered valid. This security feature keeps tokens short-lived
    | so they have less time to be guessed. You may change this as needed.
    |
    */

    'emailverification' => [

        // Require email verification for new user registrations and email change
        'required' => true,

        // Email Verification Default Broker
        'broker' => 'users',

        // Email Verification Broker Configuration(s)
        'users' => [
            'provider' => 'users',
            'expire'   => 60,
        ],

        // Send Success Email Upon Verification
        'success_email' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Login Throttling
    |--------------------------------------------------------------------------
    |
    | You may enable login throttling and specify maximum attemps before
    | being locked, lockout time, and whether to send email or not.
    |
    */

    'throttle' => [

        // Enable Login Throttling
        'enabled' => true,

        // Maximum Login Attempts before lockout
        'max_login_attempts' => 5,

        // Lockout Time (in minutes)
        'lockout_time' => 1,

        // Send Lockout Email
        'lockout_email' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor authentication
    |--------------------------------------------------------------------------
    |
    | Rinvex Fort supports a variety of Two-Factor authentication backends through
    | unified API, giving you convenient access to each using the same syntax.
    | Here you may set the active Two-Factor authentication providers.
    |
    */

    'twofactor' => [

        'providers' => [

            'totp',
            'phone',

        ],

    ],

    /*
     |--------------------------------------------------------------------------
     | Protected Models
     |--------------------------------------------------------------------------
     |
     | Model Ids of protected abilities, roles, users that no one can control
     | except someone with "Super Admin" ability (edit, delete, ..etc).
     |
     */

    'protected' => [

        'abilities' => [1],
        'roles'     => [1],
        'users'     => [1],

    ],

    /*
    |--------------------------------------------------------------------------
    | Minimum Passwords Characters
    |--------------------------------------------------------------------------
    */

    'password_min_chars' => 8,

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
     | Online Users Options
     |--------------------------------------------------------------------------
     */

    'online' => [

        /*
        |--------------------------------------------------------------------------
        | Online Users Activity Interval (minutes)
        |--------------------------------------------------------------------------
        |
        | Minutes that indicates an active user, to be considered an online user.
        |
        */

        'interval' => 15,

    ],

    /*
     |--------------------------------------------------------------------------
     | Backend Options
     |--------------------------------------------------------------------------
     */

    'backend' => [
        'items_per_page'      => 2,
        'items_per_dashboard' => 2,
    ],

];
