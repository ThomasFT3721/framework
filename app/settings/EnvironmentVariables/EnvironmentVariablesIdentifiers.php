<?php

namespace App\Settings\EnvironmentVariables;

use App\Tools\BasicEnumClass;


class EnvironmentVariablesIdentifiers extends BasicEnumClass {
    const APP_NAME = "APP_NAME";
    const VERSION = "VERSION";

    const DB_CONNECTION = "DB_CONNECTION";
    const DB_HOST = "DB_HOST";
    const DB_PORT = "DB_PORT";
    const DB_DATABASE = "DB_DATABASE";
    const DB_USERNAME = "DB_USERNAME";
    const DB_PASSWORD = "DB_PASSWORD";

    const MODE_DEBUG = "MODE_DEBUG";

    const BASE_URL = "BASE_URL";
}
