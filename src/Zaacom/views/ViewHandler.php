<?php

namespace Zaacom\views;


use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;
use Zaacom\routing\RouteMethodEnum;
use Zaacom\routing\Router;

class ViewHandler
{
	/**
	 * @throws SyntaxError
	 * @throws RuntimeError
	 * @throws LoaderError
	 */
	public static function render(string $name, array $context = [], $base_file = "base.twig"): bool
	{
		$view_folders = [
			ROOT_DIR . '/views',
		];
		self::createViewFoldersIfNotExist($view_folders);
		$view_folders[] = __DIR__ . '/templates';
		$twig = new Environment(
			new FilesystemLoader($view_folders),
			[
				//'cache' => ROOT_DIR . "/views/caches/twig",
			]
		);

		self::addDefaultFilter($twig);
		if ($base_file !== null) {
			$context['_base_file'] = $twig->load($base_file);
		}
		echo $twig->display($name, $context);

		return true;
	}

	private static function addDefaultFilter(Environment &$twig)
	{
		$twig->addFilter(new TwigFilter('html_entity_decode', 'html_entity_decode'));
		$twig->addFunction(new TwigFunction('BASE_URL', function () {
			return EnvironmentVariable::get(EnvironmentVariablesIdentifiers::BASE_URL);
		}));
		$twig->addFunction(new TwigFunction('route', function (string $name, array $args = [], string $method = RouteMethodEnum::GET) {
			$routes = Router::getRoutes($method);
			foreach ($routes as $route) {
				if ($route->getOption('name') == $name) {
					return EnvironmentVariable::get(EnvironmentVariablesIdentifiers::BASE_URL) . "/" . $route->getPathFormatted($args);
				}
			}
			throw new \Exception("Invalid route name");
		}));
	}

	private static function createViewFoldersIfNotExist(array $folderList)
	{
		foreach ($folderList as $folder) {
			if (!is_dir($folder)) {
				mkdir($folder, recursive: true);
			}
		}
	}
}
