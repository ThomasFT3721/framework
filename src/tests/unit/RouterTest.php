<?php

use Zaacom\Routing\Router;

class RouterTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
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
        \Zaacom\Routing\Route::get("/test", ['TestController', "test"], ["name" => "test"]);
        $this->tester->assertEquals(["GET" => [(new \Zaacom\Routing\Route(\Zaacom\Routing\RouteMethodEnum::GET,'/test', ['TestController', "test"], ["name" => "test"]))]], Router::getRoutes());
    }
}