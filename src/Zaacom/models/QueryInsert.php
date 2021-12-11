<?php

namespace Zaacom\models;

use Exception;
use PDOStatement;
use Throwable;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;

class QueryInsert implements QueryInterface
{
    private string $query = "not implemented";
    private ?string $table = null;
    private ?array $values = null;

    private array $params = [];

    private string $database;

    private function __construct(string $database)
    {
        $this->database = $database;
        $this->values = [];
    }

	/**
	 * @throws Exception
	 */
	public static function create(?string $database = null): self
    {
        if ($database === null) {
            $database = json_decode(EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_DATABASES))[0];
        }
        return new self($database);
    }

    public function setTable($table): self
    {
        $this->table = $table;

        return $this;
    }

    public function setValues($values): self
    {
        $this->values = $values;

        return $this;
    }

	/**
	 * @throws Throwable
	 */
	public function execute(): PDOStatement
    {
        return Database::executerRequete($this->database, $this->buildQuery(), $this->params);
    }

    public function buildQuery(): string
    {
        if ($this->table === null) {
            throw new \InvalidArgumentException("`table` query must not be null");
        }
        if (count($this->values) === 0) {
            throw new \InvalidArgumentException("`values` query must not be null");
        }

        foreach ($this->values as $key => $value) {
            $this->params[":$key"] = $value;
        }

        $query = "INSERT INTO `" . $this->table . "` (`" . implode("`,`", array_keys($this->values)) . "`) VALUES (" . implode(",", array_keys($this->params)) . ")";

        $this->query = $query;

        //echo "<br><br>$query<br><br>";
        return $query;
    }

    public function __toString(): string
    {
        return $this->query;
    }
}
