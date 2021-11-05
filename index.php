<?php

define('FRAMEWORK_START', microtime(true));
define('SERVER_REQUEST_URI', urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
define('ROOT_DIR', __DIR__);

require_once __DIR__ . "/vendor/autoload.php";

use App\Errors\ErrorHandler;
use Tools\Database;


ob_start();
try {
    session_start();

    echo "123;";
    
    (new Database())->test();

    echo ob_get_clean();
} catch (\Throwable $th) {
    ErrorHandler::report($th, ob_get_clean());
}
