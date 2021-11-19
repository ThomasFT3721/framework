<?php

namespace Zaacom\Routing;


use Zaacom\Helper\BasicEnumClass;

abstract class RouteMethodEnum  extends BasicEnumClass{
    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PATCH = 'PATCH';
}