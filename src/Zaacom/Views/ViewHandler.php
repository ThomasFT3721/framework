<?php

namespace Zaacom\Views;



use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Zaacom\Environment\EnvironmentVariable;
use Zaacom\Environment\EnvironmentVariablesIdentifiers;
use Zaacom\Foundation\App;
use Zaacom\Routing\Router;

class ViewsHandler
{
    public static function render(string $name, array $context = [], $base_file = "/app/base.html")
    {
        $twig = new Environment(
            new FilesystemLoader(
                [
                    __DIR__.'/templates',
                    __DIR__.'../templates/error',
                ]
            ),
            [
                //'cache' => ROOT_DIR . "/app/caches/twig",
            ]
        );

        self::addDefaultFilter($twig);
        if ($base_file !== null) {
            $context['_base_file'] = $twig->load($base_file);
        }
        echo $twig->display($name, $context);
    }

    private static function addDefaultFilter(Environment &$twig)
    {
        $twig->addFilter(new TwigFilter('html_entity_decode', 'html_entity_decode'));
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
