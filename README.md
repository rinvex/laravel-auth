# Rinvex Fort [WIP]

**Rinvex Fort** is a powerful authentication, authorization and verification package built on top of Laravel. It provides developers with Role Based Access Control, Two-Factor Authentication, Social Authentication, compatible with Laravel’s standard API and fully featured all-in-one solution out of the box.

[![Packagist](https://img.shields.io/packagist/v/rinvex/fort.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/rinvex/fort)
[![VersionEye Dependencies](https://img.shields.io/versioneye/d/php/rinvex:fort.svg?label=Dependencies&style=flat-square)](https://www.versioneye.com/php/rinvex:fort/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/rinvex/fort.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/rinvex/fort/)
[![Code Climate](https://img.shields.io/codeclimate/github/rinvex/fort.svg?label=CodeClimate&style=flat-square)](https://codeclimate.com/github/rinvex/fort)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/e361e7c2-c0ae-469d-8a53-6a2433e7aaad.svg?label=SensioLabs&style=flat-square)](https://insight.sensiolabs.com/projects/e361e7c2-c0ae-469d-8a53-6a2433e7aaad)
[![StyleCI](https://styleci.io/repos/66008159/shield)](https://styleci.io/repos/66008159)
[![License](https://img.shields.io/packagist/l/rinvex/fort.svg?label=License&style=flat-square)](https://github.com/rinvex/fort/blob/develop/LICENSE)


## Awesomeness Level

// Comparison table here


## Screebshots

We know how true is the idiom saying **A picture is worth a thousand words**, and thus we've captured almost every possible feature in a neat screenshot just for your eyes :wink:, give it a look.


## Examples

Once installed, you'll be able to do stuff like this:

```php
// Create a new user
app('rinvex.fort.user')->create([
    'username' => 'Tester',
    'email'    => 'test@example.com',
    'password' => 'Very.Strong.Password',
]);

// Update existing user

```


## Features

- **Default Laravel API**
- Multiple login columns
- Single/Multiple sessions
- Create Users, Roles, Abilities
- Request password reset via email
- Grant/Revoke Abilities to Users/Roles
- Assign/Remove Roles to Users (multiple roles allowed)
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


## Usage Notes

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
