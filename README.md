# Rinvex Fort

**Rinvex Fort** is a powerful authentication, authorization and verification package built on top of Laravel. It provides developers with Role Based Access Control, Two-Factor Authentication, Social Authentication, compatible with Laravel’s standard API and fully featured all-in-one solution out of the box.

[![Packagist](https://img.shields.io/packagist/v/rinvex/fort.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/rinvex/fort)
[![VersionEye Dependencies](https://img.shields.io/versioneye/d/php/rinvex:fort.svg?label=Dependencies&style=flat-square)](https://www.versioneye.com/php/rinvex:fort/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/rinvex/fort.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/rinvex/fort/)
[![Code Climate](https://img.shields.io/codeclimate/github/rinvex/fort.svg?label=CodeClimate&style=flat-square)](https://codeclimate.com/github/rinvex/fort)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/e361e7c2-c0ae-469d-8a53-6a2433e7aaad.svg?label=SensioLabs&style=flat-square)](https://insight.sensiolabs.com/projects/e361e7c2-c0ae-469d-8a53-6a2433e7aaad)
[![StyleCI](https://styleci.io/repos/66008159/shield)](https://styleci.io/repos/66008159)
[![License](https://img.shields.io/packagist/l/rinvex/fort.svg?label=License&style=flat-square)](https://github.com/rinvex/fort/blob/develop/LICENSE)


## Table Of Contents

- [Documentation](https://github.com/rinvex/fort/wiki)
    - [Features](https://github.com/rinvex/fort/wiki#features)
    - [Authentication and Authorization](https://github.com/rinvex/fort/wiki#authentication-and-authorization)
    - [Role Based Access Control](https://github.com/rinvex/fort/wiki#role-based-access-control)
    - [A Sense Of Security](https://github.com/rinvex/fort/wiki#a-sense-of-security)
- [1) Installation](https://github.com/rinvex/fort/wiki/1\)-Installation)
- [2) Screenshots](https://github.com/rinvex/fort/wiki/2\)-Screenshots)
- [3) Config Options](https://github.com/rinvex/fort/wiki/3\)-Config-Options)
- [4) Authentication](https://github.com/rinvex/fort/wiki/4\)-Authentication)
    - [Introduction](https://github.com/rinvex/fort/wiki/4\)-Authentication#introduction)
    - [Database Considerations](https://github.com/rinvex/fort/wiki/4\)-Authentication#database-considerations)
    - [Authentication Quickstart](https://github.com/rinvex/fort/wiki/4\)-Authentication#authentication-quickstart)
        - [Database](https://github.com/rinvex/fort/wiki/4\)-Authentication#database)
        - [Routing](https://github.com/rinvex/fort/wiki/4\)-Authentication#routing)
        - [Views](https://github.com/rinvex/fort/wiki/4\)-Authentication#views)
        - [Language Phrases](https://github.com/rinvex/fort/wiki/4\)-Authentication#language-phrases)
        - [Authenticating](https://github.com/rinvex/fort/wiki/4\)-Authentication#authenticating)
            - [Username Identification](https://github.com/rinvex/fort/wiki/4\)-Authentication#username-identification)
            - [Guard Customization](https://github.com/rinvex/fort/wiki/4\)-Authentication#guard-customization)
            - [Broker Customization](https://github.com/rinvex/fort/wiki/4\)-Authentication#broker-customization)
            - [Validation Rules](https://github.com/rinvex/fort/wiki/4\)-Authentication#validation-rules)
            - [Validation and Storage Customization](https://github.com/rinvex/fort/wiki/4\)-Authentication#validation-and-storage-customization)
        - [Retrieving The Authenticated User](https://github.com/rinvex/fort/wiki/4\)-Authentication#retrieving-the-authenticated-user)
            - [Determining If The Current User Is Authenticated](https://github.com/rinvex/fort/wiki/4\)-Authentication#determining-if-the-current-user-is-authenticated)
        - [Protecting Routes](https://github.com/rinvex/fort/wiki/4\)-Authentication#protecting-routes)
            - [Specifying A Guard](https://github.com/rinvex/fort/wiki/4\)-Authentication#specifying-a-guard)
        - [Remembering Users](https://github.com/rinvex/fort/wiki/4\)-Authentication#remembering-users)
        - [Login Throttling](https://github.com/rinvex/fort/wiki/4\)-Authentication#login-throttling)
        - [Logging Out](https://github.com/rinvex/fort/wiki/4\)-Authentication#logging-out)
    - [Manually Authenticating Users](https://github.com/rinvex/fort/wiki/4\)-Authentication#manually-authenticating-users)
        - [Specifying Additional Conditions](https://github.com/rinvex/fort/wiki/4\)-Authentication#specifying-additional-conditions)
        - [Accessing Specific Guard Instances](https://github.com/rinvex/fort/wiki/4\)-Authentication#accessing-specific-guard-instances)
        - [Other Authentication Methods](https://github.com/rinvex/fort/wiki/4\)-Authentication#other-authentication-methods)
            - [Authenticate A User Instance](https://github.com/rinvex/fort/wiki/4\)-Authentication#authenticate-a-user-instance)
            - [Authenticate A User By ID](https://github.com/rinvex/fort/wiki/4\)-Authentication#authenticate-a-user-by-id)
            - [Authenticate A User Once](https://github.com/rinvex/fort/wiki/4\)-Authentication#authenticate-a-user-once)
    - [HTTP Basic Authentication](https://github.com/rinvex/fort/wiki/4\)-Authentication#http-basic-authentication)
        - [Stateless HTTP Basic Authentication](https://github.com/rinvex/fort/wiki/4\)-Authentication#stateless-http-basic-authentication)
    - [Adding Custom Guards](https://github.com/rinvex/fort/wiki/4\)-Authentication#adding-custom-guards)
    - [Adding Custom User Providers](https://github.com/rinvex/fort/wiki/4\)-Authentication#adding-custom-user-providers)
        - [The User Provider Contract](https://github.com/rinvex/fort/wiki/4\)-Authentication#the user provider contract)
        - [The AuthenticatableContract Interface](https://github.com/rinvex/fort/wiki/4\)-Authentication#the-authenticatablecontract-interface)
- [5) Authorization](https://github.com/rinvex/fort/wiki/5\)-Authorization)
    - [Introduction](https://github.com/rinvex/fort/wiki/5\)-Authorization#introduction)
    - [Gates](https://github.com/rinvex/fort/wiki/5\)-Authorization#gates)
        - [Writing Gates](https://github.com/rinvex/fort/wiki/5\)-Authorization#writing-gates)
        - [Authorizing Actions](https://github.com/rinvex/fort/wiki/5\)-Authorization#authorizing-actions)
    - [Abilities](https://github.com/rinvex/fort/wiki/5\)-Authorization#abilities)
        - [Creating abilities](https://github.com/rinvex/fort/wiki/5\)-Authorization#creating-abilities)
        - [Granting and Revoking Abilities](https://github.com/rinvex/fort/wiki/5\)-Authorization#granting-and-revoking-abilities)
    - [Roles](https://github.com/rinvex/fort/wiki/5\)-Authorization#roles)
        - [Creating Roles](https://github.com/rinvex/fort/wiki/5\)-Authorization#creating-roles)
        - [Assigning and Removing Roles](https://github.com/rinvex/fort/wiki/5\)-Authorization#assigning-and-removing-roles)
        - [Check Has Role](https://github.com/rinvex/fort/wiki/5\)-Authorization#check-has-role)
        - [Check Has Role via Blade](https://github.com/rinvex/fort/wiki/5\)-Authorization#check-has-role-via-blade)
    - [Creating Policies](https://github.com/rinvex/fort/wiki/5\)-Authorization#creating-policies)
        - [Generating Policies](https://github.com/rinvex/fort/wiki/5\)-Authorization#generating-policies)
        - [Registering Policies](https://github.com/rinvex/fort/wiki/5\)-Authorization#registering-policies)
    - [Writing Policies](https://github.com/rinvex/fort/wiki/5\)-Authorization#writing-policies)
        - [Policy Methods](https://github.com/rinvex/fort/wiki/5\)-Authorization#policy-methods)
        - [Methods Without Models](https://github.com/rinvex/fort/wiki/5\)-Authorization#methods-without-models)
        - [Policy Filters](https://github.com/rinvex/fort/wiki/5\)-Authorization#policy-filters)
    - [Authorizing Actions Using Policies](https://github.com/rinvex/fort/wiki/5\)-Authorization#authorizing-actions-using-policies)
        - [Via The User Model](https://github.com/rinvex/fort/wiki/5\)-Authorization#via-the-user-model)
        - [Via Middleware](https://github.com/rinvex/fort/wiki/5\)-Authorization#via-middleware)
        - [Via Controller Helpers](https://github.com/rinvex/fort/wiki/5\)-Authorization#via-controller-helpers)
        - [Via Blade Templates](https://github.com/rinvex/fort/wiki/5\)-Authorization#via-blade-templates)
- [6) Email Verification](https://github.com/rinvex/fort/wiki/6\)-Email-Verification)
    - [Introduction](https://github.com/rinvex/fort/wiki/6\)-Email-Verification#introduction)
    - [Database Considerations](https://github.com/rinvex/fort/wiki/6\)-Email-Verification#database-considerations)
    - [Routing](https://github.com/rinvex/fort/wiki/6\)-Email-Verification#routing)
    - [Views](https://github.com/rinvex/fort/wiki/6\)-Email-Verification#views)
    - [After Verifying Emails](https://github.com/rinvex/fort/wiki/6\)-Email-Verification#after-verifying-emails)
    - [Customization](https://github.com/rinvex/fort/wiki/6\)-Email-Verification#customization)
        - [Email Verifier Broker Customization](https://github.com/rinvex/fort/wiki/6\)-Email-Verification#email-verifier-broker-customization)
        - [Reset Email Customization](https://github.com/rinvex/fort/wiki/6\)-Email-Verification#reset-email-customization)
- [7) Phone Verification](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification)
    - [Introduction](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification#introduction)
    - [Database Considerations](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification#database-considerations)
    - [Usage](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification#usage)
    - [Routing](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification#routing)
    - [Views](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification#views)
    - [After Verifying Phones](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification#after-verifying-phones)
    - [Customization](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification#customization)
        - [Phone Verification Notification Customization](https://github.com/rinvex/fort/wiki/7\)-Phone-Verification#phone-verification-notification-customization)
- [8) Password Reset](https://github.com/rinvex/fort/wiki/8\)-Password-Reset)
    - [Introduction](https://github.com/rinvex/fort/wiki/8\)-Password-Reset#introduction)
    - [Database Considerations](https://github.com/rinvex/fort/wiki/8\)-Password-Reset#database-considerations)
    - [Routing](https://github.com/rinvex/fort/wiki/8\)-Password-Reset#routing)
    - [Views](https://github.com/rinvex/fort/wiki/8\)-Password-Reset#views)
    - [After Resetting Passwords](https://github.com/rinvex/fort/wiki/8\)-Password-Reset#after-resetting-passwords)
    - [Customization](https://github.com/rinvex/fort/wiki/8\)-Password-Reset#customization)
        - [Password Broker Customization](https://github.com/rinvex/fort/wiki/8\)-Password-Reset#password-broker-customization)
        - [Reset Email Customization](https://github.com/rinvex/fort/wiki/8\)-Password-Reset#reset-email-customization)
- [9) Fired Events](https://github.com/rinvex/fort/wiki/9\)-Fired-Events)


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
- [Pull Requests](CONTRIBUTING.md#pull-requests)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Feature Requests](CONTRIBUTING.md#feature-requests)
- [Git Flow](CONTRIBUTING.md#git-flow)


## Security Vulnerabilities

We want to ensure that this package is secure for everyone. If you've discovered a security vulnerability in this package, we appreciate your help in disclosing it to us in a [responsible manner](https://en.wikipedia.org/wiki/Responsible_disclosure).

Publicly disclosing a vulnerability can put the entire community at risk. If you've discovered a security concern, please email us at [security@rinvex.com](mailto:security@rinvex.com). We'll work with you to make sure that we understand the scope of the issue, and that we fully address your concern. We consider correspondence sent to [security@rinvex.com](mailto:security@rinvex.com) our highest priority, and work to address any issues that arise as quickly as possible.

After a security vulnerability has been corrected, a security hotfix release will be deployed as soon as possible.


## About Rinvex

Rinvex is a software solutions startup, specialized in integrated enterprise solutions for SMEs established in Alexandria, Egypt since June 2016. We believe that our drive The Value, The Reach, and The Impact is what differentiates us and unleash the endless possibilities of our philosophy through the power of software. We like to call it Innovation At The Speed Of Life. That’s how we do our share of advancing humanity.


## License

This software is released under [The MIT License (MIT)](LICENSE).

(c) 2016-2017 Rinvex LLC, Some rights reserved.
