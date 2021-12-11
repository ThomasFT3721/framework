<?php

namespace Zaacom\routing;


use Zaacom\helper\BasicEnumClass;

abstract class RouteMethodEnum  extends BasicEnumClass{
    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PATCH = 'PATCH';
}
