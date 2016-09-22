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
        'action'      => 'superadmin',
        'title'       => 'Super Administrator',
    ],


    // Dashboard abilities
    [
        'action'      => 'access-dashboard',
        'policy'      => 'Rinvex\Fort\Policies\DashboardPolicy@access',
        'title'       => 'Access Dashboard',
    ],


    // Abilities abilities
    [
        'action'      => 'view-ability',
        'policy'      => 'Rinvex\Fort\Policies\AbilityPolicy@view',
        'title'       => 'Can View Existing Abilities',
    ],

    [
        'action'      => 'create-ability',
        'policy'      => 'Rinvex\Fort\Policies\AbilityPolicy@create',
        'title'       => 'Can Create New Abilities',
    ],

    [
        'action'      => 'edit-ability',
        'policy'      => 'Rinvex\Fort\Policies\AbilityPolicy@edit',
        'title'       => 'Can Edit Existing Abilities',
    ],

    [
        'action'      => 'delete-ability',
        'policy'      => 'Rinvex\Fort\Policies\AbilityPolicy@delete',
        'title'       => 'Can Delete Existing Abilities',
    ],

    [
        'action'      => 'import-ability',
        'policy'      => 'Rinvex\Fort\Policies\AbilityPolicy@import',
        'title'       => 'Can Import New Abilities',
    ],

    [
        'action'      => 'export-ability',
        'policy'      => 'Rinvex\Fort\Policies\AbilityPolicy@export',
        'title'       => 'Can Export Existing Abilities',
    ],

    [
        'action'      => 'give-ability',
        'policy'      => 'Rinvex\Fort\Policies\AbilityPolicy@give',
        'title'       => 'Can Give Abilities To Users',
    ],

    [
        'action'      => 'revoke-ability',
        'policy'      => 'Rinvex\Fort\Policies\AbilityPolicy@revoke',
        'title'       => 'Can Revoke Abilities From Users',
    ],


    // Roles abilities
    [
        'action'      => 'view-role',
        'policy'      => 'Rinvex\Fort\Policies\RolePolicy@view',
        'title'       => 'Can View Existing Roles',
    ],

    [
        'action'      => 'create-role',
        'policy'      => 'Rinvex\Fort\Policies\RolePolicy@create',
        'title'       => 'Can Create New Roles',
    ],

    [
        'action'      => 'edit-role',
        'policy'      => 'Rinvex\Fort\Policies\RolePolicy@edit',
        'title'       => 'Can Edit Existing Roles',
    ],

    [
        'action'      => 'delete-role',
        'policy'      => 'Rinvex\Fort\Policies\RolePolicy@delete',
        'title'       => 'Can Delete Existing Roles',
    ],

    [
        'action'      => 'import-role',
        'policy'      => 'Rinvex\Fort\Policies\RolePolicy@import',
        'title'       => 'Can Import New Roles',
    ],

    [
        'action'      => 'export-role',
        'policy'      => 'Rinvex\Fort\Policies\RolePolicy@export',
        'title'       => 'Can Export Existing Roles',
    ],

    [
        'action'      => 'assign-role',
        'policy'      => 'Rinvex\Fort\Policies\RolePolicy@assign',
        'title'       => 'Can Assign Roles To Users',
    ],

    [
        'action'      => 'remove-role',
        'policy'      => 'Rinvex\Fort\Policies\RolePolicy@remove',
        'title'       => 'Can Remove Roles From Users',
    ],


    // Users abilities
    [
        'action'      => 'view-user',
        'policy'      => 'Rinvex\Fort\Policies\UserPolicy@view',
        'title'       => 'Can View Existing Users',
    ],

    [
        'action'      => 'create-user',
        'policy'      => 'Rinvex\Fort\Policies\UserPolicy@create',
        'title'       => 'Can Create New Users',
    ],

    [
        'action'      => 'edit-user',
        'policy'      => 'Rinvex\Fort\Policies\UserPolicy@edit',
        'title'       => 'Can Edit Existing Users',
    ],

    [
        'action'      => 'delete-user',
        'policy'      => 'Rinvex\Fort\Policies\UserPolicy@delete',
        'title'       => 'Can Delete Existing Users',
    ],

    [
        'action'      => 'import-user',
        'policy'      => 'Rinvex\Fort\Policies\UserPolicy@import',
        'title'       => 'Can Import New Users',
    ],

    [
        'action'      => 'export-user',
        'policy'      => 'Rinvex\Fort\Policies\UserPolicy@export',
        'title'       => 'Can Export Existing Users',
    ],

    [
        'action'      => 'activate-user',
        'policy'      => 'Rinvex\Fort\Policies\UserPolicy@activate',
        'title'       => 'Can activate Users',
    ],

    [
        'action'      => 'deactivate-user',
        'policy'      => 'Rinvex\Fort\Policies\UserPolicy@deactivate',
        'title'       => 'Can De-activate Users',
    ],

];
