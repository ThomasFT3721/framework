<?php

namespace Zaacom\environment;

enum EnvironmentVariablesIdentifiers: string {
    case APP_NAME = "APP_NAME";
	case VERSION = "VERSION";

	case DB_CONNECTION = "DB_CONNECTION";
	case DB_HOST = "DB_HOST";
	case DB_PORT = "DB_PORT";
	case DB_DATABASES = "DB_DATABASES";
	case DB_USERNAME = "DB_USERNAME";
	case DB_PASSWORD = "DB_PASSWORD";

	case MODE_DEBUG = "MODE_DEBUG";

	case BASE_URL = "BASE_URL";
}
