<?php

namespace App\Routes;

class Route
{

    private string $method;
    private string $path;
    private array|string $action;
    private array $options;


    public function __construct(string $method, string $path, array|string $action, array $options)
    {
        $this->method = $method;
        $this->path = $path;
        $this->action = $action;
        $this->options = $options;
    }


    public static function get(string $path, array|string $action, array $options = [])
    {
        return Router::add(RouteMethod::GET, $path, $action, $options);
    }

    public static function post(string $path, array|string $action, array $options = [])
    {
        return Router::add(RouteMethod::POST, $path, $action, $options);
    }

    /**
     * Get the value of method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the value of path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the value of path
     */
    public function getPathFormated(array $args = [])
    {
        $path = $this->getPath();
        preg_match_all("/\{([^}]*)\}/m", $path, $matches);
        if (count($matches[0]) > 0) {
            foreach ($matches[0] as $key => $match) {
                if (!array_key_exists($matches[1][$key], $args)) {
                    throw new \Exception("Unknow key '" . $matches[1][$key] . "' for route " . $this->getOption("name"));
                }
                $path = str_replace($match, $args[$matches[1][$key]], $path);
            }
        }
        return $path;
    }

    public function getRegexPath()
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
    public function getOptions()
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
