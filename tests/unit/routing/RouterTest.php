<?php

namespace Zaacom\tests\unit;

use Codeception\Test\Unit;
use unit\controller\TestController;
use UnitTester;
use Zaacom\exception\InvalidNumberArgumentsException;
use Zaacom\exception\RouteMethodNotFoundException;
use Zaacom\exception\RouteNotFoundException;
use Zaacom\exception\UnknownRouteException;
use Zaacom\routing\Route;
use Zaacom\routing\RouteMethodEnum;
use Zaacom\routing\Router;

class RouterTest extends Unit
{

	protected UnitTester $tester;

	public function testAddRoutesGET()
	{
		$route = Route::get("/route", ["a", "b"])->name("test");
		$this->tester->assertContains($route, Router::getRoutes());
	}

	public function testAddRoutesPOST()
	{
		$route = Route::post("/route", ["a", "b"])->name("test");
		$this->tester->assertContains($route, Router::getRoutes());
	}

	public function testAddRoutesDELETE()
	{
		$route = Route::delete("/route", ["a", "b"])->name("test");
		$this->tester->assertContains($route, Router::getRoutes());
	}

	public function testAddRoutesPATCH()
	{
		$route = Route::patch("/route", ["a", "b"])->name("test");
		$this->tester->assertContains($route, Router::getRoutes());
	}

	public function testAddRoutesPUT()
	{
		$route = Route::put("/route", ["a", "b"])->name("test");
		$this->tester->assertContains($route, Router::getRoutes());
	}

	public function testGetRouteUrlByRouteName()
	{
		$route = Route::post("/routePOST", ["c", "d"])->name("test");
		$this->tester->assertEquals($route->getPath(), Router::getRouteUrlByRouteName("test", method: RouteMethodEnum::POST));
	}

	public function testGetRouteUrlByRouteNameThrowableRouteMethodNotFound()
	{
		Router::clearRoutes();
		Route::get("/routePOST", ["c", "d"])->name("test");
		$this->tester->expectThrowable(RouteMethodNotFoundException::class, function () {
			Router::getRouteUrlByRouteName("unknownRoute", method: RouteMethodEnum::POST);
		});
	}

	public function testGetRouteUrlByRouteNameThrowableRouteNotFound()
	{
		Router::clearRoutes();
		Route::get("/routePOST", ["c", "d"])->name("test");
		$this->tester->expectThrowable(RouteNotFoundException::class, function () {
			Router::getRouteUrlByRouteName("unknownRoute", method: RouteMethodEnum::GET);
		});
	}

	public function testGetRouteUrlByRouteNameThrowableMethodDifferent()
	{
		Router::clearRoutes();
		Route::get("/routePOST", ["c", "d"])->name("test");
		$this->tester->expectThrowable(\Exception::class, function () {
			Router::getRouteUrlByRouteName("test", method: RouteMethodEnum::POST);
		});
	}

	public function testGetRoutesThrowable()
	{
		$this->tester->expectThrowable(\TypeError::class, function () {
			Router::getRoutes("UNKNOWN");
		});
	}

	public function testRunSample()
	{
		Router::clearRoutes();
		$route = Route::get("/route", [TestController::class, "index"])->name("test");
		$this->tester->assertEquals($route, Router::run("/route"));
	}

	public function testRunWithParameters()
	{
		Router::clearRoutes();
		Route::get("/route([0-9]{3})No", [TestController::class, "testWithOneParameter"])->name("test");
		Route::get("/route(.*)OrNo", [TestController::class, "testWithOneParameter"])->name("test2");
		$route = Route::get("/route([0-9]{3})OrNo([456]{3})", [TestController::class, "testWithTwoParameter"])->name("test3");
		$this->tester->assertEquals($route, Router::run("/route123OrNo456"));
	}

	public function testRunThrowableNoRouteMatch()
	{
		Router::clearRoutes();
		Route::get("/route", [TestController::class, "index"])->name("test");
		$this->tester->expectThrowable(UnknownRouteException::class, function () {
			Router::run("/routeOtherUrl");
		});
	}

	public function testRunThrowableInvalidNumberArgumentsException()
	{
		Router::clearRoutes();
		Route::get("/route", [TestController::class])->name("test");
		$this->tester->expectThrowable(InvalidNumberArgumentsException::class, function () {
			Router::run("/route");
		});
	}
}
