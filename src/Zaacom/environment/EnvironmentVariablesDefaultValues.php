<?php

namespace Zaacom\environment;


abstract class EnvironmentVariablesDefaultValues {
    const VALUES = [
        EnvironmentVariablesIdentifiers::APP_NAME => "framework",
        EnvironmentVariablesIdentifiers::VERSION => "0.0.1",

        EnvironmentVariablesIdentifiers::DB_CONNECTION => "mysql",
        EnvironmentVariablesIdentifiers::DB_HOST => "127.0.0.1",
        EnvironmentVariablesIdentifiers::DB_PORT => "3306",
        EnvironmentVariablesIdentifiers::DB_DATABASES => "[\"framework\"]",
        EnvironmentVariablesIdentifiers::DB_USERNAME => "root",
        EnvironmentVariablesIdentifiers::DB_PASSWORD => "",

		EnvironmentVariablesIdentifiers::MODE_DEBUG => "true",

		EnvironmentVariablesIdentifiers::BASE_URL => "/framework",
    ];
}