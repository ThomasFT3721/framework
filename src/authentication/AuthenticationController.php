<?php

namespace Zaacom\authentication;

use Zaacom\attributes\enum\AllowPermissionEnum;
use Zaacom\controllers\BaseController;
use Zaacom\routing\Route;
use Zaacom\routing\Router;
use Zaacom\session\USession;

abstract class AuthenticationController extends BaseController implements AuthentificationInterface
{
	/**
	 * @return AllowPermissionEnum[]
	 */
	public static function permission(): array
	{
		return USession::getOrCreate('framework_permissions', []);
	}

	public static function role(): mixed
	{
		return USession::getOrCreate('framework_role');
	}

	public static function user()
	{
		return USession::getOrCreate('framework_user');
	}

	public static function isConnected(): bool
	{
		return USession::getOrCreate('framework_user') !== null;
	}

	private static function disconnect()
	{
		USession::set('framework_role', null);
		USession::set('framework_user', null);
	}

	protected static function connect(mixed $user, mixed $role)
	{
		USession::set('framework_role', $role);
		USession::set('framework_user', $user);
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
