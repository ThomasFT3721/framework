<?php

namespace Zaacom\models;

use PDOStatement;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
interface QueryInterface
{
    public static function create(?string $database): self;

    public function execute(): PDOStatement;

    public function buildQuery(): string;

    public function __toString(): string;
}
