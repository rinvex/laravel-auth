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
        Route::post('/', ['as' => 'bulk', 'uses' => 'AbilitiesController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'AbilitiesController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'AbilitiesController@export']);

        Route::get('{ability}', ['as' => 'show', 'uses' => 'AbilitiesController@show']);

        Route::get('{ability}/copy', ['as' => 'copy', 'uses' => 'AbilitiesController@copy']);

        Route::get('{ability}/edit', ['as' => 'edit', 'uses' => 'AbilitiesController@edit']);
        Route::post('{ability}/edit', ['as' => 'update', 'uses' => 'AbilitiesController@update']);

        Route::get('create', ['as' => 'create', 'uses' => 'AbilitiesController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'AbilitiesController@store']);

        Route::delete('{ability}', ['as' => 'destroy', 'uses' => 'AbilitiesController@destroy']);

    });


    /*
    |--------------------------------------------------------------------------
    | Roles Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'roles.', 'prefix' => 'roles'], function () {

        Route::get('/', ['as' => 'index', 'uses' => 'RolesController@index']);
        Route::post('/', ['as' => 'bulk', 'uses' => 'RolesController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'RolesController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'RolesController@export']);

        Route::get('{role}', ['as' => 'show', 'uses' => 'RolesController@show']);

        Route::get('{role}/copy', ['as' => 'copy', 'uses' => 'RolesController@copy']);

        Route::get('{role}/edit', ['as' => 'edit', 'uses' => 'RolesController@edit']);
        Route::post('{role}/edit', ['as' => 'update', 'uses' => 'RolesController@update']);

        Route::get('create', ['as' => 'create', 'uses' => 'RolesController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'RolesController@store']);

        Route::delete('{role}', ['as' => 'destroy', 'uses' => 'RolesController@destroy']);

    });


    /*
    |--------------------------------------------------------------------------
    | Users Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'users.', 'prefix' => 'users'], function () {

        Route::get('/', ['as' => 'index', 'uses' => 'UsersController@index']);
        Route::post('/', ['as' => 'bulk', 'uses' => 'UsersController@bulk']);

        Route::post('import', ['as' => 'import', 'uses' => 'UsersController@import']);
        Route::post('export', ['as' => 'export', 'uses' => 'UsersController@export']);

        Route::get('{user}', ['as' => 'show', 'uses' => 'UsersController@show']);

        Route::get('{user}/copy', ['as' => 'copy', 'uses' => 'UsersController@copy']);

        Route::get('{user}/edit', ['as' => 'edit', 'uses' => 'UsersController@edit']);
        Route::post('{user}/edit', ['as' => 'update', 'uses' => 'UsersController@update']);

        Route::get('create', ['as' => 'create', 'uses' => 'UsersController@create']);
        Route::post('create', ['as' => 'store', 'uses' => 'UsersController@store']);

        Route::delete('{user}', ['as' => 'destroy', 'uses' => 'UsersController@destroy']);

    });


});
