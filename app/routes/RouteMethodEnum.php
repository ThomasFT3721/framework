<?php

namespace App\Routes;

use App\Tools\BasicEnumClass;

abstract class RouteMethod  extends BasicEnumClass{
    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PATCH = 'PATCH';
}