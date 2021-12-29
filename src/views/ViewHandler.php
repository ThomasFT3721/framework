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
	public static function render(string $name, string $page_title, array $context = [], string $base_file = "base.twig"): bool
	{
		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/../../../../../thomasft");
		}
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
		$context['_page_title'] = $page_title;
		echo $twig->display($name, $context);

		return true;
	}

	private static function addDefaultFilter(Environment &$twig)
	{
		$twig->addFilter(new TwigFilter('html_entity_decode', 'html_entity_decode'));
		$twig->addFilter(new TwigFilter('json_encode', 'json_encode'));
		$twig->addFunction(new TwigFunction('BASE_URL', function () {
			return EnvironmentVariable::get(EnvironmentVariablesIdentifiers::BASE_URL);
		}));
		$twig->addFunction(new TwigFunction('CURRENT_URL_WITH_PREFIX', function () {
			return SERVER_REQUEST_URI;
		}));
		$twig->addFunction(new TwigFunction('CURRENT_URL', function () {
			return SERVER_REQUEST_URI_PARSED;
		}));
		$twig->addFunction(new TwigFunction('route', function (string $name, array $args = [], string $method = RouteMethodEnum::GET) {
			return Router::getRouteUrl($name, $args, $method);
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
