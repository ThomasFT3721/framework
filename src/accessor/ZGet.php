<?php

namespace Zaacom\accessor;

use Zaacom\helper\DateTime;

class ZGet
{

	private function __construct() { }

	public static function get(int|string $key): array|DateTime|string|int|float|null
	{
		if (!array_key_exists($key, $_GET)) {
			throw new \Couchbase\IndexNotFoundException("$key not exist");
		}
		return get_protected_data($key, $_GET);
	}

	public static function getOrCreate(int|string $key, mixed $elseValue = null): array|DateTime|string|int|float|null
	{
		if (!array_key_exists($key, $_GET)) {
			self::set($key, $elseValue);
		}
		return self::get($key);
	}

	public static function set(int|string $key, mixed $value)
	{
		$_GET[$key] = $value;
	}
}
