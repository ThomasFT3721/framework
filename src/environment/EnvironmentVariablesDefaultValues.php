<?php

namespace Zaacom\environment;

use Zaacom\helper\BasicEnumClass;

class EnvironmentVariablesDefaultValues extends BasicEnumClass
{

	const VALUES = [
		EnvironmentVariablesIdentifiers::APP_NAME => "Framework",
		EnvironmentVariablesIdentifiers::VERSION => "0.0.1",
		EnvironmentVariablesIdentifiers::DB_CONNECTION => "mysql",
		EnvironmentVariablesIdentifiers::DB_HOST => "127.0.0.1",
		EnvironmentVariablesIdentifiers::DB_PORT => "3306",
		EnvironmentVariablesIdentifiers::DB_DATABASES => "[\"framework\"]",
		EnvironmentVariablesIdentifiers::DB_USERNAME => "root",
		EnvironmentVariablesIdentifiers::DB_PASSWORD => "",
		EnvironmentVariablesIdentifiers::MODE_DEBUG => "true",
		EnvironmentVariablesIdentifiers::BASE_URL => "",
	];
}
