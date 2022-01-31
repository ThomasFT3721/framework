<?php

namespace Zaacom\routing;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;
use Zaacom\exception\IndexOutOfBounds;
use Zaacom\exception\InvalidNumberArgumentsException;
use Zaacom\exception\RouteMethodNotFoundException;
use Zaacom\exception\RouteNotFoundException;
use Zaacom\exception\UnknownRouteException;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
abstract class Router
{

	private static array $routes = [];

	public static function add(RouteMethodEnum $method, string $path, array|string $action): Route
	{
		$route = new Route($method, $path, $action);
		self::$routes[$path] = $route;
		return $route;
	}

	private static function includeRoutes()
	{
		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/../../../../..");
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
	public static function run(string $url): Route
	{
		self::includeRoutes();
		$route = null;
		$params = [];
		foreach (self::$routes as $r) {
			if (($matched = $r->matchWith($url)) !== false) {
				$route = $r;
				$params = $matched;
				break;
			}
		}

		if ($route === null) {
			throw new UnknownRouteException($url);
		}
		if (is_array($route->getAction()) && count($route->getAction()) != 2) {
			throw new InvalidNumberArgumentsException($route->getAction(), 2);
		}
		$route->runMiddlewares();
		call_user_func_array([new ($route->getAction()[0]), $route->getAction()[1]], $params);

		return $route;
	}

	public static function clearRoutes()
	{
		self::$routes = [];
	}

	/**
	 * Get the value of routes
	 *
	 * @param RouteMethodEnum|null $method
	 *
	 * @return array
	 * @throws Exception
	 */
	public static function getRoutes(?RouteMethodEnum $method = null): array
	{
		if ($method == null) {
			return self::$routes;
		}
		$routesMethods = [];
		$routes = [];
		foreach (self::$routes as $path => $route) {
			if (!in_array($route->getMethod()->name, $routesMethods)) {
				$routesMethods[] = $route->getMethod()->name;
			}
			if ($route->getMethod() == $method) {
				$routes[$path] = $route;
			}
		}
		if (count($routes) > 0) {
			return $routes;
		}
		throw new RouteMethodNotFoundException($method->name, $routesMethods);

	}

	/**
	 * @throws Exception
	 */
	public static function getRouteUrlByRouteName(string $name, array $args = [], RouteMethodEnum $method = RouteMethodEnum::GET): string
	{
		foreach (self::getRoutes($method) as $route) {
			if ($route->getName() == $name) {
				return EnvironmentVariable::get(EnvironmentVariablesIdentifiers::BASE_URL) . $route->getPathFormatted($args);
			}
		}
		foreach (self::getRoutes() as $route) {
			if ($route->getName() == $name) {
				throw new Exception("Route not found for name like '$name' and method like '" . $method->name . "' but found route for a method like '" . $route->getMethod()->name . "'");
			}
		}
		throw new RouteNotFoundException($name, $method);
	}

	/**
	 * @throws Exception
	 */
	#[NoReturn] public static function redirectTo(string $name, array $args = [], RouteMethodEnum $method = RouteMethodEnum::GET): void
	{
		header("Location: " . self::getRouteUrlByRouteName($name, $args, $method));
		exit();
	}

	public static function updateRoute(string $function, mixed $value)
	{
		if (count(self::getRoutes()) == 0) {
			throw new IndexOutOfBounds(self::getRoutes(), count(self::getRoutes()) - 1);
		}

		self::$routes[array_keys(self::getRoutes())[count(self::getRoutes()) - 1]]->{$function} = $value;
	}

}
