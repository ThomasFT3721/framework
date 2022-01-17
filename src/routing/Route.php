<?php

namespace Zaacom\routing;

use Exception;

class Route
{

	private RouteMethodEnum $method;
	private string $path;
	private array|string $action;
	public ?string $name = null;
	public ?array $middleware = null;


	public function __construct(RouteMethodEnum $method, string $path, array|string $action)
	{
		$this->method = $method;
		$this->path = trim($path, "\t\n\r\0\x0B ");
		$this->action = $action;
	}


	public static function post(string $path, array|string $action): Route
	{
		return Route::add(RouteMethodEnum::POST, $path, $action);
	}

	public static function get(string $path, array|string $action): Route
	{
		return Route::add(RouteMethodEnum::GET, $path, $action);
	}

	public static function put(string $path, array|string $action): Route
	{
		return Route::add(RouteMethodEnum::PUT, $path, $action);
	}

	public static function delete(string $path, array|string $action): Route
	{
		return Route::add(RouteMethodEnum::DELETE, $path, $action);
	}

	public static function patch(string $path, array|string $action): Route
	{
		return Route::add(RouteMethodEnum::PATCH, $path, $action);
	}

	private static function add(RouteMethodEnum $method, string $path, array|string $action): Route
	{
		return Router::add($method, $path, $action);
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

	public function middleware(array $middleware): Route
	{
		$this->middleware = $middleware;
		Router::updateRoute("middleware", $middleware);

		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getMiddleware(): ?array
	{
		return $this->middleware;
	}
}
