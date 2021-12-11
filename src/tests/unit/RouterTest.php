<?php

use Zaacom\routing\Route;
use Zaacom\routing\RouteMethodEnum;
use Zaacom\routing\Router;

class RouterTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests

    /**
     * @throws Exception
     */
    public function testAddRoute()
    {
        Route::get("/test", ['TestController', "test"], ["name" => "test"]);
        $this->tester->assertEquals(["GET" => [(new Route(RouteMethodEnum::GET,'/test', ['TestController', "test"], ["name" => "test"]))]], Router::getRoutes());
    }
}
