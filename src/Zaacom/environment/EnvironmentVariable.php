<?php

namespace Zaacom\environment;

use Exception;
use Zaacom\filesystem\FileGenerator;

abstract class EnvironmentVariable
{
    private static array $ENV_VARIABLE = [];

	/**
	 * @throws Exception
	 */
	public static function get(string $identifier)
    {
        if (empty(self::$ENV_VARIABLE)) {
            self::initEnvironmentVariables();
        }

        if (!array_key_exists($identifier, self::$ENV_VARIABLE)) {
            throw new Exception("Unknown identifier for environment variable ($identifier)");
        }

        return self::$ENV_VARIABLE[$identifier];
    }

	/**
	 * @throws Exception
	 */
	private static function initEnvironmentVariables()
    {
		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/../../../../../..");
		}
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
