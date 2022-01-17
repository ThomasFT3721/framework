<?php

namespace Zaacom\environment;

enum EnvironmentVariablesIdentifiers
{
	case APP_NAME;
	case VERSION;

	case DB_CONNECTION;
	case DB_HOST;
	case DB_PORT;
	case DB_DATABASES;
	case DB_USERNAME;
	case DB_PASSWORD;

	case MODE_DEBUG;

	case BASE_URL;

	public function defaultValues(): string|array|bool|int
	{
		return match ($this) {
			EnvironmentVariablesIdentifiers::APP_NAME => "",
			EnvironmentVariablesIdentifiers::VERSION => "0.0.1",
			EnvironmentVariablesIdentifiers::DB_CONNECTION => "mysql",
			EnvironmentVariablesIdentifiers::DB_HOST => "127.0.0.1",
			EnvironmentVariablesIdentifiers::DB_PORT => 3306,
			EnvironmentVariablesIdentifiers::DB_DATABASES => [],
			EnvironmentVariablesIdentifiers::DB_USERNAME => "root",
			EnvironmentVariablesIdentifiers::DB_PASSWORD => "",
			EnvironmentVariablesIdentifiers::MODE_DEBUG => true,
			EnvironmentVariablesIdentifiers::BASE_URL => "",
		};
	}

	public function comment(): string
	{
		return match ($this) {
			EnvironmentVariablesIdentifiers::APP_NAME => "",
			EnvironmentVariablesIdentifiers::VERSION => "",
			EnvironmentVariablesIdentifiers::DB_CONNECTION => "",
			EnvironmentVariablesIdentifiers::DB_HOST => "",
			EnvironmentVariablesIdentifiers::DB_PORT => "",
			EnvironmentVariablesIdentifiers::DB_DATABASES => "",
			EnvironmentVariablesIdentifiers::DB_USERNAME => "",
			EnvironmentVariablesIdentifiers::DB_PASSWORD => "",
			EnvironmentVariablesIdentifiers::MODE_DEBUG => "true|false",
			EnvironmentVariablesIdentifiers::BASE_URL => "",
		};
	}

	public function example(): string|array|bool|int
	{
		return match ($this) {
			EnvironmentVariablesIdentifiers::APP_NAME => "framework",
			EnvironmentVariablesIdentifiers::VERSION => "0.0.1",
			EnvironmentVariablesIdentifiers::DB_CONNECTION => "mysql",
			EnvironmentVariablesIdentifiers::DB_HOST => "127.0.0.1",
			EnvironmentVariablesIdentifiers::DB_PORT => 3306,
			EnvironmentVariablesIdentifiers::DB_DATABASES => ["database_name"],
			EnvironmentVariablesIdentifiers::DB_USERNAME => "root",
			EnvironmentVariablesIdentifiers::DB_PASSWORD => "",
			EnvironmentVariablesIdentifiers::MODE_DEBUG => true,
			EnvironmentVariablesIdentifiers::BASE_URL => "",
		};
	}
}
