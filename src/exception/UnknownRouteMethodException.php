<?php

namespace Zaacom\exception;

use JetBrains\PhpStorm\Pure;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class UnknownRouteMethodException extends \Exception
{
	#[Pure] public function __construct(string $name, $code = 0, \Throwable $previous = null)
	{
		parent::__construct("Unknown route method \"" . $name . "\"", $code, $previous);
	}
}
