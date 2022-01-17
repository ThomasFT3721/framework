<?php

namespace unit\environment;

use UnitTester;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;

class EnvironmentVariableTest extends \Codeception\Test\Unit
{

	protected UnitTester $tester;

	public function testGet()
	{
		$this->tester->assertEquals("", EnvironmentVariable::get(EnvironmentVariablesIdentifiers::APP_NAME));
	}

	public function testSet()
	{
		EnvironmentVariable::set(EnvironmentVariablesIdentifiers::APP_NAME, "test");
		$this->tester->assertEquals("test", EnvironmentVariable::get(EnvironmentVariablesIdentifiers::APP_NAME));
	}
}
