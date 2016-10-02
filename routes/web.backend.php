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

Route::group([
    'prefix'     => 'backend',
    'middleware' => ['web', 'can:access-dashboard'],
    'as'         => 'rinvex.fort.backend.',
    'namespace'  => 'Rinvex\Fort\Http\Controllers\Backend',
], function () {
    Route::get('/', ['as' => 'dashboard.home', 'uses' => 'DashboardController@home']);


    /*
    |--------------------------------------------------------------------------
    | Abilities Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'abilities.', 'prefix' => 'abilities'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'AbilitiesController@index']);
        Route::put('/', ['as' => 'bulk', 'uses' => 'AbilitiesController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'AbilitiesController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'AbilitiesController@export']);

        Route::get('{abilityId}', ['as' => 'show', 'uses' => 'AbilitiesController@show'])->where('abilityId', '[0-9]+');

        Route::get('{abilityId}/copy', ['as' => 'copy', 'uses' => 'AbilitiesController@copy'])->where('abilityId', '[0-9]+');
        Route::get('create', ['as' => 'create', 'uses' => 'AbilitiesController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'AbilitiesController@store']);

        Route::get('{abilityId}/edit', ['as' => 'edit', 'uses' => 'AbilitiesController@edit'])->where('abilityId', '[0-9]+');
        Route::put('{abilityId}/edit', ['as' => 'update', 'uses' => 'AbilitiesController@update'])->where('abilityId', '[0-9]+');

        Route::delete('{abilityId}', ['as' => 'delete', 'uses' => 'AbilitiesController@delete'])->where('abilityId', '[0-9]+');
    });


    /*
    |--------------------------------------------------------------------------
    | Roles Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'roles.', 'prefix' => 'roles'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'RolesController@index']);
        Route::put('/', ['as' => 'bulk', 'uses' => 'RolesController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'RolesController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'RolesController@export']);

        Route::get('{roleId}', ['as' => 'show', 'uses' => 'RolesController@show'])->where('roleId', '[0-9]+');

        Route::get('{roleId}/copy', ['as' => 'copy', 'uses' => 'RolesController@copy'])->where('roleId', '[0-9]+');
        Route::get('create', ['as' => 'create', 'uses' => 'RolesController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'RolesController@store']);

        Route::get('{roleId}/edit', ['as' => 'edit', 'uses' => 'RolesController@edit'])->where('roleId', '[0-9]+');
        Route::put('{roleId}/edit', ['as' => 'update', 'uses' => 'RolesController@update'])->where('roleId', '[0-9]+');

        Route::delete('{roleId}', ['as' => 'delete', 'uses' => 'RolesController@delete'])->where('roleId', '[0-9]+');
    });


    /*
    |--------------------------------------------------------------------------
    | Users Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'users.', 'prefix' => 'users'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'UsersController@index']);
        Route::put('/', ['as' => 'bulk', 'uses' => 'UsersController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'UsersController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'UsersController@export']);

        Route::get('{userId}', ['as' => 'show', 'uses' => 'UsersController@show'])->where('userId', '[0-9]+');

        Route::get('{userId}/copy', ['as' => 'copy', 'uses' => 'UsersController@copy'])->where('userId', '[0-9]+');
        Route::get('create', ['as' => 'create', 'uses' => 'UsersController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'UsersController@store']);

        Route::get('{userId}/edit', ['as' => 'edit', 'uses' => 'UsersController@edit'])->where('userId', '[0-9]+');
        Route::put('{userId}/edit', ['as' => 'update', 'uses' => 'UsersController@update'])->where('userId', '[0-9]+');

        Route::delete('{userId}', ['as' => 'delete', 'uses' => 'UsersController@delete'])->where('userId', '[0-9]+');
    });
});
