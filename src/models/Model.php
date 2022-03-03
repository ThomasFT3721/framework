<?php

namespace Zaacom\models;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
interface Model
{
	/**
	 * @param mixed ...$ids test
	 */
	public static function findById(mixed ...$ids): self|false;

	public static function findByIdOrFail(mixed ...$ids): self;

	public static function __create(array $params): self;

	public static function all(): array;

	public static function where(mixed ...$parameters): QuerySelect;

	public static function each(callable $callable): array;

	public function save(): bool;

	public function delete(): bool;

	public static function deleteAll(mixed ...$parameters): int;

	public static function count(): int;
}
