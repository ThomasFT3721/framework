<?php

namespace Zaacom\routing;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
enum RouteMethodEnum
{
	case POST;
	case GET;
	case PUT;
	case DELETE;
	case PATCH;

	/**
	 * @param string $name
	 *
	 * @return RouteMethodEnum
	 */
	public static function get(string $name): RouteMethodEnum
	{
		return match ($name) {
			'POST' => RouteMethodEnum::POST,
			'GET' => RouteMethodEnum::GET,
			'PUT' => RouteMethodEnum::PUT,
			'DELETE' => RouteMethodEnum::DELETE,
			'PATCH' => RouteMethodEnum::PATCH,
			default => null,
		};
	}
}
