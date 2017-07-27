<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Config;
use Mockery as mocker;
use Rinvex\Fort\Models\Ability;
use \Illuminate\Container\Container as Container;
use \Illuminate\Support\Facades\Facade as Facade;

class AbilityTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		$app = new Container();
		$app->singleton('app', 'Illuminate\Container\Container');

		Facade::setFacadeApplication($app);
		$app->bind('config', 'Illuminate\Config\Repository');

    Config::set('rinvex.fort.protected.abilities', [1]);
		Config::set('rinvex.fort.tables.abilities', "abilities");
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
		$ability = new Ability();
		$ability->id = 1;

		/*
		|------------------------------------------------------------
		| Assertion
		|------------------------------------------------------------
		*/
		$this->assertTrue($ability->isProtected());
	}

  public function testIsSuperadmin(){

    $ability = new Ability();
    $ability->policy = false;
    $ability->resource = 'global';
    $ability->action = 'superadmin';

    $this->assertTrue($ability->isSuperadmin());
  }

}
