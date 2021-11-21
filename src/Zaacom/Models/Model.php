<?php

namespace Zaacom\Models;

interface Model
{
    /**
     * @param mixed ...$ids test
     */
    public static function findById(mixed ...$ids): self|false;

    public static function findByIdOrFail(mixed ...$ids): self;

    public static function all(): array;

    public static function where(mixed ...$parameters): \Zaacom\Models\QuerySelect;

    public static function each(callable $callable): array;
}
