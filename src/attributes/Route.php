<?php

namespace Zaacom\attributes;

use Zaacom\routing\RouteMethodEnum;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Route
{
	public function __construct(
		private ?string $path = null,
		private ?string $name = null,
		private RouteMethodEnum $method = RouteMethodEnum::GET
	) { }

	/**
	 * @return string
	 */
	public function getPath(): ?string
	{
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return RouteMethodEnum
	 */
	public function getMethod(): RouteMethodEnum
	{
		return $this->method;
	}
}
