<?php

namespace Zaacom\authentication;

use Zaacom\attributes\Route;
use Zaacom\routing\RouteMethodEnum;

interface AuthenticationInterface
{
	public static function login();

	#[Route(path: '/login', name: 'framework_login', method: RouteMethodEnum::GET)]
	public static function loginPage();

	#[Route(path: '/logout', name: 'framework_logout', method: RouteMethodEnum::POST)]
	public static function logoutPage();
}
