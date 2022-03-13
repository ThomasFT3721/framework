<?php

namespace Zaacom\authentication;

use Zaacom\attributes\enum\AllowPermissionEnum;
use Zaacom\controllers\BaseController;
use Zaacom\routing\Route;
use Zaacom\routing\Router;
use Zaacom\accessor\ZSession;

abstract class AuthenticationController extends BaseController implements AuthenticationInterface
{
	/**
	 * @return AllowPermissionEnum[]
	 */
	public static function permission(): array
	{
		return ZSession::getOrCreate('framework_permissions', []);
	}

	public static function role(): mixed
	{
		return ZSession::getOrCreate('framework_role');
	}

	public static function user()
	{
		return ZSession::getOrCreate('framework_user');
	}

	public static function isConnected(): bool
	{
		return ZSession::getOrCreate('framework_user') !== null;
	}

	private static function disconnect()
	{
		ZSession::set('framework_role', null);
		ZSession::set('framework_user', null);
	}

	protected static function connect(mixed $user, mixed $role)
	{
		ZSession::set('framework_role', $role);
		ZSession::set('framework_user', $user);
	}

	public static function logout()
	{
		self::disconnect();
	}

	static function redirectTo(Route $route): void
	{
		Router::redirectToUrl("/");
	}
}
