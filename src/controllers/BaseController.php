<?php

namespace Zaacom\controllers;

use BadMethodCallException;
use Zaacom\views\ViewHandler;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
abstract class BaseController
{
	public function index()
	{
		$reflectionClass = new \ReflectionClass(static::class);
		echo "Controller: " . $reflectionClass->getShortName();
	}

	public function loadDefaultView(string $pageTitle, array $data = [], string $base_file = "base.twig")
	{
		$dbTrace = debug_backtrace()[1];
		ViewHandler::render((new \ReflectionClass(static::class))->getShortName() . "/" . $dbTrace['function'] . ".twig", $pageTitle, $data, $base_file);
	}

	public function loadView(string $filePath, string $pageTitle, array $data = [], string $base_file = "base.twig")
	{
		ViewHandler::render($filePath, $pageTitle, $data, $base_file);
	}

	public function __call($method, $args)
	{
		if (!in_array($method, get_class_methods($this::class))) {
			throw new BadMethodCallException("Call undefined method \"$method\" for class " . $this::class . " (args:" . json_encode($args) . ")");
		}
	}
}
