<?php

use Zaacom\helper\DateTime;

class DateTimeTest extends \Codeception\Test\Unit
{

	protected UnitTester $tester;

	public function test__construct()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertInstanceOf(DateTime::class, $datetime);
	}

	public function test__toString()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertEquals("2022-01-02 03:04:05", strval($datetime));
	}

	public function testFormatFrenchMax()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertEquals("03:04:05 02/01/2022", $datetime->formatFrenchMax());
	}

	public function testIsValidDateTime()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertTrue($datetime->isValidDateTime());
	}

	public function testIsNotValidDateTime()
	{
		$datetime = new DateTime("0000-01-01 00:00:00");
		$this->tester->assertFalse($datetime->isValidDateTime());
	}

	public function testFormatMin()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertEquals("2022-01-02", $datetime->formatMin());
	}

	public function testFormatMax()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertEquals("2022-01-02 03:04:05", $datetime->formatMax());
	}

	public function testFormatFrenchMin()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertEquals("02/01/2022", $datetime->formatFrenchMin());
	}

	public function testFormatIsBefore()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertTrue($datetime->isBefore("2022-01-02 03:04:06"));
	}

	public function testFormatIsAfter()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertTrue($datetime->isAfter("2022-01-02 03:04:04"));
	}

	public function testFormatEqual()
	{
		$datetime = new DateTime("2022-01-02 03:04:05");
		$this->tester->assertTrue($datetime->equal("2022-01-02 03:04:05"));
	}
}
