<?php

namespace App\Errors;

use App\Views\ViewsHandler;

class ErrorHandler
{
    public static function report(\Throwable $th, string $content = "")
    {
        $traces = $th->getTrace();

        ini_set("highlight.comment", "#008000");
        ini_set("highlight.default", "#d4d4d4");
        ini_set("highlight.html", "#808080");
        ini_set("highlight.keyword", "#569cd6");
        ini_set("highlight.string", "#ce9178");

        foreach ($traces as $key => &$trace) {
            if (array_key_exists("file", $trace)) {
                $trace["fileContent"] = self::getFileContentsFormated($trace["file"]);
                $trace["numberRows"] = substr_count($trace["fileContent"], "<br />");
            } else {
                unset($traces[$key]);
            }
        }

        array_unshift($traces, [
            "file" => $th->getFile(),
            "fileContent" => self::getFileContentsFormated($th->getFile()),
            "line" => $th->getLine(),
            "message" => $th->getMessage(),
        ]);
        $traces[0]["numberRows"] = substr_count($traces[0]["fileContent"], "<br />");

        if (count($traces) == 2) {
            if ($traces[0]["file"] == $traces[1]["file"] && $traces[1]["line"] == $traces[1]["line"]) {
                unset($traces[1]);
            }
        }


        for ($i = 0; $i < count($traces); $i++) {
            if ($i + 1 < count($traces)) {
                if (array_key_exists("class", $traces[$i + 1])) {
                    $traces[$i]["class"] = $traces[$i + 1]["class"];
                }
                if (array_key_exists("type", $traces[$i + 1])) {
                    $traces[$i]["type"] = $traces[$i + 1]["type"];
                }
                if (array_key_exists("function", $traces[$i + 1])) {
                    $traces[$i]["function"] = $traces[$i + 1]["function"];
                }
            } else {
                unset($traces[$i]["class"]);
                unset($traces[$i]["type"]);
                unset($traces[$i]["function"]);
            }
        }


        $request = [
            [
                "name" => "Request",
                "data" => [
                    "URL" => $_SERVER['REQUEST_URI'],
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
                "name" => "SERVER",
                "data" => $_SERVER
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
            "preview" => $content,
            "previewRows" => explode("\n", $content)
        ], null);
    }

    private static function getFileContentsFormated(string $path): string
    {
        $res = highlight_file($path, true);

        /*$regexBefore = [
                [
                    "string",
                    "/(\"[^\"]*\")(?!=|\"|<\\/span>)/",
                    "/('[^']*')(?!=|\"|<\\/span>)/"
                ],
                [
                    "keyword",
                    "/__DIR__(?!=|\"|<\\/span>)/",
                    "/__FILE__(?!=|\"|<\\/span>)/",
                    "/__LINE__(?!=|\"|<\\/span>)/",
                    "/__CLASS__(?!=|\"|<\\/span>)/",
                    "/__TRAIT__(?!=|\"|<\\/span>)/",
                    "/__METHOD__(?!=|\"|<\\/span>)/",
                    "/__FUNCTION__(?!=|\"|<\\/span>)/",
                    "/__NAMESPACE__(?!=|\"|<\\/span>)/",
                ],
                [
                    "class",
                    "/namespace&nbsp;([a-z,A-Z,0-9,_,\\\\]*)(?!=|\"|<\\/span>)/",
                    "/use&nbsp;([a-z,A-Z,0-9,_,\\\\]*)(?!=|\"|<\\/span>)/",
                    "/class&nbsp;([A-Z][a-z,A-Z,0-9,_]*)((&nbsp;)|" . chr(13) . ")/",
                    "/new&nbsp;((\\\\)?[a-z,A-Z,0-9,_]*)((&nbsp;)?)(\()(?!=|\"|<\\/span>)/",
                ],
                [
                    "utils",
                    "/(try|else)((&nbsp;)?{)(?!=|\"|<\\/span>)/",
                    "/(elseif|foreach|if|for|catch)((&nbsp;)?\()(?!=|\"|<\\/span>)/",
                    "/(include|require_once|require)((&nbsp;)?(\()?)(?!=|\"|<\\/span>)/",
                    "/throw(?!=|\"|<\\/span>)/",
                    "/(return)(&nbsp;(\\\$|[a-zA-Z])|;)(?!=|\"|<\\/span>)/",
                ],
                [
                    "function",
                    "/(([a-z,A-Z,0-9,_])*)(&nbsp;\(|\()(?!=|\"|<\\/span>)/",
                    "/echo(?!=|\"|<\\/span>)/"
                ],
                [
                    "class",
                    "/([A-Z]([a-z,A-Z,0-9,_])*)(::|\(|{|\n)(?!=|\"|<\\/span>)/",
                ],
                [
                    "variable",
                    "/\\$([a-z,A-Z,0-9,_]*)(?!=|\"|<\\/span>)/"
                ],
                [
                    "keyword",
                    "/class(?!=|\"|<\\/span>)/",
                    "/public(?!=|\"|<\\/span>)/",
                    "/private(?!=|\"|<\\/span>)/",
                    "/protected(?!=|\"|<\\/span>)/",
                    "/static(?!=|\"|<\\/span>)/",
                    "/function(?!=|\"|<\\/span>)/",
                    "/self(?!=|\"|<\\/span>)/",
                    "/\$this(?!=|\"|<\\/span>)/",
                    "/use(?!=|\"|<\\/span>)/",
                    "/namespace(?!=|\"|<\\/span>)/",
                    "/string(?!=|\"|<\\/span>)/",
                    "/int(?!=|\"|<\\/span>)/",
                    "/boolean(?!=|\"|<\\/span>)/",
                    "/array(?!=|\"|<\\/span>)/",
                    "/float(?!=|\"|<\\/span>)/",
                    "/true(?!=|\"|<\\/span>)/",
                    "/false(?!=|\"|<\\/span>)/",
                    "/new(?!=|\"|<\\/span>)/",
                ],
                ];

                foreach ($res as &$string) {

                    if ($string != chr(13)) {
                        $string = str_replace(" ", "&nbsp;", $string);

                        foreach ($regexBefore as $arr) {
                            $key = $arr[0];
                            unset($arr[0]);
                            foreach ($arr as $regex) {
                                preg_match_all($regex, $string, $matches);
                                if (count($matches[0]) > 0) {
                                    if (count($matches) <= 3) {
                                        for ($i = 0; $i < count($matches[0]); $i++) {

                                            print_r(json_encode($matches) . "<br>");
                                            if (str_contains($string, "use") && str_contains($regex, "use&nbsp;")) {
                                                $m = explode("\\", $matches[1][$i]);
                                                $m[count($m) - 1] = self::getSpan($key, $m[count($m) - 1]);
                                                $string = str_replace($matches[1][$i], "use&nbsp;" . join("\\", $m), $string);
                                            } else if ($key == "string") {
                                                $string = str_replace($matches[1][$i], self::getSpan($key, str_replace("&amp;nbsp;", "&nbsp;", htmlspecialchars($matches[0][$i])),), $string);
                                            } else {
                                                $string = str_replace(count($matches) > 1 ? $matches[1][$i] : $matches[0][$i], self::getSpan($key, $matches[0][$i]), $string);
                                            }
                                            echo "<br>";
                                        }
                                    } else {
                                        for ($i = 0; $i < count($matches[0]); $i++) {
                                            $string = str_replace(
                                                $matches[0][$i],
                                                str_replace($matches[1][$i], self::getSpan($key, $matches[1][$i]), $matches[0][$i]),
                                                $string
                                            );
                                        }
                                    }
                                }
                            }
                        }
                        $string = str_replace("<?php", self::getSpan("keyword", htmlspecialchars("<?php")), $string);
                    }
                    $string = str_replace("&nbsp;", "&nbsp;&nbsp;", $string);
                    $string = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;", "<span class=\"tab\"></span>", $string);
                    $string = highlight_string("<?php " . $string, true);
                }*/
        return $res;
    }

    private static function getSpan($class, $word): string
    {
        return "<span class=\"$class\">$word</span>";
    }
}
