<?php

namespace Zaacom\models;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
abstract class BaseModel implements Model
{
	const DATABASE = "database";
	const TABLE = "table";
	const PRIMARY_KEYS = [];



	public static function findById(mixed ...$ids): static|false
	{
		if (count(static::PRIMARY_KEYS) != count($ids)) {
			throw new \InvalidArgumentException("Too few arguments to function " . __CLASS__ . "::" . __FUNCTION__ . "(), " . count($ids) . " passed and exactly " . count(static::PRIMARY_KEYS) . " expected");
		}
		$where = [];
		foreach (static::PRIMARY_KEYS as $key => $value) {
			$where[] = [$value, $ids[$key]];
		}
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->where($where)->get(__CLASS__);
	}

	public static function findByIdOrFail(mixed ...$ids): static
	{
		$obj = static::findById(...$ids);
		if ($obj === false) {
			throw new \Exception('A faire l\'erreur');
		}
		return $obj;
	}

	public static function all(): array
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->getAll(__CLASS__);
	}

	public static function each(callable $callable): array
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->each($callable, __CLASS__);
	}

	public static function where(mixed ...$parameters): \Zaacom\models\QuerySelect
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->setClass(__CLASS__)->where(...$parameters);
	}

	public static function orWhere(mixed ...$parameters): \Zaacom\models\QuerySelect
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->setClass(__CLASS__)->orWhere(...$parameters);
	}

	public static function orderBy(array|string $field, QueryOrderEnum $direction = QueryOrderEnum::ASC): \Zaacom\models\QuerySelect
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->setClass(__CLASS__)->orderBy($field, $direction);
	}

	public static function deleteAll(mixed ...$parameters): int
	{
		return \Zaacom\models\QueryDelete::create(static::DATABASE)->setTable(static::TABLE)->where(...$parameters)->execute()->rowCount();
	}

	public static function count(): int
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->count();
	}

	public function __toString(): string
	{
		return json_encode($this);
	}
}
