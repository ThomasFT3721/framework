<?php

namespace Zaacom\exception;

use Zaacom\views\ViewHandler;

class ErrorHandler
{
    public static function report(\Throwable $th, string $content = "")
    {
        try {
            $traces = $th->getTrace();

            foreach ($traces as $key => &$trace) {
                if (array_key_exists("file", $trace)) {
                    $trace["fileContent"] = self::getFileContentsFormatted($trace["file"]);
                    $trace["numberRows"] = count($trace["fileContent"]);
                } else {
                    unset($traces[$key]);
                }
            }

            array_unshift($traces, [
                "file" => $th->getFile(),
                "fileContent" => self::getFileContentsFormatted($th->getFile()),
                "line" => $th->getLine(),
                "message" => $th->getMessage(),
            ]);
            $traces[0]["numberRows"] = count($traces[0]["fileContent"]);

            if (count($traces) == 2) {
                if ($traces[0]["file"] == $traces[1]["file"] && $traces[0]["line"] == $traces[1]["line"]) {
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

			if (!isset($_SESSION)) session_start();

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

            ViewHandler::render('/errors/template.twig', [
                "th" => $th,
                "traces" => $traces,
                "request" => $request,
                "preview" => $content,
                "previewRows" => explode("\n", $content)
            ]);
        } catch (\Throwable $th) {
            print_readable([
                "message" => $th->getMessage(),
                "file" => $th->getFile(),
				"line" => $th->getLine(),
				"trace" => $th->getTrace()
            ]);
        }
    }

    private static function getFileContentsFormatted(string $path): array
    {
        try {
            $res = explode("\n", file_get_contents($path));



            $inMultiligneComment = false;

            foreach ($res as &$string) {
                if ($string == "<?php" || $string == "<?php" . chr(13) || $string == "<?php" . chr(10)) {
                    $string = self::getSpan("keyword", htmlspecialchars($string));
                } else if ($inMultiligneComment && !str_contains($string, "*/")) {
                    $string = self::getSpan("comment", htmlspecialchars($string));
                } else if ($string != chr(13) && $string != chr(10)) {
                    self::doRegexWorkWith($string, $inMultiligneComment);
                }
                $string = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;", "<div class=\"tab\"></div>", $string);
            }
        } catch (\Throwable $th) {
            print_readable([
                "message" => $th->getMessage(),
                "file" => $th->getFile(),
                "line" => $th->getLine()
            ]);
        }
        return $res;
    }

    private static function getSpan($class, $word): string
    {
        return "<span class=\"$class\">$word</span>";
    }

    private static function getArrayString(array $matches, int $i, array $arr)
    {
        $res = "";
        foreach ($arr as $index) {
            $res .= $matches[$index][$i];
        }
        return $res;
    }

    private static function doRegexWorkWith(string &$string, bool &$inMultiligneComment)
    {
        $regexList = [
            0 => [
                "string",
                "/\"(.*?[^\\\\](\\\\{2})*)\"/",
                "/'(.*?[^\\\\](\\\\{2})*)'/"
            ],
            1 => [
                "string",
                "/\"\"/",
                "/''/"
            ],
            2 => [
                "comment",
                "/(\\/\\/.*)/",
            ],
            3 => [
                "keyword",
                "/__DIR__/",
                "/__FILE__/",
                "/__LINE__/",
                "/__CLASS__/",
                "/__TRAIT__/",
                "/__METHOD__/",
                "/__FUNCTION__/",
                "/__NAMESPACE__/",
            ],
            6 => [
                "class",
                "/^(namespace&nbsp;)([a-z,A-Z,0-9,_,\\\\]*)/",
                "/^(use&nbsp;)([a-z,A-Z,0-9,_,\\\\]*)/",
            ],
            4 => [
                "keyword",
                "/(public)(&nbsp;)/",
                "/(private)(&nbsp;)/",
                "/(protected)(&nbsp;)/",
                "/(static)(&nbsp;|\\()/",
                "/(const)(&nbsp;)/",
                "/(function)(&nbsp;)/",
                "/(use)(&nbsp;)/",
                "/(namespace)(&nbsp;)/",
                "/(new)(&nbsp;)/",
                "/(abstract)(&nbsp;)/",
                "/(null)(&nbsp;|;|\\||\\))/",
            ],
            5 => [
                "class",
                "/(extends&nbsp;)([a-z,A-Z,0-9,_,\\\\]*)/",
                "/(implements&nbsp;)([a-z,A-Z,0-9,_,\\\\]*)/",
            ],
            7 => [
                "class",
                "/(class&nbsp;)([A-Z][a-z,A-Z,0-9,_]*)(&nbsp;|" . chr(13) . ")/",
            ],
            23 => [
                "class",
                "/(\\()([A-Za-z1-9_]*)(&nbsp;\\\$)/",
            ],
            8 => [
                "class",
                "/(new&nbsp;)((\\\\?)?[a-z,A-Z,0-9,_]*)(\()/",
            ],
            9 => [
                "utils",
                "/(try|else)((&nbsp;)?{)/",
                "/(elseif|else&nbsp;if|foreach|if|for|catch)((&nbsp;)?\()/",
                "/(include|require_once|require)(&nbsp;|\\()/",
                "/(return)(&nbsp;|.)/",
            ],
            10 => [
                "utils",
                "/(throw)/",
            ],
            11 => [
                "keyword",
                "/(&nbsp;)(implements)(&nbsp;)/",
                "/(&nbsp;)(extends)(&nbsp;)/",
            ],
            15 => [
                "function",
                "/([a-z,A-Z,0-9,_]*)(&nbsp;\\(|\\()/",
                "/(echo)(&nbsp;)/"
            ],
            12 => [
                "keyword",
                "/(class)([^=\"])/",
            ],
            20 => [
                "keyword",
                "/([^class=\"])(string)/",
            ],
            18 => [
                "variable",
                "/(\\\$this->)(\\w*)/",
                "/(\\\$\\w*->)(\\w*)/"
            ],
            17 => [
                "variable",
                "/\\\$(?!this)\\w+/"
            ],
            13 => [
                "keyword",
                "/(int)(&nbsp;|\\||)/",
                "/(boolean)(&nbsp;|\\||)/",
                "/(array)(&nbsp;|\\||)/",
                "/(float)(&nbsp;|\\||)/",
                "/(bool)(&nbsp;|\\||)/",
                "/(callable)(&nbsp;|\\||)/",
                "/(true)(&nbsp;|\\||\\(|\\)|,|)/",
                "/(false)(&nbsp;|\\||\\(|\\)|,|)/",
                "/(self)(&nbsp;|\\||\\(|::|)/",
                "/(mixed)(&nbsp;|\\||\\(|::|)/",
                "/(\\\$this)(&nbsp;|\\||\\(|->|)/",
            ],
            14 => [
                "class",
                "/(\\\\[A-Z][a-z,A-Z,0-9,_]*)(::|\(|&nbsp;)/",
            ],
            16 => [
                "class",
                "/([A-Z][a-z,A-Z,0-9,\\\\,_]*)(::|\(|{|\n)/",
            ],
            19 => [
                "utils",
                "/\\(|\\)|\\[|\\]|\\{|\\}/"
            ],
            21 => [
                "number",
                "/[0-9]*/"
            ],
            22 => [
                "keyword",
                "/(&nbsp;)(as)(&nbsp;)/"
            ],
        ];

        $regexListRules = [
            0 => ["search" => 0, "replace" => 0, "before" => [], "after" => []],
            1 => ["search" => 0, "replace" => 0, "before" => [], "after" => []],
            2 => ["search" => 0, "replace" => 1, "before" => [], "after" => []],
            3 => ["search" => 0, "replace" => 0, "before" => [], "after" => []],
            4 => ["search" => 0, "replace" => 1, "before" => [], "after" => [2]],
            5 => ["search" => 0, "replace" => 2, "before" => [1], "after" => []],
            6 => ["search" => 0, "replace" => 2, "before" => [1], "after" => []],
            7 => ["search" => 0, "replace" => 2, "before" => [1], "after" => [3]],
            8 => ["search" => 0, "replace" => 2, "before" => [1], "after" => [4]],
            9 => ["search" => 0, "replace" => 1, "before" => [], "after" => [2]],
            10 => ["search" => 0, "replace" => 1, "before" => [], "after" => []],
            11 => ["search" => 0, "replace" => 2, "before" => [1], "after" => [3]],
            12 => ["search" => 0, "replace" => 1, "before" => [], "after" => [2]],
            13 => ["search" => 0, "replace" => 1, "before" => [], "after" => [2]],
            14 => ["search" => 0, "replace" => 1, "before" => [], "after" => [2]],
            15 => ["search" => 0, "replace" => 1, "before" => [], "after" => [2]],
            16 => ["search" => 0, "replace" => 1, "before" => [], "after" => [2]],
            17 => ["search" => 0, "replace" => 0, "before" => [], "after" => []],
            18 => ["search" => 0, "replace" => 2, "before" => [1], "after" => []],
            19 => ["search" => 0, "replace" => 0, "before" => [], "after" => []],
            20 => ["search" => 0, "replace" => 2, "before" => [1], "after" => []],
            21 => ["search" => 0, "replace" => 0, "before" => [], "after" => []],
            22 => ["search" => 0, "replace" => 2, "before" => [1], "after" => [3]],
            23 => ["search" => 0, "replace" => 2, "before" => [1], "after" => [3]],
        ];

		$string = str_replace("<", "&lt;", $string);
		$string = str_replace(">", "&gt;", $string);

		$string = str_replace(" ", "&nbsp;", $string);
		$string = str_replace('	', "&nbsp;&nbsp;&nbsp;&nbsp;", $string);

		foreach ($regexList as $regexIndex => $arr) {
            $key = $arr[0];
            $replacement = $regexListRules[$regexIndex];
            unset($arr[0]);
            foreach ($arr as $regexRowIndex => $regex) {
                $numberMatch = preg_match_all($regex, $string, $matches);
                $offset = -1;
                for ($i = 0; $i < $numberMatch; $i++) {
                    $replace = self::getSpan($key, $matches[$replacement['replace']][$i]);
                    if ($regexIndex == 6 && $regexRowIndex == 2) {
                        $m = explode("\\", $matches[$replacement['replace']][$i]);
                        $m[count($m) - 1] = self::getSpan($key, $m[count($m) - 1]);
                        $replace = join("\\", $m);
                    } else if ($regexIndex == 5) {
                        $m = explode("\\", $matches[$replacement['replace']][$i]);
                        $m[count($m) - 1] = self::getSpan($key, $m[count($m) - 1]);
                        $replace = join("\\", $m);
                    } else if ($key == "string") {
                        $replace = self::getSpan(
                            $key,
                            str_replace(
                                "&amp;nbsp;",
                                "&nbsp;",
                                ($matches[$replacement['replace']][$i])
                            ),
                        );
                    }

                    if ($replace != self::getSpan($key, "")) {
                        $replace =
                            self::getArrayString($matches, $i, $replacement['before']) .
                            $replace .
                            self::getArrayString($matches, $i, $replacement['after']);

                        if ($offset == -1) {
                            $offset = strpos($string, $matches[$replacement['search']][$i]);
                        }
                        $string = substr_replace(
                            $string,
                            $replace,
                            strpos($string, $matches[$replacement['search']][$i], $offset),
                            strlen($matches[$replacement['search']][$i])
                        );
                        $offset = strpos($string, $matches[$replacement['search']][$i], $offset + strlen("<span class=\"$key\">")) + (strlen($matches[$replacement['replace']][$i]) != strlen($matches[$replacement['search']][$i]) ? 0 : 1);
                    }
                }
            }
        }

    }
}
