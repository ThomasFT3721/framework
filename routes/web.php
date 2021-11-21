<?php

use App\Routes\Route;


Route::get(["", "/"], [\Controllers\TestController::class, "test"], ["name" => "test"]);
Route::get("/test", [\Controllers\TestController::class, "test"], ["name" => "test"]);
Route::get("/test/{bcd}", [\Controllers\TestController::class, "test"], ["name" => "blabla"]);
Route::get("/test/{bcd}/{def}", [\Controllers\TestController::class, "test"], ["name" => "boum"]);