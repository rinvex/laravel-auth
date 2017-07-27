<?php

namespace Rinvex\Fort\Test;

use Mockery;
use Rinvex\Fort\Models\User;
use Rinvex\Fort\Models\Role;
use PHPUnit\Framework\TestCase;
use Rinvex\Fort\Models\Ability;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade as Facade;
use Illuminate\Container\Container as Container;

/**
* Fort User
*/
class UserTest extends TestCase
{

	private $user;

	public function setUp()
	{
		parent::setUp();

		Config::set('app.locale', 'en');
		Config::set('rinvex.fort.tables.users', 'users');
		Config::set('rinvex.fort.models.ability', Ability::class);
		Config::set('rinvex.fort.tables.ability_user', 'ability_user');
		Config::set('rinvex.fort.protected.users', [1]);

		$this->user = new User();

        $app = new Container();
        $app->singleton('app', 'Illuminate\Foundation\Application');

        Facade::setFacadeApplication($app);
        $app->bind('config', 'Illuminate\Config\Repository');

	}

	public function tearDown()
    {
    	Mockery::close();
    }

	public function test_user_is_protected()
	{
        $this->user->id = '1';

        $this->assertTrue($this->user->isProtected());
	}
}