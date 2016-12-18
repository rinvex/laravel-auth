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
    Route::get('/')->name('dashboard.home')->uses('DashboardController@home');

    /*
    |--------------------------------------------------------------------------
    | Abilities Routes
    |--------------------------------------------------------------------------
    */

    Route::bind('ability', function ($id) {
        return app('rinvex.fort.ability')->findOrFail($id);
    });

    Route::group(['as' => 'abilities.', 'prefix' => 'abilities'], function () {
        Route::get('/')->name('index')->uses('AbilitiesController@index');
        Route::put('/')->name('bulk')->uses('AbilitiesController@bulk');

        Route::post('import')->name('import')->uses('AbilitiesController@import');
        Route::post('export')->name('export')->uses('AbilitiesController@export');

        Route::get('{ability}')->name('show')->uses('AbilitiesController@show')->where('ability', '[0-9]+');

        Route::get('{ability}/copy')->name('copy')->uses('AbilitiesController@copy')->where('ability', '[0-9]+');
        Route::get('create')->name('create')->uses('AbilitiesController@create');
        Route::post('create')->name('store')->uses('AbilitiesController@store');

        Route::get('{ability}/edit')->name('edit')->uses('AbilitiesController@edit')->where('ability', '[0-9]+');
        Route::put('{ability}/edit')->name('update')->uses('AbilitiesController@update')->where('ability', '[0-9]+');

        Route::delete('{ability}')->name('delete')->uses('AbilitiesController@delete')->where('ability', '[0-9]+');
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
        Route::get('/')->name('index')->uses('RolesController@index');
        Route::put('/')->name('bulk')->uses('RolesController@bulk');

        Route::post('import')->name('import')->uses('RolesController@import');
        Route::post('export')->name('export')->uses('RolesController@export');

        Route::get('{role}')->name('show')->uses('RolesController@show')->where('role', '[0-9]+');

        Route::get('{role}/copy')->name('copy')->uses('RolesController@copy')->where('role', '[0-9]+');
        Route::get('create')->name('create')->uses('RolesController@create');
        Route::post('create')->name('store')->uses('RolesController@store');

        Route::get('{role}/edit')->name('edit')->uses('RolesController@edit')->where('role', '[0-9]+');
        Route::put('{role}/edit')->name('update')->uses('RolesController@update')->where('role', '[0-9]+');

        Route::delete('{role}')->name('delete')->uses('RolesController@delete')->where('role', '[0-9]+');
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
        Route::get('/')->name('index')->uses('UsersController@index');
        Route::put('/')->name('bulk')->uses('UsersController@bulk');

        Route::post('import')->name('import')->uses('UsersController@import');
        Route::post('export')->name('export')->uses('UsersController@export');

        Route::get('{user}')->name('show')->uses('UsersController@show')->where('user', '[0-9]+');

        Route::get('{user}/copy')->name('copy')->uses('UsersController@copy')->where('user', '[0-9]+');
        Route::get('create')->name('create')->uses('UsersController@create');
        Route::post('create')->name('store')->uses('UsersController@store');

        Route::get('{user}/edit')->name('edit')->uses('UsersController@edit')->where('user', '[0-9]+');
        Route::put('{user}/edit')->name('update')->uses('UsersController@update')->where('user', '[0-9]+');

        Route::delete('{user}')->name('delete')->uses('UsersController@delete')->where('user', '[0-9]+');
    });
});
