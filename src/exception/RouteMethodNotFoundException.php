<?php

namespace Zaacom\exception;

use JetBrains\PhpStorm\Pure;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class RouteMethodNotFoundException extends \Exception
{
	public function __construct(string $name, array $existingRouteMethod, $code = 0, \Throwable $previous = null)
	{
		parent::__construct("Route method not found for name like '$name'. " . json_encode($existingRouteMethod), $code, $previous);
	}
}
