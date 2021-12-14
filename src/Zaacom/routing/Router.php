<?php

namespace Zaacom\routing;

use Exception;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;
use Zaacom\exception\InvalidNumberArguments;
use Zaacom\exception\InvalidNumberArgumentsException;
use Zaacom\exception\UnknownRouteException;

abstract class Router
{

	private static array $routes = [];

	public static function add(string $method, string $path, array|string $action, array $options = []): Route
	{
		if (!array_key_exists($method, self::$routes)) {
			self::$routes[$method] = [];
		}
		$route = new Route($method, $path, $action, $options);
		self::$routes[$method][] = $route;
		return $route;
	}

	private static function includeRoutes()
	{
		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/../../../../../..");
		}
		require_once __DIR__ . '/admin.php';
		if (!is_dir(ROOT_DIR . "/routes")) {
			mkdir(ROOT_DIR . "/routes");
		}
		foreach (glob(ROOT_DIR . "/routes/*.php", GLOB_BRACE) as $filePath) {
			require_once ROOT_DIR . str_replace(ROOT_DIR, "", $filePath);
		}
	}

	/**
	 * @throws UnknownRouteException
	 * @throws InvalidNumberArgumentsException
	 */
	public static function run()
	{
		self::includeRoutes();
		$route = null;
		$params = [];
		foreach (self::$routes as $arr) {
			foreach ($arr as $r) {
				if (($matched = self::matchWith($r))[0]) {
					$route = $r;
					unset($matched[1][0]);
					$params = array_values($matched[1]);
				}
			}
		}

		if ($route === null) {
			throw new UnknownRouteException(SERVER_REQUEST_URI_PARSED);
		}
		if (count($route->getAction()) != 2) {
			throw new InvalidNumberArgumentsException($route->getAction(), 2);
		}
		call_user_func_array([new ($route->getAction()[0]), $route->getAction()[1]], $params);
	}

	private static function matchWith(Route $route): array
	{
		preg_match($route->getRegexPath(), SERVER_REQUEST_URI_PARSED, $matches);
		return [count($matches) > 0 && $matches[0] == SERVER_REQUEST_URI_PARSED, $matches];
	}

	/**
	 * Get the value of routes
	 *
	 * @param string|null $method
	 *
	 * @return array
	 * @throws Exception
	 */
	public static function getRoutes(?string $method = null): array
	{
		if ($method == null) {
			return self::$routes;
		}
		if (array_key_exists($method, self::$routes)) {
			return self::$routes[$method];
		}
		throw new Exception("Unknown method '$method'");

	}

	/**
	 * @throws Exception
	 */
	public static function getRouteUrl(string $name, array $args = [], string $method = RouteMethodEnum::GET): string
	{
		$routes = self::getRoutes($method);
		foreach ($routes as $route) {
			if ($route->getOption('name') == $name) {
				return EnvironmentVariable::get(EnvironmentVariablesIdentifiers::BASE_URL) . "/" . $route->getPathFormatted($args);
			}
		}
		throw new \Exception("Route not found for '$name'");
	}
}
