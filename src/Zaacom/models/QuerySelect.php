<?php

namespace Zaacom\models;


use Exception;
use InvalidArgumentException;
use PDOStatement;
use Throwable;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;

class QuerySelect implements QueryInterface
{
    private string $query = "not implemented";
    private ?string $select = null;
    private ?string $where = "1";
    private ?string $from = null;
    private ?string $orderBy = null;
    private ?string $groupBy = null;
    private ?string $having = null;
    private ?int $limit = null;
    private ?int $offset = null;

    private string $database;

    private ?string $class = null;

    private function __construct(string $database)
    {
        $this->database = $database;
    }

    public static function create(?string $database = null): self
    {
        if ($database === null) {
            $database = json_decode(EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_DATABASES))[0];
        }
        return new self($database);
    }

    public function select(string $select = "*"): self
    {
        $this->select = $select;

        return $this;
    }

    public function setWhere(string $where): self
    {
        $this->where = $where;

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
	 * @return QuerySelect
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

		$where .= match (gettype($value)) {
			'string' => "'$value'",
			'int' => "$value",
			'array' => "(" . implode(",", $value) . ")",
			default => "$value",
		};

        return $where;
    }

    public function orWhere(mixed ...$parameters): self
    {
        if (count($parameters) != 0) {
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

    public function from(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function join(string $table, string|array $left, string|array $right, string $joinType = "INNER"): self
    {
        if (gettype($left) == "array") {
            $left = implode('`.`', $left);
        }
        if (gettype($right) == "array") {
            $right = implode('`.`', $right);
        }

        if ($this->from == null) {
            $this->from = "";
        }

        $this->from .= " $joinType JOIN `$table` ON `$left`=`$right`";

        return $this;
    }

    public function orderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function groupBy(string $groupBy): self
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    public function having(string $having): self
    {
        $this->having = $having;

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
     * Set the value of class
     *
     * @return  self
     */
    public function setClass(string $class)
    {
        $this->class = $class;

        return $this;
    }

	/**
	 * @throws Throwable
	 */
	public function execute(): PDOStatement
    {
        return Database::executerRequete($this->database, $this->buildQuery());
    }

    public function get(?string $class = null): mixed
    {
        if ($class != null) {
            $this->setClass($class);
        }
        $result = $this->execute();
        if ($result->rowCount() == 0) {
            return false;
        }
        return $this->class::__create($result->fetch());
    }

    public function getAll(?string $class = null): mixed
    {
        if ($class != null) {
            $this->setClass($class);
        }
        $result = $this->execute();
        if ($result->rowCount() == 0) {
            return false;
        }
        $objects = [];
        foreach ($result->fetchAll() as $key => $value) {
            $objects[$key] = $this->class::__create($value);
        }
        return $objects;
    }

    public function each(callable $callable, ?string $class = null): array
    {
        if ($class != null) {
            $this->setClass($class);
        }

        foreach (($objects = $this->getAll($this->class)) as &$obj) {
            $callable($obj);
        }
        return $objects;
    }

    public function buildQuery(): string
    {
        if ($this->select === null) {
            $this->select = "*";
        }
        if ($this->from === null) {
            throw new InvalidArgumentException("`from` query must not be null");
        }
        $query = "SELECT " . $this->select . " FROM " . $this->from;

        if ($this->where !== null) {
            $query .= " WHERE " . $this->where;
        }
        if ($this->orderBy !== null) {
            $query .= " ORDER BY " . $this->orderBy;
        }
        if ($this->groupBy !== null) {
            $query .= " GROUP BY " . $this->groupBy;
        }
        if ($this->having !== null) {
            $query .= " HAVING " . $this->having;
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
