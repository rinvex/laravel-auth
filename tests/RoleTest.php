<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
// use Illuminate\Config\Repository as ConfigClass;
// use Config;
use Illuminate\Support\Facades\Config;
use Mockery as mocker;
use Rinvex\Fort\Models\Role;
use \Illuminate\Container\Container as Container;
use \Illuminate\Support\Facades\Facade as Facade;

class RoleTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		$app = new Container();
		$app->singleton('app', 'Illuminate\Container\Container');

		Facade::setFacadeApplication($app);
		$app->bind('config', 'Illuminate\Config\Repository');
	}

	public function tearDown()
	{
		mocker::close();
	}

	/**
	* Determine if the role is protected.
	*
	* @return bool
	*/
	public function testIsProtected()
	{
		/*
		|------------------------------------------------------------
		| Set
		|------------------------------------------------------------
		*/
		// $role = new \Rinvex\Fort\Models\Role();
		// $role->id = 1;

		/*
		|------------------------------------------------------------
		| Expectation
		|------------------------------------------------------------
		*/

		Config::set('rinvex.fort.protected.roles', [1, 3, 4]);
		Config::set('rinvex.fort.tables.roles', "roles");

		$role = new Role();
		$role->id = 1;

		/*
		|------------------------------------------------------------
		| Assertion
		|------------------------------------------------------------
		*/
		$this->assertTrue($role->isProtected());
	}

}
