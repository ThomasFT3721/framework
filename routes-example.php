<?php


use unit\controller\TestController;
use Zaacom\routing\Route;

Route::get("/route_path", [TestController::class, "method_name"])->name("test_route");
Route::get("/route_path/(.{6})", [TestController::class, "method_name_with_parameters"])->name("test_route_2");
