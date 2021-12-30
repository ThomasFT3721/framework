<?php

use Zaacom\models\QuerySelect;

class QuerySelectTest extends \Codeception\Test\Unit
{

	public function testHaving()
	{
		$query = QuerySelect::create("aaa")->from("test")->where(["t", 2],["cochon", ["piggy","mickey", "mouse"]],["d", 3]);
		print_r($query->buildQuery());
		print_r($query->whereParameters);
		self::assertTrue(true);
	}
}
