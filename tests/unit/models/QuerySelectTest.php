<?php

use Zaacom\models\QuerySelect;

class QuerySelectTest extends \Codeception\Test\Unit
{

	protected UnitTester $tester;

	public function testFrom()
	{
		$query = QuerySelect::create("db")->from("test");
		$this->tester->assertEquals("SELECT * FROM `test`", $query->buildQuery());
	}

	public function testSelect()
	{
		$query = QuerySelect::create("db")->select("`field`")->from("test");
		$this->tester->assertEquals("SELECT `field` FROM `test`", $query->buildQuery());
	}

	public function testWhereString()
	{
		$query = QuerySelect::create("db")->from("test")->where("`field`", "aString");
		$this->tester->assertEquals("SELECT * FROM `test` WHERE 1 AND `field` LIKE :P0P", $query->buildQuery());
	}

	public function testWhereInt()
	{
		$query = QuerySelect::create("db")->from("test")->where("`field`", 123);
		$this->tester->assertEquals("SELECT * FROM `test` WHERE 1 AND `field` = :P0P", $query->buildQuery());
	}

	public function testWhereArray()
	{
		$query = QuerySelect::create("db")->from("test")->where("`field`", [1, 2, 3, 4, "5"]);
		$this->tester->assertEquals("SELECT * FROM `test` WHERE 1 AND `field` IN (:P0P,:P1P,:P2P,:P3P,:P4P)", $query->buildQuery());
	}

	public function testWhereSetComparator()
	{
		$query = QuerySelect::create("db")->from("test")->where("`field`", "=", "aString");
		$this->tester->assertEquals("SELECT * FROM `test` WHERE 1 AND `field` = :P0P", $query->buildQuery());
	}

	public function testGroupBy()
	{
		$query = QuerySelect::create("db")->from("test")->groupBy("`field`");
		$this->tester->assertEquals("SELECT * FROM `test` GROUP BY `field`", $query->buildQuery());
	}

	public function testHaving()
	{
		$query = QuerySelect::create("db")->from("test")->having("field = 123");
		$this->tester->assertEquals("SELECT * FROM `test` HAVING field = 123", $query->buildQuery());
	}

	public function testSetWhere()
	{
		$query = QuerySelect::create("db")->from("test")->setWhere("field in (0,1,2)");
		$this->tester->assertEquals("SELECT * FROM `test` WHERE field in (0,1,2)", $query->buildQuery());
	}

	public function testJoin()
	{
		$query = QuerySelect::create("db")->from("test")->join("otherTable", ["test", "field"], "ota_id");
		$this->tester->assertEquals("SELECT * FROM `test` INNER JOIN `otherTable` ON `test`.`field`=`ota_id`", $query->buildQuery());
	}

	public function testLimit()
	{
		$query = QuerySelect::create("db")->from("test")->limit(10);
		$this->tester->assertEquals("SELECT * FROM `test` LIMIT 10", $query->buildQuery());
	}

	public function testSetOrderBy()
	{
		$query = QuerySelect::create("db")->from("test")->setOrderBy("`field` ASC");
		$this->tester->assertEquals("SELECT * FROM `test` ORDER BY `field` ASC", $query->buildQuery());
	}

	public function test__toString()
	{
		$query = QuerySelect::create("db")->from("test");
		$this->tester->assertEquals("SELECT * FROM `test`", strval($query));
	}

	public function testOffset()
	{
		$query = QuerySelect::create("db")->from("test")->offset(10);
		$this->tester->assertEquals("SELECT * FROM `test` OFFSET 10", $query->buildQuery());
	}

	public function testOrWhere()
	{
		$query = QuerySelect::create("db")->from("test")->orWhere("field", "aString");
		$this->tester->assertEquals("SELECT * FROM `test` WHERE 0 OR (1 AND field LIKE :P0P)", $query->buildQuery());
	}

	public function testOrderBy()
	{
		$query = QuerySelect::create("db")->from("test")->orderBy("field", \Zaacom\models\QueryOrderEnum::DESC);
		$this->tester->assertEquals("SELECT * FROM `test` ORDER BY `field` DESC", $query->buildQuery());
	}
}
