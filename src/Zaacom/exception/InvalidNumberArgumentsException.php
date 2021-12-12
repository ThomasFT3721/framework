<?php

namespace Zaacom\exception;

use JetBrains\PhpStorm\Pure;

class InvalidNumberArgumentsException extends \Exception
{

	private array $arguments;
	private int $expectedNumber;


	#[Pure] public function __construct(array $arguments, int $expectedNumber, $code = 0, \Throwable $previous = null)
	{
		$this->arguments = $arguments;
		$this->expectedNumber = $expectedNumber;
		parent::__construct("Invalid number of arguments, expected $expectedNumber given " . count($arguments) . " (" . json_encode($arguments) . ")", $code, $previous);
	}
}
