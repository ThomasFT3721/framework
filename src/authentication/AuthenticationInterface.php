<?php

namespace Zaacom\authentication;

use Zaacom\routing\Route;
use Zaacom\routing\RouteMethodEnum;

interface AuthenticationInterface
{
	public static function login();

	#[\Zaacom\attributes\Route(path: '/login', name: 'framework_login', method: RouteMethodEnum::GET)]
	public static function loginPage();

	#[\Zaacom\attributes\Route(path: '/logout', name: 'framework_logout', method: RouteMethodEnum::POST)]
	public static function logoutPage();
}
