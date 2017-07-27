<?php

namespace Rinvex\Fort\Test;

use Mockery as mocker;
use Rinvex\Fort\Models\User;
use PHPUnit\Framework\TestCase;

/**
* Fort User
*/
class UserTest extends TestCase
{
	public function test_user_is_superadmin()
	{
		/*
		|------------------------------------------------------------
        | Set
        |------------------------------------------------------------
		*/
		$user = mocker::mock('Rinvex\Fort\Models\User');
		
		/*
		|------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
		*/
        $user->shouldReceive('isSuperadmin')
        	->andReturn(true)
        	->once();

		/*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($user->isSuperadmin());
	}
}