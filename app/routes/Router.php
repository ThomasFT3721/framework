<?php

namespace App\Routes;

use App\Views\ViewsHandler;

abstract class Router
{

    private static $routes = [];

    public static function add(string $method, string $path, array|string $action, array $options): Route
    {
        if (!array_key_exists($method, self::$routes)) {
            self::$routes[$method] = [];
        }
        $route = new Route($method, trim($path, "\t\n\r\0\x0B/ "), $action, $options);
        self::$routes[$method][] = $route;
        return $route;
    }

    private static function includeRoutes()
    {
        if (!is_dir(ROOT_DIR . "/routes")) {
            mkdir(ROOT_DIR . "/routes");
        }
        foreach (glob(ROOT_DIR . "/routes/*.php", GLOB_BRACE) as $filePath) {
            require_once __DIR__ . "/../.." . str_replace(ROOT_DIR, "", $filePath);
        }
    }

    public static function run()
    {
        self::includeRoutes();
        $route = null;
        $params = [];
        foreach (self::$routes as $method => $arr) {
            foreach ($arr as $r) {
                if (($matched = self::matchWith($r))[0]) {
                    $route = $r;
                    unset($matched[1][0]);
                    $params = array_values($matched[1]);
                }
            }
        }

        if ($route === null) {
            throw new \Exception("No route matches the url \"" . SERVER_REQUEST_URI_PARSED . "\"");
        }
        if (count($route->getAction()) != 2) {
            throw new \Exception("Invalid number of parameters for the action");
        }
        call_user_func_array([new ($route->getAction()[0]), $route->getAction()[1]], $params);
    }

    private static function matchWith(Route $route)
    {
        preg_match($route->getRegexPath(), SERVER_REQUEST_URI_PARSED, $matches);
        return [count($matches) > 0 && $matches[0] == SERVER_REQUEST_URI_PARSED, $matches];
    }

    /**
     * Get the value of routes
     * 
     * @return array
     */
    public static function getRoutes(?string $method = null)
    {
        if ($method == null) {
            return self::$routes;
        }
        if (array_key_exists($method, self::$routes)) {
            return self::$routes[$method];
        }
        throw new \Exception("Unknow method '$method'");
        
    }
}
