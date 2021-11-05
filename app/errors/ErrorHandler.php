<?php

namespace App\Errors;

use App\Views\ViewsHandler;
use Throwable;

class ErrorHandler
{
    public static function report(Throwable $th, string $content = "")
    {
        $traces = $th->getTrace();

        foreach ($traces as &$trace) {
            $trace["fileContent"] = self::getFileContentsFormated($trace["file"]);
        }

        array_unshift($traces, [
            "file" => $th->getFile(),
            "fileContent" => self::getFileContentsFormated($th->getFile()),
            "line" => $th->getLine(),
            "message" => $th->getMessage(),
        ]);

        $request = [
            [
                "name" => "Request",
                "data" => [
                    "URL" => $_SERVER['HTTP_REFERER'],
                    "method" => $_SERVER['REQUEST_METHOD'],
                ]
            ],
            [
                "name" => "Headers",
                "data" => getallheaders()
            ],
            [
                "name" => "GET",
                "data" => $_GET
            ],
            [
                "name" => "POST",
                "data" => $_POST
            ],
            [
                "name" => "Files",
                "data" => $_FILES
            ],
            [
                "name" => "Session",
                "data" => $_SESSION
            ],
            [
                "name" => "Cookies",
                "data" => $_COOKIE
            ]
        ];

        ViewsHandler::render('/app/errors/template.html', [
            "th" => $th,
            "traces" => $traces,
            "request" => $request,
            "preview" => $content
        ], null);
    }

    private static function getFileContentsFormated(string $path): array
    {
        $res = explode("\n", file_get_contents($path));
        $classWordBefore = [
            "keyword" => [
                "class",
                "public",
                "private",
                "protected",
                "static",
                "function",
                "self",
                "\$this",
                "use",
                "namespace",
                htmlspecialchars("<?php"),
                "string",
                "int",
                "boolean",
                "array",
                "float",
            ],
            "function" => ["echo", "print"],
        ];
        $classWordAfter = [
            "utils" => ["(", ")", "{", "}", "[", "]"],
        ];

        foreach ($res as &$string) {
            $string = str_replace("<?php", htmlspecialchars("<?php"), $string);
            $string = str_replace(" ", "&nbsp;", $string);

            $wordStrings = [];
            $strDoubleQuotes = "";
            $strSingleQuotes = "";
            foreach (str_split($string) as $char) {
                if ($char == '"') {
                    if (str_contains($strDoubleQuotes, '"')) {
                        $wordStrings[] = $strDoubleQuotes . $char;
                        $strDoubleQuotes = "";
                    } else {
                        $strDoubleQuotes = $char;
                    }
                } else {
                    $strDoubleQuotes .= $char;
                }
                if ($char == "'") {
                    if (str_contains($strSingleQuotes, "'")) {
                        $wordStrings[] = $strSingleQuotes . $char;
                        $strSingleQuotes = "";
                    } else {
                        $strSingleQuotes = $char;
                    }
                } else {
                    $strSingleQuotes .= $char;
                }
            }
            foreach ($classWordBefore as $class => $arr) {
                foreach ($arr as $word) {
                    $string = str_replace($word, self::getSpan($class, $word), $string);
                }
            }
            if ($wordStrings != []) {
                foreach ($wordStrings as $word) {
                    if ($word != "") {
                        $string = str_replace($word, self::getSpan("string", $word), $string);
                    }
                }
            }
            $wordFunctions = [];
            foreach (explode("&nbsp;", $string) as $word) {
                if ($word != "") {
                    $strFunctions = "";
                    foreach (str_split($word) as $char) {
                        if ($char == "(") {
                            $wordFunctions[] = $strFunctions;
                            $strFunctions = "";
                        } elseif (in_array($char, [':', ';'])) {
                            $strFunctions = "";
                        } else {
                            $strFunctions .= $char;
                        }
                    }
                }
            }
            foreach ($wordFunctions as $word) {
                if ($word != "") {
                    $string = str_replace($word, self::getSpan("function", $word), $string);
                }
            }

            foreach ($classWordAfter as $class => $arr) {
                foreach ($arr as $word) {
                    $string = str_replace($word, self::getSpan($class, $word), $string);
                }
            }

            $string = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;", "<span class=\"tab\"></span>", $string);
        }
        return $res;
    }

    private static function getSpan($class, $word): string
    {
        return "<span class=\"$class\">$word</span>";
    }
}
