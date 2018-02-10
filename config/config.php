<?php

declare(strict_types=1);

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
    |       config/auth.php (Check 'model' key inside the 'providers' array)
    |
    | Defaults:
    | - Rinvex\Fort\Models\Session::class
    | - Rinvex\Fort\Models\Socialite::class
    |
    */

    'models' => [

        'session' => \Rinvex\Fort\Models\Session::class,
        'socialite' => \Rinvex\Fort\Models\Socialite::class,

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
    | Notes: - Sessions table is defined in the default Laravel configuration file:
    |          config/session.php (Check 'table' key)
    |
    | Defaults:
    | - users
    | - socialites
    |
    */

    'tables' => [

        'users' => 'users',
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

        // Send Welcome Email Upon Registration Success
        'welcome_email' => true,

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
            'expire' => 60,
        ],

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

        // Send Lockout Email
        'lockout_email' => true,

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

    // Minimum Passwords Characters
    'password_min_chars' => 8,

    // Online Users Activity Interval (minutes to indicate user as active)
    'online_interval' => 15,

    // List items per page (use accross data lists)
    'items_per_page' => 10,

];
