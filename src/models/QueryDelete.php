<?php

namespace Zaacom\models;

use Exception;
use PDOStatement;
use Throwable;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class QueryDelete implements QueryInterface
{
	private string $query = "not implemented";
	private ?string $table = null;

	private ?string $where = "1";
	public array $whereParameters = [];
	private ?int $limit = null;
	private ?int $offset = null;

	private string $database;

	private function __construct(string $database)
	{
		$this->database = $database;
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
	 * @return QueryDelete
	 * @throws Exception
	 */
	public function where(mixed ...$parameters): self
	{
		if (count($parameters) != 0) {
			if (!is_array($parameters[0])) {
				$parameters = [[$parameters]];
			} elseif (!is_array($parameters[0][0])) {
				$parameters = [$parameters];
			}

			$where = "";

			foreach ($parameters as $paramsList) {
				foreach ($paramsList as $params) {
					if (count($params) == 2) {
						$where .= $this->buildWhere('AND', ...$params);
					} elseif (count($params) == 3) {
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

		if (gettype($value) === "array") {
			$keys = [];
			foreach ($value as $val) {
				$key = ":P" . count($this->whereParameters) . "P";
				$keys[] = $key;
				$this->whereParameters[$key] = $val;
			}
			$where .= "(" . implode(",", $keys) . ")";
		} else {
			$where .= ":P" . count($this->whereParameters) . "P";
			$this->whereParameters[":P" . count($this->whereParameters) . "P"] = $value;
		}

		return $where;
	}

	/**
	 * @throws Exception
	 */
	public function orWhere(mixed ...$parameters): self
	{
		if (count($parameters) == 0) {
			if (!is_array($parameters[0])) {
				$parameters = [[$parameters]];
			} elseif (!is_array($parameters[0][0])) {
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
					} elseif (count($params) == 3) {
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
		return DataBase::executerRequete($this->database, $this->buildQuery());
	}

	public function buildQuery(): string
	{
		if ($this->table === null) {
			throw new \InvalidArgumentException("`table` query must not be null");
		}

		$query = "DELETE FROM `" . $this->table . "`";

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
