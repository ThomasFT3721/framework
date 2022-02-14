<?php

namespace Zaacom\filesystem;


use Zaacom\models\ClassBuilder;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class EnumGenerator extends FileGenerator
{
	public string $namespace;
	public string $className;

	public function __construct(string $database, string $table, string $columnName, array $values)
	{
		$this->namespace = "Enum\\" . ClassBuilder::normalizeDatabaseName($database);
		$this->className = ClassBuilder::normalizeClassName($table) . ClassBuilder::normalizeClassName($columnName) . "Enum";

		parent::__construct($this->className . ".php", "", "");
		$this->setBasePath("/models");
		$this->addContentLine("<?php")
			->addBlankLine()
			->addContentLine("namespace " . $this->namespace . ";")
			->addBlankLine()
			->addContentLine("enum " . $this->className.": string")
			->addContentLine("{");
		foreach ($values as $key => $value) {
			$this->addContentLine("case " . strtoupper($key) . " = \"" . $value . "\";", 1);
		}
	}

	/**
	 * @return string
	 */
	public function getNamespace(): string
	{
		return $this->namespace;
	}

	/**
	 * @return string
	 */
	public function getClassName(): string
	{
		return $this->className;
	}

	public function generate(): bool
	{
		$this->addContentLine("}");
		return parent::generate();
	}
}
