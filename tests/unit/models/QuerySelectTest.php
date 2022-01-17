<?php

namespace unit\models;

use Zaacom\models\QuerySelect;

class QuerySelectTest extends \Codeception\Test\Unit
{

	public function testHaving()
	{
		var_dump(exec("composer dump"));
		$query = QuerySelect::create("aaa")->from("test")->where(["t", 2], ["cochon", ["piggy", "mickey", "mouse"]], ["d", 3]);
		self::assertTrue(true);
	}
}
