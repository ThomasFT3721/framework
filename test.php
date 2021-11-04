<?php
$nameVariable = "abc";
$$nameVariable = 123;

$ENV_VARIABLE = [];
foreach (explode("\n", file_get_contents(__DIR__ . "/.env")) as $string) {
    if (!empty(trim($string))) {
        $kv = explode("=", $string);
        $ENV_VARIABLE[trim($kv[0])] = trim($kv[1]);
    }
}
define('ENV_VARIABLE', $ENV_VARIABLE);
print_r($ENV_VARIABLE);
