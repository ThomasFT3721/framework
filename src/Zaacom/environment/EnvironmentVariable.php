<?php

namespace Zaacom\environment;

use Exception;
use Zaacom\exception\FileNotFoundException;
use Zaacom\filesystem\FileGenerator;

abstract class EnvironmentVariable
{
    private static array $ENV_VARIABLE = [];

	/**
	 * @throws Exception
	 */
	public static function get(EnvironmentVariablesIdentifiers $identifier)
    {
        if (empty(self::$ENV_VARIABLE)) {
            self::initEnvironmentVariables();
        }

        if (!array_key_exists($identifier->value, self::$ENV_VARIABLE)) {
            throw new Exception("Unknown identifier for environment variable ($identifier->value)");
        }

        return self::$ENV_VARIABLE[$identifier->value];
    }

	/**
	 * @throws Exception
	 * @throws FileNotFoundException
	 */
	private static function initEnvironmentVariables()
    {
		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/../../../../../..");
		}
        if (!file_exists(ROOT_DIR . "/.env")) {
			throw new FileNotFoundException(".env", ROOT_DIR);
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
