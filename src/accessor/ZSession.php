<?php

namespace Zaacom\accessor;

class ZSession
{
	private array $data = [];

	private function __construct() { }

	private static string $key = "framework_zaacom";

	public static function get(int|string $key): mixed
	{
		if (!array_key_exists($key, self::getData())) {
			throw new \Couchbase\IndexNotFoundException("$key not exist");
		}
		return self::getData()[$key];
	}

	public static function getOrCreate(int|string $key, mixed $elseValue = null): mixed
	{
		if (!array_key_exists($key, self::getData())) {
			self::set($key, $elseValue);
		}
		return self::getData()[$key];
	}

	public static function set(int|string $key, mixed $value)
	{
		if (!isset($_SESSION)) session_start();
		if (!array_key_exists(self::$key, $_SESSION)) {
			$_SESSION[self::$key] = new self();
		}
		$_SESSION[self::$key]->data[$key] = $value;
	}

	private static function getData(): array
	{
		if (!isset($_SESSION)) session_start();
		if (!array_key_exists(self::$key, $_SESSION)) {
			$_SESSION[self::$key] = new self();
		}
		return $_SESSION[self::$key]->data;
	}
}
