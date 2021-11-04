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
        \Test::test();
    }

    public static function testStatic()
    {
        echo "Database";
        \Test::test();
    }
}
