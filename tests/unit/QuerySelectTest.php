<?php

use Zaacom\models\QuerySelect;

class QuerySelectTest extends \Codeception\Test\Unit
{

	public function testHaving()
	{
		$query = QuerySelect::create("aaa")->from("test")->where("cochon", "piggy")->where("mickey", "mouse");
		print_r($query->buildQuery());
		print_r($query->whereParameters);
		self::assertTrue(true);
	}
}
