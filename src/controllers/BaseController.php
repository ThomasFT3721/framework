<?php

namespace Zaacom\controllers;

use BadMethodCallException;

abstract class BaseController
{
	public function index()
	{
		$reflectionClass = new \ReflectionClass(static::class);
		echo "Controller: " . $reflectionClass->getShortName();
	}

	public function __call($method, $args)
	{
		if (!in_array($method, get_class_methods($this::class))) {
			throw new BadMethodCallException("Call undefined method \"$method\" for class " . $this::class . " (args:" . json_encode($args) . ")");
		}
	}
}
