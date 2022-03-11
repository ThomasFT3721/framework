<?php

namespace Zaacom\models;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
interface Model
{
	public static function findById(int $id): self|null;

	public static function findByIdOrFail(int $id): self;

	public static function __create(array $params): self;

	public static function all(): array;

	public static function where(mixed ...$parameters): QuerySelect;

	public static function each(callable $callable): array;

	public function save(): bool;

	public function delete(): bool;

	public static function deleteAll(mixed ...$parameters): int;

	public static function count(): int;
}
