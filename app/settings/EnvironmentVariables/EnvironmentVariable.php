<?php

namespace App\Settings\EnvironmentVariables;

use App\Tools\Files\CacheFileGenerator;
use App\Tools\Files\FileGenerator;

class EnvironmentVariable
{
    private static array $ENV_VARIABLE = [];

    public static function get(string $identifier)
    {
        if (empty(self::$ENV_VARIABLE)) {
            self::initEnvironmentVariables();
        }

        if (!array_key_exists($identifier, self::$ENV_VARIABLE)) {
            throw new \Exception("Unknow identifier for environment variable ($identifier)");
        }

        return self::$ENV_VARIABLE[$identifier];
    }

    private static function initEnvironmentVariables()
    {
        if (!file_exists(ROOT_DIR . "/.env")) {
            $env = new FileGenerator(".env", content: "");
            foreach (EnvironmentVariablesDefaultValues::VALUES as $key => $value) {
                $env->addContentLine("$key=$value");
            }
            $env->generate();
            self::$ENV_VARIABLE = EnvironmentVariablesDefaultValues::VALUES;
        } else {
            foreach (explode("\n", file_get_contents(ROOT_DIR . "/.env")) as $str) {
                if (!empty(trim($str))) {
                    $key = trim(explode("=", $str)[0]);
                    $value = trim(explode("=", $str)[1]);
                    self::$ENV_VARIABLE[$key] = $value;
                }
            }
        }
    }
}
