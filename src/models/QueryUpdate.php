<?php

namespace Zaacom\models;

use Exception;
use InvalidArgumentException;
use PDOStatement;
use Throwable;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;

class QueryUpdate implements QueryInterface
{
    private string $query = "not implemented";
    private ?string $table = null;
    private ?array $values = null;

    private ?string $where = "1";
    private ?int $limit = null;
    private ?int $offset = null;

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
	 * Add where clause
	 *
	 * where function with 1 parameters:
	 *
	 * $parameters match with patern [[$key, $value], ...] or [[$key, $comparator, $value], ...]
	 *
	 * where function with 2 parameters:
	 *
	 * where($key, int $value) => " AND $key = $value"
	 *
	 * where($key, string $value) => " AND $key LIKE '$value'"
	 *
	 * where($key, array $value) => " AND $key IN (" . implode(', ', $value) . ")"
	 *
	 * where($key, null) => " AND $key IS NULL"
	 *
	 * where function with 3 parameters:
	 *
	 * where($key, $comparator, int $value) => " AND $key $comparator $value"
	 *
	 * where($key, $comparator, string $value) => " AND $key $comparator '$value'"
	 *
	 * where($key, $comparator, array $value) => " AND $key $comparator (" . implode(', ', $value) . ")"
	 *
	 * where($key, $comparator, null) => " AND $key $comparator NULL"
	 *
	 * @param mixed $parameters
	 *
	 * @return QueryUpdate
	 * @throws Exception
	 */
    public function where(mixed ...$parameters): self
    {
        if (count($parameters) != 0) {
            if (!is_array($parameters[0])) {
                $parameters = [[$parameters]];
            } else if (!is_array($parameters[0][0])) {
                $parameters = [$parameters];
            }

            $where = "";

            foreach ($parameters as $paramsList) {
                foreach ($paramsList as $params) {
                    if (count($params) == 2) {
                        $where .= $this->buildWhere('AND', ...$params);
                    } else if (count($params) == 3) {
                        $where .= $this->buildWhere('AND', $params[0], $params[2], $params[1]);
                    } else {
                        throw new Exception("Error Processing Request");
                    }
                }
            }

            $this->where .= $where;
        }

        return $this;
    }

    private function buildWhere(string $andOr, string $key, $value, ?string $comparator = null): string
    {
        $where = " $andOr $key ";

        if ($comparator === null) {
			$where .= match (gettype($value)) {
				'string' => "LIKE ",
				'int' => "= ",
				'array' => "IN ",
				default => "= ",
			};
        } else {
            $where .= "$comparator ";
        }

		$where .= ":P" . count($this->params) . "P";

		$this->params[":P" . count($this->params) . "P"] = match (gettype($value)) {
			'string' => "'$value'",
			'array' => "(" . implode(",", $value) . ")",
			default => "$value",
		};

        return $where;
    }

    public function orWhere(mixed ...$parameters): self
    {
        if (count($parameters) == 0) {
            if (!is_array($parameters[0])) {
                $parameters = [[$parameters]];
            } else if (!is_array($parameters[0][0])) {
                $parameters = [$parameters];
            }

            if ($this->where == "1") {
                $this->where = "0";
            }

            $where = " OR (1";

            foreach ($parameters as $paramsList) {
                foreach ($paramsList as $params) {
                    if (count($params) == 2) {
                        $where .= $this->buildWhere('AND', ...$params);
                    } else if (count($params) == 3) {
                        $where .= $this->buildWhere('AND', $params[0], $params[2], $params[1]);
                    } else {
                        throw new Exception("Error Processing Request");
                    }
                }
            }

            $this->where .= $where . ")";
        }

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;

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
            throw new InvalidArgumentException("`table` query must not be null");
        }
        if (count($this->values) === 0) {
            throw new InvalidArgumentException("`values` query must not be null");
        }

        $fields = [];

        foreach ($this->values as $key => $value) {
            $fields[] = "`$key` = :$key";

            $this->params[":$key"] = $value;
        }

        $query = "UPDATE `" . $this->table . "` SET " . implode(",", $fields);

        if ($this->where !== null) {
            $query .= " WHERE " . $this->where;
        }
        if ($this->limit !== null) {
            $query .= " LIMIT " . $this->limit;
        }
        if ($this->offset !== null) {
            $query .= " OFFSET " . $this->offset;
        }

        $this->query = $query;

        //echo "<br><br>$query<br><br>";
        return $query;
    }

    public function __toString(): string
    {
        return $this->query;
    }
}
