<?php

namespace Zaacom\models;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
abstract class BaseModel implements Model
{
	const DATABASE = "database";
	const TABLE = "table";
	const PRIMARY_KEY = "primary_key";



	public static function findById(int $id): static|null
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->where(static::PRIMARY_KEY, $id)->get(static::class);
	}

	public static function findByIdOrFail(int $id): static
	{
		$obj = static::findById($id);
		if ($obj === false) {
			throw new \Exception('A faire l\'erreur');
		}
		return $obj;
	}

	public static function all(): array
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->getAll(static::class);
	}

	public static function each(callable $callable): array
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->each($callable, static::class);
	}

	public static function where(mixed ...$parameters): \Zaacom\models\QuerySelect
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->setClass(static::class)->where(...$parameters);
	}

	public static function orWhere(mixed ...$parameters): \Zaacom\models\QuerySelect
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->setClass(static::class)->orWhere(...$parameters);
	}

	public static function orderBy(array|string $field, QueryOrderEnum $direction = QueryOrderEnum::ASC): \Zaacom\models\QuerySelect
	{
		return \Zaacom\models\QuerySelect::create(static::DATABASE)->from(static::TABLE)->setClass(static::class)->orderBy($field, $direction);
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
