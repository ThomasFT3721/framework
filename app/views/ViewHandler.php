<?php

namespace App\Views;

use Twig\TwigFilter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewsHandler
{
    public static function render(string $name, array $context = [], $base_file = "/app/base.twig")
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
        self::addDefaultFilter($twig);
        if ($base_file !== null) {
            $context['_base_file'] = $twig->load($base_file);
        }
        echo $twig->display($name, $context);
    }

    private static function addDefaultFilter(Environment &$twig)
    {
        $twig->addFilter(new TwigFilter('html_entity_decode', 'html_entity_decode'));
    }
}
