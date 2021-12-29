<?php

namespace Zaacom\exception;

use JetBrains\PhpStorm\Pure;

class UnknownRouteException extends \Exception
{

	private string $url;


	#[Pure] public function __construct(string $url, $code = 0, \Throwable $previous = null)
	{
		$this->url = $url;
		parent::__construct("No route matches the url \"" . $url . "\"", $code, $previous);
	}
}
