<?php

namespace Rinvex\Fort\Test\Services;

use Mockery as m;
use Rinvex\Fort\Models\User;
use PHPUnit\Framework\TestCase;

use Rinvex\Fort\Guards\SessionGuard;
use Illuminate\Support\Facades\Config;
use \Illuminate\Session\Session;
use Illuminate\Contracts\Auth\UserProvider;
use \Symfony\Component\HttpFoundation\Request;
use \Illuminate\Support\Facades\Facade as Facade;
use \Illuminate\Container\Container as Container;
use \Illuminate\Contracts\Cache\Repository as Cache;
use \Illuminate\Cache\Repository as CacheImplementation;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\RateLimiter;

use Rinvex\Fort\Services\AccessGate;
use Rinvex\Fort\Exceptions\AuthorizationException;

/*
* Fort SessionGuard
*/
class AccessGateTest extends TestCase
{

	public $app = null;

	public function setup()
	{
		parent::setup();

		$this->app = new Container();
		$this->app->bind('config', 'Illuminate\Config\Repository');
		Facade::setFacadeApplication($this->app);

	}

	public function test_gate_authorize_create_ability(){

		// arrange
		$ag = new AccessGate(new Container, function(){
				return new \stdClass;
		}, 

		$abilities = ["create" =>  function(){ return true; }], [],[function(){}],[function(){}]);

		/// act and assert
		$this->assertTrue($ag->authorize("create") instanceof \Illuminate\Auth\Access\Response);
	}


	public function test_authorize_exception(){

		// arrange
		$ag = new AccessGate(new Container, function(){
				return new \stdClass;
		}, 
		$abilities = ["create" =>  function(){ return true; }], [],[function(){}],[function(){}]);
		
		$this->expectException(AuthorizationException::class);
		
		/// act and assert
		$this->assertTrue($ag->authorize("update") instanceof \Illuminate\Auth\Access\Response);
	}

}