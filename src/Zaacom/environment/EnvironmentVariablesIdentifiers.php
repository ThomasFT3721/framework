<?php

namespace Zaacom\environment;

use Zaacom\helper\BasicEnumClass;

abstract class EnvironmentVariablesIdentifiers extends BasicEnumClass {
    const APP_NAME = "APP_NAME";
    const VERSION = "VERSION";

    const DB_CONNECTION = "DB_CONNECTION";
    const DB_HOST = "DB_HOST";
    const DB_PORT = "DB_PORT";
    const DB_DATABASES = "DB_DATABASES";
    const DB_USERNAME = "DB_USERNAME";
    const DB_PASSWORD = "DB_PASSWORD";

    const MODE_DEBUG = "MODE_DEBUG";

    const BASE_URL = "BASE_URL";
}
