<?php

namespace Controllers;

use App\Controllers\BaseController;
use App\Views\ViewsHandler;

class TestController extends BaseController
{

    public function test($a = "coucou")
    {
        return ViewsHandler::render("test.html", ["message" => "is a test : " . $a]);
    }
}
