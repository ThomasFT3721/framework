<?php

namespace unit\controller;

class TestController extends \Zaacom\controllers\BaseController
{
	public function testWithOneParameter(mixed $one)
	{
		$this->index();
		echo "\nParameters:\n";
		var_dump($one);
	}

	public function testWithTwoParameter(int $one, string $two)
	{
		$this->index();
		echo "\nParameters:\n";
		var_dump($one);
		var_dump($two);
	}
}
