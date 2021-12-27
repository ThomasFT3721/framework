<?php

namespace Zaacom\exception;

use JetBrains\PhpStorm\Pure;

class FileNotFoundException extends \Exception
{
	#[Pure] public function __construct(string $fileName, string $path, $code = 0, \Throwable $previous = null)
	{
		parent::__construct("File not found at $path/$fileName", $code, $previous);
	}
}
