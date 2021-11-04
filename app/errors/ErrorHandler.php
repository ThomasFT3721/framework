<?php

namespace App\Errors;

use Throwable;

class ErrorHandler
{
    public static function report(Throwable $th, string $content = "")
    {
        $errorTrace = $th->getTrace();
        ob_start();
        echo self::getFileContentsFormated($th->getFile());
        $main = ob_get_clean();
        include __DIR__ . "/views/template.php";
    }

    private static function getFileContentsFormated(string $path)
    {
        return str_replace("\n", "<br>", str_replace(" ", "&nbsp;", htmlspecialchars(file_get_contents($path))));
    }
}
