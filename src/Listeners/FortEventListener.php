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

namespace Rinvex\Fort\Listeners;

use Illuminate\Http\Request;
use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Rinvex\Fort\Models\Ability;
use Rinvex\Fort\Models\Persistence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;
use Rinvex\Fort\Contracts\RoleRepositoryContract;
use Rinvex\Fort\Contracts\UserRepositoryContract;
use Rinvex\Fort\Contracts\AuthenticatableContract;
use Rinvex\Fort\Contracts\AbilityRepositoryContract;
use Rinvex\Fort\Contracts\PersistenceRepositoryContract;
use Rinvex\Fort\Notifications\RegistrationSuccessNotification;
use Rinvex\Fort\Notifications\VerificationSuccessNotification;
use Rinvex\Fort\Notifications\AuthenticationLockoutNotification;

class FortEventListener
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Create a new fort event listener instance.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function subscribe(Dispatcher $dispatcher)
    {
        // Authentication events
        $dispatcher->listen('rinvex.fort.auth.user', __CLASS__.'@authUser');
        $dispatcher->listen('rinvex.fort.auth.login', __CLASS__.'@authLogin');
        $dispatcher->listen('rinvex.fort.auth.failed', __CLASS__.'@authFailed');
        $dispatcher->listen('rinvex.fort.auth.lockout', __CLASS__.'@authLockout');
        $dispatcher->listen('rinvex.fort.auth.unverified', __CLASS__.'@authUnverified');
        $dispatcher->listen('rinvex.fort.auth.autologout', __CLASS__.'@authAutoLogout');
        $dispatcher->listen('rinvex.fort.auth.attempt', __CLASS__.'@authAttempt');
        $dispatcher->listen('rinvex.fort.auth.logout', __CLASS__.'@authLogout');
        $dispatcher->listen('rinvex.fort.auth.valid', __CLASS__.'@authValid');

        // Two-Factor required events
        $dispatcher->listen('rinvex.fort.twofactor.required', __CLASS__.'@twoFactorRequired');

        // Two-Factor backup verify events
        $dispatcher->listen('rinvex.fort.twofactor.backup.verify.start', __CLASS__.'@twoFactorBackupVerifyStart');
        $dispatcher->listen('rinvex.fort.twofactor.backup.verify.failed', __CLASS__.'@twoFactorBackupVerifyFailed');
        $dispatcher->listen('rinvex.fort.twofactor.backup.verify.success', __CLASS__.'@twoFactorBackupVerifySuccess');

        // Two-Factor phone register events
        $dispatcher->listen('rinvex.fort.twofactor.phone.register.start', __CLASS__.'@twoFactorPhoneRegisterStart');
        $dispatcher->listen('rinvex.fort.twofactor.phone.register.failed', __CLASS__.'@twoFactorPhoneRegisterFailed');
        $dispatcher->listen('rinvex.fort.twofactor.phone.register.success', __CLASS__.'@twoFactorPhoneRegisterSuccess');

        // Two-Factor phone verify events
        $dispatcher->listen('rinvex.fort.twofactor.phone.verify.start', __CLASS__.'@twoFactorPhoneVerifyStart');
        $dispatcher->listen('rinvex.fort.twofactor.phone.verify.failed', __CLASS__.'@twoFactorPhoneVerifyFailed');
        $dispatcher->listen('rinvex.fort.twofactor.phone.verify.success', __CLASS__.'@twoFactorPhoneVerifySuccess');

        // Two-Factor phone delete events
        $dispatcher->listen('rinvex.fort.twofactor.phone.delete.start', __CLASS__.'@twoFactorPhoneDeleteStart');
        $dispatcher->listen('rinvex.fort.twofactor.phone.delete.failed', __CLASS__.'@twoFactorPhoneDeleteFailed');
        $dispatcher->listen('rinvex.fort.twofactor.phone.delete.success', __CLASS__.'@twoFactorPhoneDeleteSuccess');

        // Two-Factor TOTP verify events
        $dispatcher->listen('rinvex.fort.twofactor.totp.verify.start', __CLASS__.'@twoFactorTotpVerifyStart');
        $dispatcher->listen('rinvex.fort.twofactor.totp.verify.failed', __CLASS__.'@twoFactorTotpVerifyFailed');
        $dispatcher->listen('rinvex.fort.twofactor.totp.verify.success', __CLASS__.'@twoFactorTotpVerifySuccess');

        // Registration events
        $dispatcher->listen('rinvex.fort.register.start', __CLASS__.'@registerStart');
        $dispatcher->listen('rinvex.fort.register.success', __CLASS__.'@registerSuccess');
        $dispatcher->listen('rinvex.fort.register.social.start', __CLASS__.'@registerSocialStart');
        $dispatcher->listen('rinvex.fort.register.social.success', __CLASS__.'@registerSocialSuccess');

        // Reset password events
        $dispatcher->listen('rinvex.fort.passwordreset.start', __CLASS__.'@passwordResetStart');
        $dispatcher->listen('rinvex.fort.passwordreset.success', __CLASS__.'@passwordResetSuccess');

        // Email verification events
        $dispatcher->listen('rinvex.fort.emailverification.start', __CLASS__.'@emailVerificationStart');
        $dispatcher->listen('rinvex.fort.emailverification.success', __CLASS__.'@emailVerificationSuccess');

        // Ability events
        $dispatcher->listen('rinvex.fort.ability.granting', __CLASS__.'@abilityGranting');
        $dispatcher->listen('rinvex.fort.ability.granted', __CLASS__.'@abilityGranted');
        $dispatcher->listen('rinvex.fort.ability.revoking', __CLASS__.'@abilityRevoking');
        $dispatcher->listen('rinvex.fort.ability.revoked', __CLASS__.'@abilityRevoked');

        $dispatcher->listen('rinvex.fort.ability.entity.creating', __CLASS__.'@abilityCreating');
        $dispatcher->listen('rinvex.fort.ability.entity.created', __CLASS__.'@abilityCreated');
        $dispatcher->listen('rinvex.fort.ability.entity.updating', __CLASS__.'@abilityUpdating');
        $dispatcher->listen('rinvex.fort.ability.entity.updated', __CLASS__.'@abilityUpdated');
        $dispatcher->listen('rinvex.fort.ability.entity.deleting', __CLASS__.'@abilityDeleting');
        $dispatcher->listen('rinvex.fort.ability.entity.deleted', __CLASS__.'@abilityDeleted');

        // Roles events
        $dispatcher->listen('rinvex.fort.role.assigning', __CLASS__.'@roleAssigning');
        $dispatcher->listen('rinvex.fort.role.assigned', __CLASS__.'@roleAssigned');
        $dispatcher->listen('rinvex.fort.role.removing', __CLASS__.'@roleRemoving');
        $dispatcher->listen('rinvex.fort.role.removed', __CLASS__.'@roleRemoved');

        $dispatcher->listen('rinvex.fort.role.entity.creating', __CLASS__.'@roleCreating');
        $dispatcher->listen('rinvex.fort.role.entity.created', __CLASS__.'@roleCreated');
        $dispatcher->listen('rinvex.fort.role.entity.updating', __CLASS__.'@roleUpdating');
        $dispatcher->listen('rinvex.fort.role.entity.updated', __CLASS__.'@roleUpdated');
        $dispatcher->listen('rinvex.fort.role.entity.deleting', __CLASS__.'@roleDeleting');
        $dispatcher->listen('rinvex.fort.role.entity.deleted', __CLASS__.'@roleDeleted');

        // Users events
        $dispatcher->listen('rinvex.fort.user.entity.creating', __CLASS__.'@userCreating');
        $dispatcher->listen('rinvex.fort.user.entity.created', __CLASS__.'@userCreated');
        $dispatcher->listen('rinvex.fort.user.entity.updating', __CLASS__.'@userUpdating');
        $dispatcher->listen('rinvex.fort.user.entity.updated', __CLASS__.'@userUpdated');
        $dispatcher->listen('rinvex.fort.user.entity.deleting', __CLASS__.'@userDeleting');
        $dispatcher->listen('rinvex.fort.user.entity.deleted', __CLASS__.'@userDeleted');

        // Persistences events
        $dispatcher->listen('rinvex.fort.persistence.entity.creating', __CLASS__.'@persistenceCreating');
        $dispatcher->listen('rinvex.fort.persistence.entity.created', __CLASS__.'@persistenceCreated');
        $dispatcher->listen('rinvex.fort.persistence.entity.updating', __CLASS__.'@persistenceUpdating');
        $dispatcher->listen('rinvex.fort.persistence.entity.updated', __CLASS__.'@persistenceUpdated');
        $dispatcher->listen('rinvex.fort.persistence.entity.deleting', __CLASS__.'@persistenceDeleting');
        $dispatcher->listen('rinvex.fort.persistence.entity.deleted', __CLASS__.'@persistenceDeleted');
    }

    /**
     * Listen to the authentication event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function authUser(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the authentication event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param bool                                           $remember
     *
     * @return void
     */
    public function authLogin(AuthenticatableContract $user, $remember)
    {
        //
    }

    /**
     * Listen to the authentication fail event.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return void
     */
    public function authFailed(array $credentials, $remember)
    {
        //
    }

    /**
     * Listen to the authentication lockout event.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function authLockout(Request $request)
    {
        if (config('rinvex.fort.throttle.lockout_email')) {
            $user = get_login_field($loginfield = $request->get('loginfield')) == 'email' ? $this->app['rinvex.fort.user']->findBy('email', $loginfield) : $this->app['rinvex.fort.user']->findBy('username', $loginfield);

            $user->notify(new AuthenticationLockoutNotification($request));
        }
    }

    /**
     * Listen to the authentication unverified event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function authUnverified(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the automatic logout event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function authAutoLogout(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the authentication attempt event.
     *
     * @param array $credentials
     * @param bool  $remember
     * @param bool  $login
     *
     * @return void
     */
    public function authAttempt($credentials, $remember, $login)
    {
        //
    }

    /**
     * Listen to the authentication logout event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function authLogout(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the authentication valid event.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return void
     */
    public function authValid(array $credentials, $remember)
    {
        //
    }

    /**
     * Listen to the Two-Factor required event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function twoFactorRequired(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the Two-Factor backup verify start event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $token
     *
     * @return void
     */
    public function twoFactorBackupVerifyStart(AuthenticatableContract $user, $token)
    {
        //
    }

    /**
     * Listen to the Two-Factor backup verify failed event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $token
     *
     * @return void
     */
    public function twoFactorBackupVerifyFailed(AuthenticatableContract $user, $token)
    {
        //
    }

    /**
     * Listen to the Two-Factor backup verify success event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $token
     *
     * @return void
     */
    public function twoFactorBackupVerifySuccess(AuthenticatableContract $user, $token)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone register start event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function twoFactorPhoneRegisterStart(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone register failed event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param array                                          $response
     *
     * @return void
     */
    public function twoFactorPhoneRegisterFailed(AuthenticatableContract $user, array $response)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone register success event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param array                                          $response
     *
     * @return void
     */
    public function twoFactorPhoneRegisterSuccess(AuthenticatableContract $user, array $response)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone verify start event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $token
     *
     * @return void
     */
    public function twoFactorPhoneVerifyStart(AuthenticatableContract $user, $token)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone verify failed event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $token
     * @param array                                          $response
     *
     * @return void
     */
    public function twoFactorPhoneVerifyFailed(AuthenticatableContract $user, $token, array $response)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone verify success event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $token
     * @param array                                          $response
     *
     * @return void
     */
    public function twoFactorPhoneVerifySuccess(AuthenticatableContract $user, $token, array $response)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone delete start event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function twoFactorPhoneDeleteStart(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone delete failed event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param array                                          $response
     *
     * @return void
     */
    public function twoFactorPhoneDeleteFailed(AuthenticatableContract $user, array $response)
    {
        //
    }

    /**
     * Listen to the Two-Factor phone delete success event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param array                                          $response
     *
     * @return void
     */
    public function twoFactorPhoneDeleteSuccess(AuthenticatableContract $user, array $response)
    {
        //
    }

    /**
     * Listen to the Two-Factor TOTP verify start event.
     *
     * @param string $secret
     * @param string $token
     *
     * @return void
     */
    public function twoFactorTotpVerifyStart($secret, $token)
    {
        //
    }

    /**
     * Listen to the Two-Factor TOTP verify failed event.
     *
     * @param string $secret
     * @param string $token
     *
     * @return void
     */
    public function twoFactorTotpVerifyFailed($secret, $token)
    {
        //
    }

    /**
     * Listen to the Two-Factor TOTP verify success event.
     *
     * @param string $secret
     * @param string $token
     *
     * @return void
     */
    public function twoFactorTotpVerifySuccess($secret, $token)
    {
        //
    }

    /**
     * Listen to the register start event.
     *
     * @param array $credentials
     *
     * @return void
     */
    public function registerStart(array $credentials)
    {
        //
    }

    /**
     * Listen to the register success event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function registerSuccess(AuthenticatableContract $user)
    {
        // Send welcome email
        if (config('rinvex.fort.registration.welcome_email')) {
            $user->notify(new RegistrationSuccessNotification());
        }

        // Attach default role to the registered user
        if ($default = $this->app['config']->get('rinvex.fort.registration.default_role')) {
            if ($role = $this->app['rinvex.fort.role']->findBy('slug', $default)) {
                $user->roles()->attach($role);
            }
        }
    }

    /**
     * Listen to the register social start event.
     *
     * @param array $credentials
     *
     * @return void
     */
    public function registerSocialStart(array $credentials)
    {
        //
    }

    /**
     * Listen to the register social success event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function registerSocialSuccess(AuthenticatableContract $user)
    {
        // Send welcome email
        if (config('rinvex.fort.registration.welcome_email')) {
            $user->notify(new RegistrationSuccessNotification(true));
        }

        // Attach default role to the registered user
        if ($default = $this->app['config']->get('rinvex.fort.registration.default_role')) {
            if ($role = $this->app['rinvex.fort.role']->findBy('slug', $default)) {
                $user->roles()->attach($role);
            }
        }
    }

    /**
     * Listen to the password reset start event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function passwordResetStart(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the password reset success event.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function passwordResetSuccess(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the email verification start.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function emailVerificationStart(AuthenticatableContract $user)
    {
        //
    }

    /**
     * Listen to the email verification success.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function emailVerificationSuccess(AuthenticatableContract $user)
    {
        if (config('rinvex.fort.emailverification.success_notification')) {
            $user->notify(new VerificationSuccessNotification($user->active));
        }
    }

    /**
     * Listen to the ability granting.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string|array                        $action
     * @param string|array                        $resource
     *
     * @return void
     */
    public function abilityGranting(Model $model, $action, $resource)
    {
        //
    }

    /**
     * Listen to the ability granted.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string|array                        $action
     * @param string|array                        $resource
     *
     * @return void
     */
    public function abilityGranted(Model $model, $action, $resource)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the ability revoking.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string|array                        $action
     * @param string|array                        $resource
     *
     * @return void
     */
    public function abilityRevoking(Model $model, $action, $resource)
    {
        //
    }

    /**
     * Listen to the ability revoked.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string|array                        $action
     * @param string|array                        $resource
     *
     * @return void
     */
    public function abilityRevoked(Model $model, $action, $resource)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the ability being created.
     *
     * @param \Rinvex\Fort\Contracts\AbilityRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Ability                      $ability
     *
     * @return void
     */
    public function abilityCreating(AbilityRepositoryContract $repository, Ability $ability)
    {
        //
    }

    /**
     * Listen to the ability created.
     *
     * @param \Rinvex\Fort\Contracts\AbilityRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Ability                      $ability
     *
     * @return void
     */
    public function abilityCreated(AbilityRepositoryContract $repository, Ability $ability)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
    }

    /**
     * Listen to the ability being updated.
     *
     * @param \Rinvex\Fort\Contracts\AbilityRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Ability                      $ability
     *
     * @return void
     */
    public function abilityUpdating(AbilityRepositoryContract $repository, Ability $ability)
    {
        //
    }

    /**
     * Listen to the ability updated.
     *
     * @param \Rinvex\Fort\Contracts\AbilityRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Ability                      $ability
     *
     * @return void
     */
    public function abilityUpdated(AbilityRepositoryContract $repository, Ability $ability)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the ability being deleted.
     *
     * @param \Rinvex\Fort\Contracts\AbilityRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Ability                      $ability
     *
     * @return void
     */
    public function abilityDeleting(AbilityRepositoryContract $repository, Ability $ability)
    {
        //
    }

    /**
     * Listen to the ability deleted.
     *
     * @param \Rinvex\Fort\Contracts\AbilityRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Ability                      $ability
     *
     * @return void
     */
    public function abilityDeleted(AbilityRepositoryContract $repository, Ability $ability)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the role assigning.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $role
     *
     * @return void
     */
    public function roleAssigning(Model $model, $role)
    {
        //
    }

    /**
     * Listen to the role assigned.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $role
     *
     * @return void
     */
    public function roleAssigned(Model $model, $role)
    {
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the role removing.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $role
     *
     * @return void
     */
    public function roleRemoving(Model $model, $role)
    {
        //
    }

    /**
     * Listen to the role removed.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $role
     *
     * @return void
     */
    public function roleRemoved(Model $model, $role)
    {
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the role being created.
     *
     * @param \Rinvex\Fort\Contracts\RoleRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Role                      $model
     *
     * @return void
     */
    public function roleCreating(RoleRepositoryContract $repository, Role $model)
    {
        //
    }

    /**
     * Listen to the role created.
     *
     * @param \Rinvex\Fort\Contracts\RoleRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Role                      $model
     *
     * @return void
     */
    public function roleCreated(RoleRepositoryContract $repository, Role $model)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
    }

    /**
     * Listen to the role being updated.
     *
     * @param \Rinvex\Fort\Contracts\RoleRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Role                      $model
     *
     * @return void
     */
    public function roleUpdating(RoleRepositoryContract $repository, Role $model)
    {
        //
    }

    /**
     * Listen to the role updated.
     *
     * @param \Rinvex\Fort\Contracts\RoleRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Role                      $model
     *
     * @return void
     */
    public function roleUpdated(RoleRepositoryContract $repository, Role $model)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the role being deleted.
     *
     * @param \Rinvex\Fort\Contracts\RoleRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Role                      $model
     *
     * @return void
     */
    public function roleDeleting(RoleRepositoryContract $repository, Role $model)
    {
        //
    }

    /**
     * Listen to the role deleted.
     *
     * @param \Rinvex\Fort\Contracts\RoleRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Role                      $model
     *
     * @return void
     */
    public function roleDeleted(RoleRepositoryContract $repository, Role $model)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the user being created.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $repository
     * @param \Rinvex\Fort\Models\User                      $model
     *
     * @return void
     */
    public function userCreating(UserRepositoryContract $repository, User $model)
    {
        //
    }

    /**
     * Listen to the user created.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $repository
     * @param \Rinvex\Fort\Models\User                      $model
     *
     * @return void
     */
    public function userCreated(UserRepositoryContract $repository, User $model)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
        $this->app['rinvex.fort.persistence']->forgetCache();
    }

    /**
     * Listen to the user being updated.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $repository
     * @param \Rinvex\Fort\Models\User                      $model
     *
     * @return void
     */
    public function userUpdating(UserRepositoryContract $repository, User $model)
    {
        //
    }

    /**
     * Listen to the user updated.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $repository
     * @param \Rinvex\Fort\Models\User                      $model
     *
     * @return void
     */
    public function userUpdated(UserRepositoryContract $repository, User $model)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
        $this->app['rinvex.fort.persistence']->forgetCache();
    }

    /**
     * Listen to the user being deleted.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $repository
     * @param \Rinvex\Fort\Models\User                      $model
     *
     * @return void
     */
    public function userDeleting(UserRepositoryContract $repository, User $model)
    {
        //
    }

    /**
     * Listen to the user deleted.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $repository
     * @param \Rinvex\Fort\Models\User                      $model
     *
     * @return void
     */
    public function userDeleted(UserRepositoryContract $repository, User $model)
    {
        $this->app['rinvex.fort.ability']->forgetCache();
        $this->app['rinvex.fort.role']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
        $this->app['rinvex.fort.persistence']->forgetCache();
    }

    /**
     * Listen to the persistence being created.
     *
     * @param \Rinvex\Fort\Contracts\PersistenceRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Persistence                      $model
     *
     * @return void
     */
    public function persistenceCreating(PersistenceRepositoryContract $repository, Persistence $model)
    {
        //
    }

    /**
     * Listen to the persistence created.
     *
     * @param \Rinvex\Fort\Contracts\PersistenceRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Persistence                      $model
     *
     * @return void
     */
    public function persistenceCreated(PersistenceRepositoryContract $repository, Persistence $model)
    {
        $this->app['rinvex.fort.persistence']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the persistence being updated.
     *
     * @param \Rinvex\Fort\Contracts\PersistenceRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Persistence                      $model
     *
     * @return void
     */
    public function persistenceUpdating(PersistenceRepositoryContract $repository, Persistence $model)
    {
        //
    }

    /**
     * Listen to the persistence updated.
     *
     * @param \Rinvex\Fort\Contracts\PersistenceRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Persistence                      $model
     *
     * @return void
     */
    public function persistenceUpdated(PersistenceRepositoryContract $repository, Persistence $model)
    {
        $this->app['rinvex.fort.persistence']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }

    /**
     * Listen to the persistence being deleted.
     *
     * @param \Rinvex\Fort\Contracts\PersistenceRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Persistence                      $model
     *
     * @return void
     */
    public function persistenceDeleting(PersistenceRepositoryContract $repository, Persistence $model)
    {
        //
    }

    /**
     * Listen to the persistence deleted.
     *
     * @param \Rinvex\Fort\Contracts\PersistenceRepositoryContract $repository
     * @param \Rinvex\Fort\Models\Persistence                      $model
     *
     * @return void
     */
    public function persistenceDeleted(PersistenceRepositoryContract $repository, Persistence $model)
    {
        $this->app['rinvex.fort.persistence']->forgetCache();
        $this->app['rinvex.fort.user']->forgetCache();
    }
}
