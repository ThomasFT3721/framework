<?php

namespace Zaacom\tests\unit;

use Codeception\Test\Unit;
use unit\controller\TestController;
use UnitTester;
use Zaacom\routing\Route;
use Zaacom\routing\RouteMethodEnum;

class RouteTest extends Unit
{

	protected UnitTester $tester;

	public function testRouteGetFormattedPath()
	{
		$route = new Route(RouteMethodEnum::GET, "/sample", [TestController::class, "index"]);
		$this->tester->assertEquals("/sample", $route->getPathFormatted([]));
	}

	public function testRouteGetFormattedPathWithOneParameter()
	{
		$route = new Route(RouteMethodEnum::GET, "/sample-(.*)", [TestController::class, "index"]);
		$this->tester->assertEquals("/sample-yes", $route->getPathFormatted(["yes"]));
	}

	public function testRouteGetFormattedPathWithMultipleParameters()
	{
		$route = new Route(RouteMethodEnum::GET, "/sample-(.{2})(.{3})", [TestController::class, "index"]);
		$this->tester->assertEquals("/sample-yesno", $route->getPathFormatted(["yes", "no"]));
	}

}
