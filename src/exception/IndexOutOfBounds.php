<?php

namespace Zaacom\exception;

use JetBrains\PhpStorm\Pure;

class IndexOutOfBounds extends \Exception
{
	#[Pure] public function __construct(array $array, int $index, $code = 0, \Throwable $previous = null)
	{
		parent::__construct("Index out of bounds: $index, number of elements in the array: " . count($array), $code, $previous);
	}
}
