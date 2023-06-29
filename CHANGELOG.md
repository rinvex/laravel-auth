# Rinvex Auth Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](CONTRIBUTING.md).


## [v7.1.1] - 2023-06-29
- Drop config resource publishing since there's no config for this package

## [v7.1.0] - 2023-05-02
- Add support for Laravel v11, and drop support for Laravel v9
- Upgrade laravel/socialite to v5.6 from v5.5
- Update phpunit to v10.1 from v9.5

## [v7.0.0] - 2023-01-09
- Tweak artisan commands registration
- Drop PHP v8.0 support and update composer dependencies
- Utilize PHP 8.1 attributes feature for artisan commands

## [v6.1.0] - 2022-02-14
- Update composer dependencies to Laravel v9

## [v6.0.0] - 2021-08-22
- Drop PHP v7 support, and upgrade rinvex package dependencies to next major version
- Update composer dependencies
- Upgrade to GitHub-native Dependabot (#165)

## [v5.0.3] - 2021-02-28
- Move email verification config to default project config/auth.php file

## [v5.0.2] - 2021-01-18
- fix reset password declaration compatibility (#163)
- Enable StyleCI risky mode

## [v5.0.1] - 2020-12-25
- Add support for PHP v8

## [v5.0.0] - 2020-12-22
- Upgrade to Laravel v8

## [v4.1.0] - 2020-06-15
- Update validation rules
- Drop using rinvex/laravel-cacheable from core packages for more flexibility
  - Caching should be handled on the application layer, not enforced from the core packages
- Drop PHP 7.2 & 7.3 support from travis

## [v4.0.7] - 2020-04-12
- Fix ServiceProvider registerCommands method compatibility

## [v4.0.6] - 2020-04-09
- Tweak artisan command registration
- Refactor publish command and allow multiple resource values

## [v4.0.5] - 2020-04-04
- Fix namespace issue

## [v4.0.4] - 2020-04-04
- Enforce consistent artisan command tag namespacing
- Enforce consistent package namespace
- Drop laravel/helpers usage as it's no longer used

## [v4.0.3] - 2020-03-20
- Convert into bigInteger database fields
- Add shortcut -f (force) for artisan publish commands
- Fix migrations path

## [v4.0.2] - 2020-03-15
- Fix composer package laravel-notification-channels/authy version

## [v4.0.1] - 2020-03-15
- Fix wrong package version laravelcollective/html

## [v4.0.0] - 2020-03-15
- Upgrade to Laravel v7.1.x & PHP v7.4.x

## [v3.0.4] - 2020-03-13
- Tweak TravisCI config
- Remove indirect composer dependency
- Drop using global helpers
- Update StyleCI config

## [v3.0.3] - 2019-09-24
- Add missing laravel/helpers composer package

## [v3.0.2] - 2019-09-23
- Fix outdated package version

## [v3.0.1] - 2019-09-23
- Fix outdated package version

## [v3.0.0] - 2019-09-23
- Upgrade to Laravel v6 and update dependencies

## [v2.2.0] - 2019-08-03
- Update composer dependenices

## [v2.1.1] - 2019-06-03
- Enforce latest composer package versions

## [v2.1.0] - 2019-06-02
- Update composer deps
- Drop PHP 7.1 travis test
- Tweak service provider publishes functionality and console tools

## [v2.0.1] - 2019-03-03
- Update composer dependencies

## [v2.0.0] - 2019-03-03
- Rename environment variable QUEUE_DRIVER to QUEUE_CONNECTION
- Require PHP 7.2 & Laravel 5.8
- Apply PHPUnit 8 updates
- Utilize support helpers

## [v1.0.2] - 2018-12-22
- Update composer dependencies
- Add PHP 7.3 support to travis

## [v1.0.1] - 2018-10-05
- Fix wrong composer package version constraints

## [v1.0.0] - 2018-10-01
- Enforce Consistency
- Support Laravel 5.7+
- Rename package to rinvex/laravel-auth

## [v0.0.9] - 2018-09-22
- Update travis php versions
- Drop useless composer dependencies
- Typehint notifications method parameters
- Move country validation rule to rinvex/.country package
- Move language validation rule to rinvex/languages package
- Allow brokers to return null on user retrieval
- Use global helper functions instead of class based methods
- Drop StyleCI multi-language support (paid feature now!)
- Update composer dependencies
- Prepare and tweak testing configuration
- Update StyleCI options
- Update PHPUnit options
- Update default email verification brokers config

## [v0.0.8] - 2018-02-18
- Update composer dependencies
- Drop Laravel 5.5 support

## [v0.0.7] - 2018-02-17
- Major refactor with complete package re-write
- Remove frontend stuff (use cortex/fort for that)
- Use Carbon global helper
- Skip credential validation on social login
- Change social account user id datatype
- Remove frontend stuff & exception handler (user cortex/fort or your app level layer for this)
- Add missing namespaces
- Use ->getKeyName() and ->getKey() methods instead of ->id
- Refactor authorization layer for simplicity & intuitiveness
- Rename job_title to title
- Typehint method returns
- Drop useless model contracts (models already swappable through IoC)
- Move policies & SeedComand to cortex/fort
- Move login throttling to cortex/fort
- Separate TwoFactor into new trait rather than SessionGuard
- Move session persistence mode check to EvenHandler from SessionGuard
- Drop the custom SessionGuard in favor for Laravel's vanilla
- Drop email verification success message
- Move TwoFactorAuthenticatesUsers trait from cortex/fort
- Remove useless TwoFactor config options
- Simplify event listeners
- Simplify registration welcome email
- Move events subscriber to cortex/fort
- Merge GetsMiddleware trait into AbstractController in cortex/foundation
- Fix nullable return type phone & country for verification
- Move middleware & notifications to cortex/fort
- Move HasHashables trait from rinvex/laravel-support
- Simplify IoC binding
- Convert genders database storage to explicit male/female instead of m/f
- Refactor parseAbilities & parseRoles
- Fix wrong parameter names
- Fix cacheable issues
- Add force option to artisan commands
- Refactor abilities to use silber/bouncer
- Refactor user_id to polymorphic relation
- Simplify rinvex/fort config options
- Move GenericException to cortex/foundation from rinvex/fort
- Refactor package and move app-layer features to cortex/fort
- Rename rinvex/fort to rinvex/laravel-auth

## [v0.0.6] - 2018-01-04
- Support Laravel 5.5
- Major refactor with better Laravel integrity
- Push composer dependencies forward
- Fix gender validation rules
- Fix last activity middleware update issue for guests
- Redirect user to email verification form after changing email if required
- Redirect newly registered users to email verification form
- Separate userarea stuff from frontend with appropriate isolation [major refactor]
- Remove TowFactor json mutator/accessor in favor to attribute casting
- Enforce attribute casting
- Enforce validation rules
- Enforce eloquent builder usage consistency
- Call fill->save() rather than create() for explicit calls
- Add country and language validators
- Use collection higher order messages for simpler and clearer code
- Add request validation and authorization functionality for controllers
- Fix date validation rules
- Fix two_factor validation rule
- Tweak and fix exception handler
- Tag published resources
- Update user last_activity via raw query not eloquent to avoid triggering events and thus flushing cache
- Clean data resource files
- Add role users mutator attribute for easy assigning
- Fix php artisan make:auth issues
- Fix dashboard online users issues
- Fix make:auth views generation extension issues
- Use antonioribeiro/google2fa rather than our custom TOTP (close #132)
- Remove outdated console commands
- Remove wrong userarea route prefix
- Update composer dependencies versions
- Refactor GenericException and exception handler (close #126)
- Add query scopes for user active/inactive states
- Add user activate/deactivate model helpers
- Fix routes registration
- Enforce consistent slug formats
- Move prepareUniqueRule validation method to it's own support trait
- Add migration command for easy installation
- Move seeder traits from cortex/fort
- Remove translatable fields from auto casting (It's already casted to array by translatable trait)
- Use custom slug separator rather than attribute mutator
- Use IoC bound model instead of the explicitly hardcoded
- Bind model alias into IoC container
- Use IoC bound model rather than hard coding
- Move seeder helper from rinvex/laravel-support
- Tweak service provider and enforce consistency
- Program to an interface not implementation for flexible model swapping
- Assure unique slugs generated at all times
- Eliminate reset form eliminate button
- Use default dash slug separator
- Trim Model name if ModelContract given
- Handle default translation through overridden HasTranslations trait
- Refactor access areas
- Override MakeAuthCommand conditionally (config option)
- Rename adminarea ability / route / phrase
- Convert seeds to artisan command
- Enforce strict abilities/roles assignment (users can't assign roles or grant abilities they don't already have)
- Add missing guard
- Move slug auto generation to the custom HasSlug trait
- Merge and rename guestarea & memberarea to frontarea
- Remove FormRequest override in favor for native prepareForValidation feature
- Update frontarea route names
- Add Rollback Console Command

## [v0.0.5] - 2017-06-29
- Major refactor with better Laravel integrity
- Refactor FormRequests and Validation Rules
- Refactor Crypto-based email verification
- Refactor Crypto-based password resets
- Refactor TwoFactor Authentication
- Refactor Session Persistence
- Add NoHttpCache middleware
- Tweak and harden security
- BACKWORK INCOMPATIBILITY
- Add Laravel 5.5 support

## [v0.0.4] - 2017-03-08
- Major rewrite with better Laravel integrity
- Total frontend isolation and auth:make generation
- System wide bug fixes, feature enhancements, and stability

## [v0.0.3] - 2017-02-11
- Major changes and wide enhancements

## [v0.0.2] - 2016-12-19
- Fix multiple bugs & issues

## v0.0.1 - 2016-12-19
- Tag first release

[v7.1.1]: https://github.com/rinvex/laravel-auth/compare/v7.1.0...v7.1.1
[v7.1.0]: https://github.com/rinvex/laravel-auth/compare/v7.0.0...v7.1.0
[v7.0.0]: https://github.com/rinvex/laravel-auth/compare/v6.1.0...v7.0.0
[v6.1.0]: https://github.com/rinvex/laravel-auth/compare/v6.0.0...v6.1.0
[v6.0.0]: https://github.com/rinvex/laravel-auth/compare/v5.0.3...v6.0.0
[v5.0.3]: https://github.com/rinvex/laravel-auth/compare/v5.0.2...v5.0.3
[v5.0.2]: https://github.com/rinvex/laravel-auth/compare/v5.0.1...v5.0.2
[v5.0.1]: https://github.com/rinvex/laravel-auth/compare/v5.0.0...v5.0.1
[v5.0.0]: https://github.com/rinvex/laravel-auth/compare/v4.1.0...v5.0.0
[v4.1.0]: https://github.com/rinvex/laravel-auth/compare/v4.0.7...v4.1.0
[v4.0.7]: https://github.com/rinvex/laravel-auth/compare/v4.0.6...v4.0.7
[v4.0.6]: https://github.com/rinvex/laravel-auth/compare/v4.0.5...v4.0.6
[v4.0.5]: https://github.com/rinvex/laravel-auth/compare/v4.0.4...v4.0.5
[v4.0.4]: https://github.com/rinvex/laravel-auth/compare/v4.0.3...v4.0.4
[v4.0.3]: https://github.com/rinvex/laravel-auth/compare/v4.0.2...v4.0.3
[v4.0.2]: https://github.com/rinvex/laravel-auth/compare/v4.0.1...v4.0.2
[v4.0.1]: https://github.com/rinvex/laravel-auth/compare/v4.0.0...v4.0.1
[v4.0.0]: https://github.com/rinvex/laravel-auth/compare/v3.0.4...v4.0.0
[v3.0.4]: https://github.com/rinvex/laravel-auth/compare/v3.0.3...v3.0.4
[v3.0.3]: https://github.com/rinvex/laravel-auth/compare/v3.0.2...v3.0.3
[v3.0.2]: https://github.com/rinvex/laravel-auth/compare/v3.0.1...v3.0.2
[v3.0.1]: https://github.com/rinvex/laravel-auth/compare/v3.0.0...v3.0.1
[v3.0.0]: https://github.com/rinvex/laravel-auth/compare/v2.2.0...v3.0.0
[v2.2.0]: https://github.com/rinvex/laravel-auth/compare/v2.1.1...v2.2.0
[v2.1.1]: https://github.com/rinvex/laravel-auth/compare/v2.1.0...v2.1.1
[v2.1.0]: https://github.com/rinvex/laravel-auth/compare/v2.0.1...v2.1.0
[v2.0.1]: https://github.com/rinvex/laravel-auth/compare/v2.0.0...v2.0.1
[v2.0.0]: https://github.com/rinvex/laravel-auth/compare/v1.0.2...v2.0.0
[v1.0.2]: https://github.com/rinvex/laravel-auth/compare/v1.0.1...v1.0.2
[v1.0.1]: https://github.com/rinvex/laravel-auth/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/rinvex/laravel-auth/compare/v0.0.9...v1.0.0
[v0.0.9]: https://github.com/rinvex/laravel-auth/compare/v0.0.8...v0.0.9
[v0.0.8]: https://github.com/rinvex/laravel-auth/compare/v0.0.7...v0.0.8
[v0.0.7]: https://github.com/rinvex/laravel-auth/compare/v0.0.6...v0.0.7
[v0.0.6]: https://github.com/rinvex/laravel-auth/compare/v0.0.5...v0.0.6
[v0.0.5]: https://github.com/rinvex/laravel-auth/compare/v0.0.4...v0.0.5
[v0.0.4]: https://github.com/rinvex/laravel-auth/compare/v0.0.3...v0.0.4
[v0.0.3]: https://github.com/rinvex/laravel-auth/compare/v0.0.2...v0.0.3
[v0.0.2]: https://github.com/rinvex/laravel-auth/compare/v0.0.1...v0.0.2
