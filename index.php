<?php

use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;
use Zaacom\exception\ErrorHandler;
use Zaacom\routing\Router;

define('FRAMEWORK_START', microtime(true));
define('SERVER_REQUEST_URI', urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
define('SERVER_REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
const ROOT_DIR = __DIR__;

require_once __DIR__ . "/vendor/autoload.php";

if (EnvironmentVariable::get(EnvironmentVariablesIdentifiers::MODE_DEBUG) == "true") {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

function exception_handler($th)
{
	if (is_true(EnvironmentVariable::get(EnvironmentVariablesIdentifiers::MODE_DEBUG)) === true) {
		ErrorHandler::report($th, ob_get_clean());
	} else {
		echo "Display 404";
	}
}

function is_true($val): bool
{
	return (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool)$val) ?? false;
}

function zzzPrintReadableArray(array $array, int $tabIndex = 0)
{
	$tab = "|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$carriageReturn = "<br>";
	$index = $tabIndex + 1;
	if ($tabIndex == 0) {
		echo "<div class=\"php_functions_print_readable_array\"><b>[</b>" . $carriageReturn;
		$index++;
	}

	foreach ($array as $key => $value) {
		if (is_object($value)) {
			for ($i = 1; $i < $index; $i++) {
				echo $tab;
			}
			echo "[<b>" . $key . "</b>] => <i>" . get_class($value) . "</i> <b>[</b>" . $carriageReturn;
			zzzPrintReadableArray(get_object_vars($value), $index);
			for ($i = 1; $i < $index; $i++) {
				echo $tab;
			}
			echo "<b>]</b>," . $carriageReturn;
		} elseif (is_array($value)) {
			for ($i = 1; $i < $index; $i++) {
				echo $tab;
			}
			echo "[<b>" . $key . "</b>] => <i>length(" . count($value) . ")</i> <b>[</b>";
			if (count($value) != 0) {
				echo $carriageReturn;
				zzzPrintReadableArray($value, $index);
				for ($i = 1; $i < $index; $i++) {
					echo $tab;
				}
			}
			echo "<b>]</b>," . $carriageReturn;
		} else {
			ob_start();
			var_dump($value);
			$varDump = ob_get_clean();
			for ($i = 1; $i < $index; $i++) {
				echo $tab;
			}
			if ($varDump == "NULL\n") {
				echo "[<b>" . $key . "</b>] => <b>NULL</b>," . $carriageReturn;
			} else {
				if (is_int($value)) {
					echo "[<b>" . $key . "</b>] => <i>" . preg_replace("/\)/", ")</i><b>", $varDump, 1) . $value . "</b>," . $carriageReturn;
				} else {
					echo "[<b>" . $key . "</b>] => <i>" . preg_replace("/\)/", ")</i><b>", $varDump, 1) . "</b>," . $carriageReturn;
				}
			}
		}
	}

	if ($tabIndex == 0) {
		echo "<b>]</b>" . $carriageReturn . "</div>";
	}
}

function print_readable(...$values)
{
	if (count($values) == 1 && is_array($values[0])) {
		$values = $values[0];
	}
	zzzPrintReadableArray($values);
}

function get_protected_data(string $key, ?array $from = null): array|\Zaacom\helper\DateTime|string|int|float|null
{
	if ($from === null) {
		$from = [$_POST, $_GET];
	}
	foreach ($from as $array) {
		if (array_key_exists($key, $array)) {
			$value = $array[$key];
			if (gettype($value) === "string") {
				$value = trim($value);
				if (!empty($value)) {
					try {
						$numberMatchesInt = preg_match("/^-?[0-9]*$/", $value, $matches);
						$numberMatchesFloat = preg_match("/^-?[0-9]*(.|,)?[0-9]*$/", $value, $matches);
						if ($numberMatchesInt === 1) {
							$value = intval($value);
						} elseif ($numberMatchesFloat === 1) {
							$value = floatval($value);
						} elseif (($datetime = new \Zaacom\helper\DateTime($value))->isValidDateTime()) {
							$value = $datetime;
						}
					} catch (\Throwable $th) {

					}
				}
			}
			return $value;
		}
	}
	return null;
}

set_exception_handler('exception_handler');
define('SERVER_REQUEST_URI_PARSED', trim(preg_replace('/' . preg_quote(EnvironmentVariable::get(EnvironmentVariablesIdentifiers::BASE_URL), '/') . '/', "", SERVER_REQUEST_URI), "\t\n\r\0\x0B/ "));

ob_start();
try {
	session_start();
	Router::run();
	echo ob_get_clean();
} catch (\Throwable $th) {
	throw $th;
}
