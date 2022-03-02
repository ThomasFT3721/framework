<?php

namespace Zaacom\routing;

use Exception;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class Route
{

	private RouteMethodEnum $method;
	private string $path;
	public array|string $action;
	public ?string $name = null;
	public ?array $middlewares = null;
	public array $allowed;
	public string $methodString;


	public function __construct(RouteMethodEnum $method, string $path, array|string $action, array $allowed)
	{
		$this->method = $method;
		$this->path = trim($path, "\t\n\r\0\x0B ");
		$this->action = $action;
		$this->methodString = $method->name;
		$this->allowed = $allowed;
	}


	public static function post(string $path, array|string $action, array $allowed): Route
	{
		return Route::add(RouteMethodEnum::POST, $path, $action, $allowed);
	}

	public static function get(string $path, array|string $action, array $allowed): Route
	{
		return Route::add(RouteMethodEnum::GET, $path, $action, $allowed);
	}

	public static function put(string $path, array|string $action, array $allowed): Route
	{
		return Route::add(RouteMethodEnum::PUT, $path, $action, $allowed);
	}

	public static function delete(string $path, array|string $action, array $allowed): Route
	{
		return Route::add(RouteMethodEnum::DELETE, $path, $action, $allowed);
	}

	public static function patch(string $path, array|string $action, array $allowed): Route
	{
		return Route::add(RouteMethodEnum::PATCH, $path, $action, $allowed);
	}

	private static function add(RouteMethodEnum $method, string $path, array|string $action, array $allowed): Route
	{
		return Router::add($method, $path, $action, $allowed);
	}

	/**
	 * Get the value of method
	 */
	public function getMethod(): RouteMethodEnum
	{
		return $this->method;
	}

	/**
	 * Get the value of path
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * Get the value of path
	 *
	 * @throws Exception
	 */
	public function getPathFormatted(array $args = []): string
	{
		$path = $this->getPath();
		preg_match_all("/\([^)]*\)/", $path, $matches);
		$matches = $matches[0];
		foreach ($matches as $key => $match) {
			$path = str_replace($match, $args[$key], $path);
		}

		return $path;
	}

	public function getRegexPath(): string
	{
		return "/^" . str_replace("/", "\/", $this->getPath()) . "$/";
	}

	public function matchWith(string $url): array|bool
	{
		preg_match_all($this->getRegexPath(), $url, $matches);
		if (count($matches[0]) == 0) {
			return false;
		}
		$parameters = [];
		unset($matches[0]);

		foreach ($matches as $match) {
			if ($match[0] != "") {
				$parameters[] = $match[0];
			}
		}
		return $parameters;
	}

	/**
	 * @return array
	 */
	public function getAllowed(): array
	{
		return $this->allowed;
	}

	/**
	 * Get the value of action
	 */
	public function getAction(): array|string
	{
		return $this->action;
	}

	public function name(string $name): Route
	{
		$this->name = $name;
		Router::updateRoute("name", $name);

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	public function middleware(string $controller, string $method): Route
	{
		if ($this->middlewares == null) {
			$this->middlewares = [];
		}
		$this->middlewares[] = [$controller, $method];
		Router::updateRoute("middlewares", $this->middlewares);

		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getMiddlewares(): ?array
	{
		return $this->middlewares;
	}

	public function runMiddlewares()
	{
		if ($this->getMiddlewares() != null) {
			foreach ($this->getMiddlewares() as $middleware) {
				call_user_func_array([new ($middleware[0]), $middleware[1]], []);
			}
		}
	}
}
