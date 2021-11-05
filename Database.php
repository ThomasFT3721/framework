<?php

namespace Tools;

use Test\Test;

class Database
{

    protected $db;

    public function __construct()
    {
    }

    public function test()
    {
        echo "Database";
        self::testStatic();
    }

    public static function testStatic()
    {
        echo "Database" . "deuyf";
        \Test::test();
    }
}
