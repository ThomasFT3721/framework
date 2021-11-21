<?php

namespace App\Views;

use App\Routes\RouteMethod;
use Twig\TwigFilter;
use Twig\Environment;
use App\Routes\Router;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use App\Settings\EnvironmentVariables\EnvironmentVariable;
use App\Settings\EnvironmentVariables\EnvironmentVariablesIdentifiers;

class ViewsHandler
{
    public static function render(string $name, array $context = [], $base_file = "/app/base.html")
    {
        $twig = new Environment(
            new FilesystemLoader(
                [
                    ROOT_DIR . "/views",
                    ROOT_DIR . "/views/app",
                    ROOT_DIR . "/views/app/errors"
                ]
            ),
            [
                //'cache' => ROOT_DIR . "/app/caches/twig",
            ]
        );

        self::addDefaultTools($twig);
        if ($base_file !== null) {
            $context['_base_file'] = $twig->load($base_file);
        }
        echo $twig->display($name, $context);
    }

    private static function addDefaultTools(Environment &$twig)
    {
        $twig->addFilter(new TwigFilter('html_entity_decode', 'html_entity_decode'));
        $twig->addFilter(new TwigFilter('json_encode', "json_encode"));


        $twig->addFunction(new TwigFunction('BASE_URL', function () {
            return EnvironmentVariable::get(EnvironmentVariablesIdentifiers::BASE_URL);
        }));
        $twig->addFunction(new TwigFunction('route', function (string $name, array $args = [], string $method = RouteMethod::GET) {
            $routes = Router::getRoutes($method);
            foreach ($routes as $route) {
                if ($route->getOption('name') == $name) {
                    return $route->getPathFormated($args);
                }
            }
            throw new \Exception("Invalid route name");
        }));
    }
}
