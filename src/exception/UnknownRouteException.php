<?php

namespace Zaacom\exception;

use JetBrains\PhpStorm\Pure;

class UnknownRouteException extends \Exception
{
	#[Pure] public function __construct(string $url, $code = 0, \Throwable $previous = null)
	{
		parent::__construct("No route matches the url \"" . $url . "\"", $code, $previous);
	}
}
