<?php

use Codeception\Events;

class ExtensionTest extends \Codeception\Extension
{
	// list events to listen to
	// Codeception\Events constants used to set the event

	public static $events = [
		Events::TEST_BEFORE => 'beforeTest',
		Events::TEST_AFTER => 'afterTest',
	];

	// methods that handle events

	public function beforeTest(\Codeception\Event\TestEvent $e)
	{
		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/..");
		}
		if (file_exists(ROOT_DIR . "/.env")) {
			unlink(ROOT_DIR . "/.env");
		}

		echo "\n\n";
	}

	public function afterTest(\Codeception\Event\TestEvent $e)
	{
		\Zaacom\environment\EnvironmentVariable::clearEnvironmentVariables();
	}

}
