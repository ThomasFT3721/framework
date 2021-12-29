<?php

namespace Zaacom\tests\unit;

use Codeception\Test\Unit;
use UnitTester;
use Zaacom\routing\Route;
use Zaacom\routing\RouteMethodEnum;
use Zaacom\routing\Router;

class RouterTest extends Unit
{

	protected UnitTester $tester;

	public function testGetRoutesGET()
	{
		Route::get(["/route"], ["a", "b"], ["name" => "route_name"]);
		$this->tester->assertArrayHasKey(RouteMethodEnum::GET, Router::getRoutes());
	}

	public function testGetRoutesPOST()
	{
		Route::post(["/route"], ["c", "d"], ["name" => "route_name"]);
		$this->tester->assertArrayHasKey(RouteMethodEnum::POST, Router::getRoutes());
	}

	public function testGetRouteUrlPOST()
	{
		Route::post(["/route"], ["c", "d"], ["name" => "route_name"]);
		$this->tester->assertEquals("/route", Router::getRouteUrl("route_name", method: RouteMethodEnum::POST));
	}

	public function testGetRoutesThrowable()
	{
		$this->tester->expectThrowable(\Exception::class, function () {
			Router::getRoutes("UNKNOWN");
		});
	}

	public function testAdd()
	{
		Route::delete(["/route"], ["e", "f"], ["name" => "route_name"]);
		$this->tester->assertCount(1, Router::getRoutes(RouteMethodEnum::DELETE));
	}
}