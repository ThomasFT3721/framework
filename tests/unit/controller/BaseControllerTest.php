<?php

namespace unit\controller;

use BadMethodCallException;
use UnitTester;

class BaseControllerTest extends \Codeception\Test\Unit
{

	protected UnitTester $tester;

	public function testUndefinedMethodName()
	{
		$this->tester->expectThrowable(BadMethodCallException::class, function () {
			$controller = new TestController();
			$controller->getUndefinedMethodBlaBla();
		});
	}
}
