<?php

define('FRAMEWORK_START', microtime(true));
define('SERVER_REQUEST_URI', urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
define('SERVER_REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
define('ROOT_DIR', __DIR__);

require_once __DIR__ . "/vendor/autoload.php";

use App\Routes\Route;
use App\Routes\Router;
use App\Errors\ErrorHandler;
use Controllers\TestController;
use App\Settings\EnvironmentVariables\EnvironmentVariable;
use App\Settings\EnvironmentVariables\EnvironmentVariablesIdentifiers;

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
    return (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val) ?? false;
}

set_exception_handler('exception_handler');
define('SERVER_REQUEST_URI_PARSED', trim(preg_replace('/' . preg_quote(EnvironmentVariable::get(EnvironmentVariablesIdentifiers::BASE_URL), '/') . '/', "", SERVER_REQUEST_URI), "\t\n\r\0\x0B/ "));

ob_start();
try {
    session_start();

    include __DIR__ . "/routes/web.php";


    //print_r(call_user_func([Router::class, "getRoutes"]));


    Router::run();
    echo ob_get_clean();
} catch (\Throwable $th) {
    throw $th;
}
