<?php

namespace Zaacom\models;

use Zaacom\filesystem\ClassGenerator;
use Zaacom\filesystem\EnumGenerator;
use Zaacom\helper\DateTime;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class ClassField
{
	const TYPES_ASSOC = [
		"tinyint" => "int",
		"smallint" => "int",
		"mediumint" => "int",
		"int" => "int",
		"bigint" => "int",
		"decimal" => "float",
		"float" => "float",
		"double" => "float",
		"bit" => "int",
		"date" => "\\" . DateTime::class,
		"datetime" => "\\" . DateTime::class,
		"timestamp" => "\\" . DateTime::class,
		"time" => "string",
		"year" => "int",
		"char" => "string",
		"varchar" => "string",
		"tinytext" => "string",
		"text" => "string",
		"mediumtext" => "string",
		"longtext" => "string",
		"binary" => "string",
		"varbinary" => "string",
		"tinyblob" => "string",
		"blob" => "string",
		"mediumblob" => "string",
		"longblob" => "string",
		"json" => "string",
	];

	private string $database;
	private string $tableName;
	private string $name;
	private string $columnName;
	private string $columnType;
	private string $type;
	private bool $canBeNull;
	private bool $primaryKey = false;
	private ?string $comment = null;
	private ?ClassFieldLink $link = null;
	private bool $getter = true;
	private bool $setter = false;
	private ?EnumGenerator $enumGenerator = null;


	public function __construct(string $database, string $tableName, string $name, string $type, string $columnType, string $columnName, bool $canBeNull = true, ?string $dataType = null)
	{
		$this->database = $database;
		$this->tableName = $tableName;
		$this->name = $name;
		$this->columnName = $columnName;
		$this->columnType = $columnType;
		$this->type = self::SQLToPHPType($type);
		$this->canBeNull = $canBeNull;

		if (in_array($dataType, ["enum", "set"])) {
			$values = [];
			foreach (explode(',', str_replace("$type(", '', str_replace(')', '', str_replace("'", '', $columnType)))) as $value) {
				$values[$value] = $value;
			}
			$this->enumGenerator = new EnumGenerator($database, $tableName, $name, $values);
			$this->enumGenerator->generate();
		}
	}

	/**
	 * Get the value of database
	 */
	public function getDatabase(): string
	{
		return $this->database;
	}

	/**
	 * Get the value of database
	 */
	public function getTableName(): string
	{
		return $this->tableName;
	}

	/**
	 * Get the value of name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the value of columnName
	 */
	public function getColumnName()
	{
		return $this->columnName;
	}

	/**
	 * Get the value of type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set the value of type
	 *
	 * @return  self
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get the value of columnType
	 */
	public function getColumnType()
	{
		return $this->columnType;
	}

	/**
	 * Set the value of columnType
	 *
	 * @return  self
	 */
	public function setColumnType($columnType)
	{
		$this->columnType = $columnType;

		return $this;
	}

	/**
	 * Get the value of canBeNull
	 */
	public function getCanBeNull()
	{
		return $this->canBeNull;
	}

	public function isPrimary(bool $boolean)
	{
		$this->primaryKey = $boolean;

		return $this;
	}

	public function getPrimary(): bool
	{
		return $this->primaryKey;
	}

	/**
	 * Get the value of comment
	 */
	public function getComment()
	{
		return $this->comment;
	}

	public function addComment(string $comment)
	{
		$comment = trim($comment);
		if ($comment == '') {
			return $this;
		}
		if ($this->comment === null) {
			$this->comment = "";
		} else {
			$this->comment .= " | ";
		}

		$this->comment .= $comment;

		return $this;
	}

	/**
	 * Get the value of link
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Set the value of link
	 *
	 * @return  self
	 */
	public function setLink(ClassFieldLink $link)
	{
		$this->link = $link;

		return $this;
	}

	public static function SQLToPHPType(string $type)
	{
		if ($type == "enum" || $type == "set") {
			return "enum";
		}
		return array_key_exists($type, self::TYPES_ASSOC) ? self::TYPES_ASSOC[$type] : ClassBuilder::normalizeClassName($type);
	}

	/**
	 * Get the value of getter
	 */
	public function getGetter()
	{
		return $this->getter;
	}

	/**
	 * Remove getter
	 *
	 * @return  self
	 */
	public function removeGetter()
	{
		$this->getter = false;

		return $this;
	}

	/**
	 * Get the value of setter
	 */
	public function getSetter()
	{
		return $this->setter;
	}

	/**
	 * Add setter
	 *
	 * @return  self
	 */
	public function addSetter()
	{
		$this->setter = true;

		return $this;
	}

	/**
	 * Get the value of enumGenerator
	 */
	public function getEnumGenerator(): ?EnumGenerator
	{
		return $this->enumGenerator;
	}
}
