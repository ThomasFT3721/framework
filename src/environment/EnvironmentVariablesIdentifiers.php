<?php

namespace Zaacom\environment;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
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

	case ADMIN_TABLE;
	case ADMIN_LOGIN;
	case ADMIN_PASSWORD;
	case ADMIN_PASSWORD_HASH_ALGO;

	case DOMAIN;
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
			EnvironmentVariablesIdentifiers::ADMIN_TABLE => "user",
			EnvironmentVariablesIdentifiers::ADMIN_LOGIN => "login",
			EnvironmentVariablesIdentifiers::ADMIN_PASSWORD => "password",
			EnvironmentVariablesIdentifiers::ADMIN_PASSWORD_HASH_ALGO => "PASSWORD_DEFAULT",
			EnvironmentVariablesIdentifiers::DOMAIN => "http://localhost",
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
			EnvironmentVariablesIdentifiers::ADMIN_TABLE => "The table where the users with administrative rights are located",
			EnvironmentVariablesIdentifiers::ADMIN_LOGIN => "The login column, can be a mail or other",
			EnvironmentVariablesIdentifiers::ADMIN_PASSWORD => "The login password",
			EnvironmentVariablesIdentifiers::ADMIN_PASSWORD_HASH_ALGO => "The password hash algorithm of the 'password_hash' function. (PASSWORD_DEFAULT, PASSWORD_BCRYPT, PASSWORD_ARGON2I, PASSWORD_ARGON2ID)",
			EnvironmentVariablesIdentifiers::DOMAIN => "",
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
			EnvironmentVariablesIdentifiers::ADMIN_TABLE => "user",
			EnvironmentVariablesIdentifiers::ADMIN_LOGIN => "login",
			EnvironmentVariablesIdentifiers::ADMIN_PASSWORD => "password",
			EnvironmentVariablesIdentifiers::ADMIN_PASSWORD_HASH_ALGO => "PASSWORD_DEFAULT",
			EnvironmentVariablesIdentifiers::DOMAIN => "http://localhost",
			EnvironmentVariablesIdentifiers::BASE_URL => "",
		};
	}
}
