<?php

namespace Zaacom\routing;

use Exception;

class Route
{

	private string $method;
	private string $path;
	private array|string $action;
	private array $options;


	public function __construct(string $method, string $path, array|string $action, array $options)
	{
		$this->method = $method;
		$this->path = trim($path, "\t\n\r\0\x0B/ ");
		$this->action = $action;
		$this->options = $options;
	}


	public static function post(array|string $path, array|string $action, array $options = []): array
	{
		return Route::add(RouteMethodEnum::POST, $path, $action, $options);
	}

	public static function get(array|string $path, array|string $action, array $options = []): array
	{
		return Route::add(RouteMethodEnum::GET, $path, $action, $options);
	}

	public static function put(array|string $path, array|string $action, array $options = []): array
	{
		return Route::add(RouteMethodEnum::PUT, $path, $action, $options);
	}

	public static function delete(array|string $path, array|string $action, array $options = []): array
	{
		return Route::add(RouteMethodEnum::DELETE, $path, $action, $options);
	}

	public static function patch(array|string $path, array|string $action, array $options = []): array
	{
		return Route::add(RouteMethodEnum::PATCH, $path, $action, $options);
	}

	private static function add(string $method, array|string $path, array|string $action, array $options = []): array
	{
		if (gettype($path) == 'string') {
			$path = [$path];
		}
		$routes = [];
		foreach ($path as $p) {
			$routes[] = Router::add($method, $p, $action, $options);
		}
		return $routes;
	}

	/**
	 * Get the value of method
	 */
	public function getMethod(): string
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
		preg_match_all("/\{([^}]*)\}/m", $path, $matches);
		if (count($matches[0]) > 0) {
			foreach ($matches[0] as $key => $match) {
				if (!array_key_exists($matches[1][$key], $args)) {
					throw new Exception("Unknow key '" . $matches[1][$key] . "' for route " . $this->getOption("name"));
				}
				$path = str_replace($match, $args[$matches[1][$key]], $path);
			}
		}
		return $path;
	}

	public function getRegexPath(): string
	{
		return "/" . preg_replace("/\\\{[^}]*\\\}/", "([^-\\/]*)", preg_quote($this->getPath(), "/")) . "/";
	}

	/**
	 * Get the value of action
	 */
	public function getAction(): array|string
	{
		return $this->action;
	}

	/**
	 * Get the value of options
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * Get the value of options
	 */
	public function getOption($key)
	{
		if (!array_key_exists($key, $this->getOptions())) {
			return null;
		}
		return $this->getOptions()[$key];
	}
}
