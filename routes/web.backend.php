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
    Route::get('/')->name('dashboard.home')->uses('DashboardController@home');

    /*
    |--------------------------------------------------------------------------
    | Abilities Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'abilities.', 'prefix' => 'abilities'], function () {
        Route::get('/')->name('index')->uses('AbilitiesController@index');
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

    Route::group(['as' => 'roles.', 'prefix' => 'roles'], function () {
        Route::get('/')->name('index')->uses('RolesController@index');
        Route::get('{role}')->name('show')->uses('RolesController@show')->where('role', '[a-z0-9_-]+');
        Route::get('{role}/copy')->name('copy')->uses('RolesController@copy')->where('role', '[a-z0-9_-]+');
        Route::get('create')->name('create')->uses('RolesController@create');
        Route::post('create')->name('store')->uses('RolesController@store');
        Route::get('{role}/edit')->name('edit')->uses('RolesController@edit')->where('role', '[a-z0-9_-]+');
        Route::put('{role}/edit')->name('update')->uses('RolesController@update')->where('role', '[a-z0-9_-]+');
        Route::delete('{role}')->name('delete')->uses('RolesController@delete')->where('role', '[a-z0-9_-]+');
    });

    /*
    |--------------------------------------------------------------------------
    | Users Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'users.', 'prefix' => 'users'], function () {
        Route::get('/')->name('index')->uses('UsersController@index');
        Route::get('{user}')->name('show')->uses('UsersController@show')->where('user', '[a-zA-Z0-9_-]+');
        Route::get('{user}/copy')->name('copy')->uses('UsersController@copy')->where('user', '[a-zA-Z0-9_-]+');
        Route::get('create')->name('create')->uses('UsersController@create');
        Route::post('create')->name('store')->uses('UsersController@store');
        Route::get('{user}/edit')->name('edit')->uses('UsersController@edit')->where('user', '[a-zA-Z0-9_-]+');
        Route::put('{user}/edit')->name('update')->uses('UsersController@update')->where('user', '[a-zA-Z0-9_-]+');
        Route::delete('{user}')->name('delete')->uses('UsersController@delete')->where('user', '[a-zA-Z0-9_-]+');
    });
});
