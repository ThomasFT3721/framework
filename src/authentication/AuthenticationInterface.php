<?php

namespace Zaacom\authentication;

use Zaacom\attributes\Route;
use Zaacom\routing\RouteMethodEnum;

interface AuthenticationInterface
{
	public static function login();

	#[Route(path: '/login', name: 'framework_login', method: RouteMethodEnum::GET)]
	public function loginPage();

	#[Route(path: '/logout', name: 'framework_logout', method: RouteMethodEnum::POST)]
	public function logoutPage();

	#[Route(path: '/register', name: 'framework_register', method: RouteMethodEnum::GET)]
	public function registerPage();
}
