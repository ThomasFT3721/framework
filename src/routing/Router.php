<?php

namespace Zaacom\routing;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use Zaacom\attributes\Controller;
use Zaacom\authentication\AuthenticationController;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;
use Zaacom\exception\IndexOutOfBounds;
use Zaacom\exception\InvalidNumberArgumentsException;
use Zaacom\exception\RouteMethodNotFoundException;
use Zaacom\exception\RouteNotFoundException;
use Zaacom\exception\UnknownRouteException;
use Zaacom\session\USession;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
abstract class Router
{

	private static array $routes = [];
	private static ?Route $currentRoute = null;

	public static function add(RouteMethodEnum $method, string $path, array|string $action, array $allowed): Route
	{
		$route = new Route($method, $path, $action, $allowed);
		self::$routes[$path] = $route;
		return $route;
	}

	private static function includeRoutes()
	{
		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/../../../../..");
		}
		if (EnvironmentVariable::get(EnvironmentVariablesIdentifiers::MODE_DEBUG) || !file_exists(ROOT_DIR . '/cache/routes.json')) {
			foreach (scandir(ROOT_DIR . "/controllers") as $item) {
				$pathInfo = pathinfo(ROOT_DIR . "/controllers/$item");
				if ($pathInfo['extension'] == "php") {
					require_once ROOT_DIR . "/controllers/$item";
				}
			}
			foreach (scandir(__DIR__ . "/../controllers") as $item) {
				$pathInfo = pathinfo(__DIR__ . "/../controllers/$item");
				if ($pathInfo['extension'] == "php") {
					require_once __DIR__ . "/../controllers/$item";
				}
			}

			$classArray = [];
			foreach (get_declared_classes() as $class) {
				$reflectionClass = new \ReflectionClass($class);
				$c = [
					'isController' => false,
					'className' => $reflectionClass->getName(),
					'namespace' => $reflectionClass->getNamespaceName(),
					'routes' => [],
					'allowed' => [],
					'methods' => [],
				];
				foreach ($reflectionClass->getAttributes() as $attribute) {
					if ($attribute->getName() == Controller::class) {
						$c['isController'] = true;
					}
					if ($attribute->getName() == \Zaacom\attributes\Route::class) {
						$c['routes'][$attribute->newInstance()->getMethod()->name][] = [
							'path' => trim($attribute->newInstance()->getPath(), "/"),
							'name' => $attribute->newInstance()->getName() ?? $attribute->newInstance()->getMethod()->name . "." . $reflectionClass->getShortName(),
						];
					}
					if ($attribute->getName() == \Zaacom\attributes\Allow::class) {
						foreach ($attribute->newInstance()->getRoles() as $role) {
							$c['allowed'][] = new AllowedPermission($role, $attribute->newInstance()->getPermissions());
						}
					}
				}
				foreach ($reflectionClass->getMethods() as $key => $method) {
					if ($method->getDeclaringClass()->getShortName() == $reflectionClass->getShortName()) {
						if (count($method->getAttributes()) > 0) {
							$c['methods'][$method->getName()] = [
								'defaultViewExist' => file_exists(ROOT_DIR . "/views/" . $reflectionClass->getShortName() . "/" . $method->getShortName() . ".twig"),
								'attributes' => [],
								'allowed' => [],
								'routes' => [],
							];
							foreach ($method->getAttributes() as $attr) {
								switch ($attr->getName()) {
									case \Zaacom\attributes\Route::class:
										$path = trim($attr->newInstance()->getPath() ?? $method->getName(), "/");
										$methodName = $attr->newInstance()->getMethod()->name;
										if (array_key_exists($methodName, $c['routes'])) {
											foreach ($c['routes'][$methodName] as $item) {
												$c['methods'][$method->getName()]['routes'][$methodName][] = [
													'name' => (!empty($item['name']) ? $item['name'] . "." : "") . $attr->newInstance()->getName(),
													'method' => $attr->newInstance()->getMethod(),
													'path' => $path,
													'fullPath' => "/" . $item['path'] . "/" . $path,
												];
											}
										} else {
											$c['methods'][$method->getName()]['routes'][$methodName][] = [
												'name' => $attr->newInstance()->getName(),
												'method' => $attr->newInstance()->getMethod(),
												'path' => $path,
												'fullPath' => "/" . $path,
											];
										}
										break;
									case \Zaacom\attributes\Allow::class:
										foreach ($attr->newInstance()->getRoles() as $role) {
											$c['methods'][$method->getName()]['allowed'][] = new AllowedPermission($role, $attr->newInstance()->getPermissions());
										}
										break;
								}
							}
						}
					}
				}
				$classArray[] = $c;
			}
			$ca = [];
			foreach ($classArray as $c) {
				if ($c['isController']) {
					$ca[] = $c;
					$allowsClass = [];
					foreach ($c['allowed'] as $allow) {
						if (array_key_exists($allow->getRole(), $allowsClass)) {
							$permissions = array_merge($allowsClass[$allow->getRole()], $allow->getPermissions());
							unset($allowsClass[$allow->getRole()]);
							$allowsClass[$allow->getRole()] = $permissions;
						} else {
							$allowsClass[$allow->getRole()] = $allow->getPermissions();
						}
					}
					foreach ($c['methods'] as $method => $methodData) {
						$allowsRoute = $allowsClass;
						foreach ($methodData['allowed'] as $allow) {
							if (array_key_exists($allow->getRole(), $allowsRoute)) {
								$permissions = $allowsRoute[$allow->getRole()];
								if (count($allow->getPermissions()) > 0) {
									$permissions = $allow->getPermissions();
								}
								unset($allowsRoute[$allow->getRole()]);
								if (!empty($permissions)) {
									$allowsRoute[$allow->getRole()] = $permissions;
								}
							} else {
								$allowsRoute[$allow->getRole()] = $allow->getPermissions();
							}
						}
						foreach ($methodData['routes'] as $httpMethodName => $routes) {
							foreach ($routes as $route) {
								$r = Route::{strtolower($httpMethodName)}($route['fullPath'], [$c['className'], $method], $allowsRoute);
								if (!empty($route['name'])) {
									$r->name($route['name']);
								}
							}
						}
					}
				}
			}
			if (!is_dir(ROOT_DIR . '/cache')) {
				mkdir(ROOT_DIR . '/cache');
			}
			file_put_contents(ROOT_DIR . '/cache/routes.json', json_encode(self::getRoutes()));
		} else {
			$json = json_decode(file_get_contents(ROOT_DIR . '/cache/routes.json'), true);
			foreach ($json as $path => $item) {
				$r = Route::{strtolower($item['methodString'])}($path, $item['action']);
				if (!empty($item['name'])) {
					$r->name($item['name']);
				}
				if (!empty($item['middlewares'])) {
					foreach ($item['middlewares'] as $middleware) {
						$r->middleware($middleware[0], $middleware[1]);
					}
				}
			}
		}
	}

	/**
	 * @throws UnknownRouteException
	 * @throws InvalidNumberArgumentsException
	 */
	public static function run(string $url): Route
	{
		self::includeRoutes();
		$params = [];
		foreach (self::$routes as $r) {
			if (($matched = $r->matchWith($url)) !== false) {
				self::$currentRoute = $r;
				$params = $matched;
				break;
			}
		}

		if (self::$currentRoute === null) {
			throw new UnknownRouteException($url);
		}
		USession::set('framework_permissions', []);
		$canAccess = false;
		if (!empty(self::$currentRoute->getAllowed())) {
			if (AuthenticationController::user() !== null) {
				$canAccess = true;
				if (array_key_exists(AuthenticationController::role(), self::$currentRoute->getAllowed())) {
					$canAccess = true;
					USession::set('framework_permissions', self::$currentRoute->getAllowed()[AuthenticationController::role()]);
				} else {
					$canAccess = false;
				}
			} else {
				$canAccess = false;
			}
		} else {
			$canAccess = true;
		}
		if ($canAccess) {
			if (is_array(self::$currentRoute->getAction()) && count(self::$currentRoute->getAction()) != 2) {
				throw new InvalidNumberArgumentsException(self::$currentRoute->getAction(), 2);
			}
			self::$currentRoute->runMiddlewares();
			call_user_func_array([new (self::$currentRoute->getAction()[0]), self::$currentRoute->getAction()[1]], $params);
		} else {
			foreach (get_declared_classes() as $class) {
				$c = (new \ReflectionClass($class));
				if ($c->getParentClass() !== false) {
					if ($c->getParentClass()->getName() === AuthenticationController::class) {
						return self::$currentRoute;
					}
				}
			}
		}

		return self::$currentRoute;
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

	/**
	 * @throws Exception
	 */
	#[NoReturn] public static function redirectToUrl(string $url): void
	{
		header("Location: " . $url);
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
