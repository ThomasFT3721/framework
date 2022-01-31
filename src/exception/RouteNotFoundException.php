<?php

namespace Zaacom\exception;

use JetBrains\PhpStorm\Pure;
use Zaacom\routing\RouteMethodEnum;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class RouteNotFoundException extends \Exception
{
	#[Pure]
	public function __construct(string $name, RouteMethodEnum $method, $code = 0, \Throwable $previous = null)
	{
		parent::__construct("Route not found for name like '$name' and method like '" . $method->name . "'", $code, $previous);
	}
}
