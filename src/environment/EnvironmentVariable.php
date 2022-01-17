<?php

namespace Zaacom\environment;

use Exception;
use Zaacom\filesystem\FileGenerator;
use function PHPUnit\Framework\stringStartsWith;

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

		return json_decode(self::$ENV_VARIABLE[$identifier->name]['value']);
	}

	/**
	 * @throws Exception
	 */
	public static function set(EnvironmentVariablesIdentifiers $identifiers, mixed $value)
	{
		if (empty(self::$ENV_VARIABLE)) {
			self::initEnvironmentVariables();
		}

		self::$ENV_VARIABLE[$identifiers->name]['value'] = json_encode($value);

		self::generateDotEnv(self::$ENV_VARIABLE);
	}

	/**
	 * @throws Exception
	 */
	private static function initEnvironmentVariables()
	{
		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/../../../../..");
		}
		if (!file_exists(ROOT_DIR . "/.env")) {
			$envVariables = [];
			foreach (EnvironmentVariablesIdentifiers::cases() as $value) {
				$envVariables[$value->name] = [
					'comment' => $value->comment(),
					'default_value' => json_encode($value->defaultValues()),
					'example' => json_encode($value->example()),
					'value' => json_encode($value->defaultValues()),
				];
			}
			self::generateDotEnv($envVariables);
		}
		self::recoveryEnvironmentVariablesFromFile();
	}

	private static function recoveryEnvironmentVariablesFromFile()
	{
		$fileRows = explode("\n", file_get_contents(ROOT_DIR . "/.env"));

		for ($i = 0; $i < count($fileRows); $i++) {
			$row = $fileRows[$i];
			if (!empty(trim($row))) {
				$comment = explode(' * comment:', $fileRows[$i + 1])[1];
				$default_value = explode(' * default_value:', $fileRows[$i + 2])[1];
				$example = explode(' * example:', $fileRows[$i + 3])[1];
				$key = trim(explode("=", $fileRows[$i + 5])[0]);
				$value = trim(explode("=", $fileRows[$i + 5])[1]);
				self::$ENV_VARIABLE[$key] = [
					'comment' => $comment,
					'default_value' => $default_value,
					'example' => $example,
					'value' => $value,
				];
				$i += 5;
			}
		}
	}

	/**
	 * @throws Exception
	 */
	private static function generateDotEnv(array $array)
	{
		$env = new FileGenerator(".env", content: "");
		foreach ($array as $key => $value) {
			$env->addContentLine("/**");
			$env->addContentLine(" * comment:" . $value['comment']);
			$env->addContentLine(" * default_value:" . $value['default_value']);
			$env->addContentLine(" * example:" . $value['example']);
			$env->addContentLine(" **/");
			$env->addContentLine("$key=" . $value['value']);
			$env->addBlankLine();
		}
		$env->generate();
	}

	public static function clearEnvironmentVariables() {
		self::$ENV_VARIABLE = [];
	}
}
