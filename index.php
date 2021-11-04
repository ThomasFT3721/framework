<?php

define('FRAMEWORK_START', microtime(true));

require_once __DIR__ . "/";

use Tools\Database;



try {

    echo "123;";
    Database::test();
    

} catch (\Throwable $th) {
    var_dump($th);
}
