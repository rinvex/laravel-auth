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

    // Global abilities
    [
        'action'   => 'superadmin',
        'resource' => 'global',
        'title'    => 'Super Administrator',
    ],


    // Dashboard abilities
    [
        'action'   => 'access',
        'resource' => 'dashboard',
        'policy'   => 'Rinvex\Fort\Policies\DashboardPolicy@access',
        'title'    => 'Access Dashboard',
    ],


    // Abilities abilities
    [
        'action'   => 'view',
        'resource' => 'ability',
        'policy'   => 'Rinvex\Fort\Policies\AbilityPolicy@view',
        'title'    => 'Can View Existing Abilities',
    ],

    [
        'action'   => 'create',
        'resource' => 'ability',
        'policy'   => 'Rinvex\Fort\Policies\AbilityPolicy@create',
        'title'    => 'Can Create New Abilities',
    ],

    [
        'action'   => 'edit',
        'resource' => 'ability',
        'policy'   => 'Rinvex\Fort\Policies\AbilityPolicy@edit',
        'title'    => 'Can Edit Existing Abilities',
    ],

    [
        'action'   => 'delete',
        'resource' => 'ability',
        'policy'   => 'Rinvex\Fort\Policies\AbilityPolicy@delete',
        'title'    => 'Can Delete Existing Abilities',
    ],

    [
        'action'   => 'import',
        'resource' => 'ability',
        'policy'   => 'Rinvex\Fort\Policies\AbilityPolicy@import',
        'title'    => 'Can Import New Abilities',
    ],

    [
        'action'   => 'export',
        'resource' => 'ability',
        'policy'   => 'Rinvex\Fort\Policies\AbilityPolicy@export',
        'title'    => 'Can Export Existing Abilities',
    ],

    [
        'action'   => 'give',
        'resource' => 'ability',
        'policy'   => 'Rinvex\Fort\Policies\AbilityPolicy@give',
        'title'    => 'Can Give Abilities To Users',
    ],

    [
        'action'   => 'revoke',
        'resource' => 'ability',
        'policy'   => 'Rinvex\Fort\Policies\AbilityPolicy@revoke',
        'title'    => 'Can Revoke Abilities From Users',
    ],


    // Roles abilities
    [
        'action'   => 'view',
        'resource' => 'role',
        'policy'   => 'Rinvex\Fort\Policies\RolePolicy@view',
        'title'    => 'Can View Existing Roles',
    ],

    [
        'action'   => 'create',
        'resource' => 'role',
        'policy'   => 'Rinvex\Fort\Policies\RolePolicy@create',
        'title'    => 'Can Create New Roles',
    ],

    [
        'action'   => 'edit',
        'resource' => 'role',
        'policy'   => 'Rinvex\Fort\Policies\RolePolicy@edit',
        'title'    => 'Can Edit Existing Roles',
    ],

    [
        'action'   => 'delete',
        'resource' => 'role',
        'policy'   => 'Rinvex\Fort\Policies\RolePolicy@delete',
        'title'    => 'Can Delete Existing Roles',
    ],

    [
        'action'   => 'import',
        'resource' => 'role',
        'policy'   => 'Rinvex\Fort\Policies\RolePolicy@import',
        'title'    => 'Can Import New Roles',
    ],

    [
        'action'   => 'export',
        'resource' => 'role',
        'policy'   => 'Rinvex\Fort\Policies\RolePolicy@export',
        'title'    => 'Can Export Existing Roles',
    ],

    [
        'action'   => 'assign',
        'resource' => 'role',
        'policy'   => 'Rinvex\Fort\Policies\RolePolicy@assign',
        'title'    => 'Can Assign Roles To Users',
    ],

    [
        'action'   => 'remove',
        'resource' => 'role',
        'policy'   => 'Rinvex\Fort\Policies\RolePolicy@remove',
        'title'    => 'Can Remove Roles From Users',
    ],


    // Users abilities
    [
        'action'   => 'view',
        'resource' => 'user',
        'policy'   => 'Rinvex\Fort\Policies\UserPolicy@view',
        'title'    => 'Can View Existing Users',
    ],

    [
        'action'   => 'create',
        'resource' => 'user',
        'policy'   => 'Rinvex\Fort\Policies\UserPolicy@create',
        'title'    => 'Can Create New Users',
    ],

    [
        'action'   => 'edit',
        'resource' => 'user',
        'policy'   => 'Rinvex\Fort\Policies\UserPolicy@edit',
        'title'    => 'Can Edit Existing Users',
    ],

    [
        'action'   => 'delete',
        'resource' => 'user',
        'policy'   => 'Rinvex\Fort\Policies\UserPolicy@delete',
        'title'    => 'Can Delete Existing Users',
    ],

    [
        'action'   => 'import',
        'resource' => 'user',
        'policy'   => 'Rinvex\Fort\Policies\UserPolicy@import',
        'title'    => 'Can Import New Users',
    ],

    [
        'action'   => 'export',
        'resource' => 'user',
        'policy'   => 'Rinvex\Fort\Policies\UserPolicy@export',
        'title'    => 'Can Export Existing Users',
    ],

    [
        'action'   => 'activate',
        'resource' => 'user',
        'policy'   => 'Rinvex\Fort\Policies\UserPolicy@activate',
        'title'    => 'Can activate Users',
    ],

    [
        'action'   => 'deactivate',
        'resource' => 'user',
        'policy'   => 'Rinvex\Fort\Policies\UserPolicy@deactivate',
        'title'    => 'Can De-activate Users',
    ],

];
