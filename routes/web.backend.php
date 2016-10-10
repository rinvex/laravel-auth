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
    'middleware' => ['rinvex.fort.backend', 'can:access-dashboard'],
    'as'         => 'rinvex.fort.backend.',
    'namespace'  => 'Rinvex\Fort\Http\Controllers\Backend',
], function () {
    Route::get('/', ['as' => 'dashboard.home', 'uses' => 'DashboardController@home']);


    /*
    |--------------------------------------------------------------------------
    | Abilities Routes
    |--------------------------------------------------------------------------
    */

    Route::bind('ability', function ($id) {
        return app('rinvex.fort.ability')->findOrFail($id);
    });

    Route::group(['as' => 'abilities.', 'prefix' => 'abilities'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'AbilitiesController@index']);
        Route::put('/', ['as' => 'bulk', 'uses' => 'AbilitiesController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'AbilitiesController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'AbilitiesController@export']);

        Route::get('{ability}', ['as' => 'show', 'uses' => 'AbilitiesController@show'])->where('ability', '[0-9]+');

        Route::get('{ability}/copy', ['as' => 'copy', 'uses' => 'AbilitiesController@copy'])->where('ability', '[0-9]+');
        Route::get('create', ['as' => 'create', 'uses' => 'AbilitiesController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'AbilitiesController@store']);

        Route::get('{ability}/edit', ['as' => 'edit', 'uses' => 'AbilitiesController@edit'])->where('ability', '[0-9]+');
        Route::put('{ability}/edit', ['as' => 'update', 'uses' => 'AbilitiesController@update'])->where('ability', '[0-9]+');

        Route::delete('{ability}', ['as' => 'delete', 'uses' => 'AbilitiesController@delete'])->where('ability', '[0-9]+');
    });


    /*
    |--------------------------------------------------------------------------
    | Roles Routes
    |--------------------------------------------------------------------------
    */

    Route::bind('role', function ($id) {
        return app('rinvex.fort.role')->findOrFail($id);
    });

    Route::group(['as' => 'roles.', 'prefix' => 'roles'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'RolesController@index']);
        Route::put('/', ['as' => 'bulk', 'uses' => 'RolesController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'RolesController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'RolesController@export']);

        Route::get('{role}', ['as' => 'show', 'uses' => 'RolesController@show'])->where('role', '[0-9]+');

        Route::get('{role}/copy', ['as' => 'copy', 'uses' => 'RolesController@copy'])->where('role', '[0-9]+');
        Route::get('create', ['as' => 'create', 'uses' => 'RolesController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'RolesController@store']);

        Route::get('{role}/edit', ['as' => 'edit', 'uses' => 'RolesController@edit'])->where('role', '[0-9]+');
        Route::put('{role}/edit', ['as' => 'update', 'uses' => 'RolesController@update'])->where('role', '[0-9]+');

        Route::delete('{role}', ['as' => 'delete', 'uses' => 'RolesController@delete'])->where('role', '[0-9]+');
    });


    /*
    |--------------------------------------------------------------------------
    | Users Routes
    |--------------------------------------------------------------------------
    */

    Route::bind('user', function ($id) {
        return app('rinvex.fort.user')->findOrFail($id);
    });

    Route::group(['as' => 'users.', 'prefix' => 'users'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'UsersController@index']);
        Route::put('/', ['as' => 'bulk', 'uses' => 'UsersController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'UsersController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'UsersController@export']);

        Route::get('{user}', ['as' => 'show', 'uses' => 'UsersController@show'])->where('user', '[0-9]+');

        Route::get('{user}/copy', ['as' => 'copy', 'uses' => 'UsersController@copy'])->where('user', '[0-9]+');
        Route::get('create', ['as' => 'create', 'uses' => 'UsersController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'UsersController@store']);

        Route::get('{user}/edit', ['as' => 'edit', 'uses' => 'UsersController@edit'])->where('user', '[0-9]+');
        Route::put('{user}/edit', ['as' => 'update', 'uses' => 'UsersController@update'])->where('user', '[0-9]+');

        Route::delete('{user}', ['as' => 'delete', 'uses' => 'UsersController@delete'])->where('user', '[0-9]+');
    });
});
