<?php

namespace Zaacom\filesystem;


use Exception;
use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;
use Zaacom\helper\DateTime;
use Zaacom\models\BaseModel;
use Zaacom\models\ClassBuilder;
use Zaacom\models\ClassField;
use Zaacom\models\Model;
use Zaacom\models\QueryDelete;
use Zaacom\models\QueryInsert;
use Zaacom\models\QuerySelect;
use Zaacom\models\QueryUpdate;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class ClassGenerator extends FileGenerator
{

	private string $namespace;
	private string $privateOrPublic;

	/**
	 * @throws Exception
	 */
	public function __construct(string $table, string $database)
	{
		$this->privateOrPublic = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::MODE_DEBUG) == "true" ? "public" : "private";
		$this->namespace = ClassBuilder::normalizeDatabaseName($database);
		$className = ClassBuilder::normalizeClassName($table);
		parent::__construct($className . ".php", "", "");
		$this->setBasePath("/models");
		$this->addContentLine("<?php")
			->addBlankLine()
			->addContentLine("namespace Models\\" . $this->namespace . ";")
			->addBlankLine()
			->addContentLine("class $className extends \\" . BaseModel::class)
			->addContentLine("{")
			->addContentLine("const DATABASE = \"$database\";", 1)
			->addContentLine("const TABLE = \"$table\";", 1);
	}

	public function createConstructor(array $fieldList)
	{
		$this->addBlankLine();

		$assigned = $nullElements = $specialTypes = $enumTypes = [];

		foreach ($fieldList as $database => $fields) {
			foreach ($fields as $field) {
				$name = $field->getName();
				$type = $field->getType();

				if (in_array($type, array_values(ClassField::TYPES_ASSOC))) {
					$assigned[$name] = $field->getCanBeNull() || $field->getPrimary();
				} elseif ($field->getEnumGenerator() !== null) {
					$assigned[$name] = $field->getCanBeNull();
					$enumTypes[$name] = "\\" . $field->getEnumGenerator()->getNamespace() . "\\" . $field->getEnumGenerator()->getClassName();
				} else {
					$nullElements[] = $field->getName() . "List";
				}

				if (in_array($type, ["\\" . DateTime::class])) {
					$specialTypes[$name] = $type;
				}

				if ($field->getLink() != null && in_array($type, array_values(ClassField::TYPES_ASSOC))) {
					$nullElements[] = lcfirst(ClassBuilder::normalizeClassName($field->getLink()->getReferencedClassName()));
				}
			}
		}

		$this
			->addContentLine("private function __construct()", 1)
			->addContentLine("{", 1);
		foreach ($nullElements as $name) {
			$this->addContentLine("\$this->$name = null;", 2);
		}
		$this
			->addContentLine("}", 1)
			->addBlankLine()
			->addContentLine("public static function __create(array \$params): self", 1)
			->addContentLine("{", 1)
			->addContentLine("\$obj = new static();", 2);
		foreach ($assigned as $name => $canBeNull) {
			$value = "\$params[self::" . strtoupper($name) . "]";
			if (array_key_exists($name, $specialTypes)) {
				$value = "new " . $specialTypes[$name] . "($value)";
			}
			if (array_key_exists($name, $enumTypes)) {
				$value = $enumTypes[$name] . "::from($value)";
			}
			if ($canBeNull) {
				$this
					->addContentLine("\$obj->$name = array_key_exists(self::" . strtoupper($name) . ", \$params) ? $value : null;", 2);
			} else {
				$this->addContentLine("\$obj->$name = $value;", 2);
			}
		}
		$this
			->addContentLine("return \$obj;", 2)
			->addContentLine("}", 1);

		return $this;
	}

	public function addField(ClassField $field)
	{
		$nullable = $field->getCanBeNull() || $field->getPrimary() ? "?" : "";
		$name = $field->getName();
		$type = $field->getType();

		if ($field->getEnumGenerator() !== null) {
			$type = "\\" . $field->getEnumGenerator()->getNamespace() . "\\" . $field->getEnumGenerator()->getClassName();
		} elseif (!in_array($type, array_values(ClassField::TYPES_ASSOC))) {
			$type = "array";
			$name = $name . "List";
		}
		if ($field->getColumnType() === "tinyint(1)") {
			$type = "bool";
		}

		$this->addContentLine($this->privateOrPublic . " $nullable$type \$$name;", 1);

		if ($field->getLink() != null && in_array($type, array_values(ClassField::TYPES_ASSOC))) {
			$nameLink = lcfirst(ClassBuilder::normalizeClassName($field->getLink()->getReferencedClassName()));
			$typeLink = $field->getLink()->getReferencedClassName();
			if ($this->namespace != ClassBuilder::normalizeDatabaseName($field->getLink()->getDatabase())) {
				$typeLink = "\\Models\\" . ClassBuilder::normalizeDatabaseName($field->getLink()->getDatabase()) . "\\$typeLink";
			}

			$this->addContentLine($this->privateOrPublic . " ?$typeLink \$$nameLink;", 1);
		}

		return $this;
	}

	public function addStaticFields(array $fieldsArray)
	{
		$primaryKeys = [];
		foreach ($fieldsArray as $fields) {
			foreach ($fields as $field) {
				if ($field->getPrimary()) {
					$primaryKeys[] = "self::" . strtoupper($field->getName());
				}
			}
		}

		$this->addContentLine("const PRIMARY_KEYS = [" . implode(',', $primaryKeys) . "];", 1);

		foreach ($fieldsArray as $fields) {
			foreach ($fields as $field) {
				$this->addStaticField($field);
			}
		}

		return $this;
	}

	public function addStaticField(ClassField $field)
	{
		$name = $field->getName();
		$type = $field->getType();
		$comment = $field->getComment() != null ? $field->getComment() : "";

		if (in_array($type, array_values(ClassField::TYPES_ASSOC)) || in_array($type, ['enum', 'set'])) {
			if ($field->getPrimary()) {
				$this->addContentLine("//PRIMARY_KEY", 1);
			}
			$this->addContentLine("const " . strtoupper($name) . " = \"$name\";" . ($comment != "" ? " //" . $comment : ""), 1);
		}

		return $this;
	}

	public function addIsser(ClassField $field)
	{
		$name = $field->getName();
		$funcName = ClassBuilder::normalizeMethodName("is_" . $name);

		if ($field->getColumnType() === "tinyint(1)") {
			$this
				->addContentLine("public function $funcName(): bool", 1)
				->addContentLine("{", 1)
				->addContentLine("return \$this->$name == true;", 2)
				->addContentLine("}", 1)
				->addBlankLine();
		}

		return $this;
	}

	public function addGetter(ClassField $field)
	{
		$nullable = $field->getCanBeNull() || $field->getPrimary() ? "?" : "";
		$name = $field->getName();
		$type = $field->getType();
		$funcName = ClassBuilder::normalizeMethodName("get_" . $name);


		if (!in_array($type, array_values(ClassField::TYPES_ASSOC)) && !in_array($type, ['enum', 'set'])) {
			$type = "array";
			$objectClass = ucfirst($name);
			$fieldLink = $field->getLink()->getReferencedFieldName();
			$name = $name . "List";
			$funcName = $funcName . "List";
			$namespace = $namespaceLink = "";
			$staticField = strtoupper($field->getColumnName());

			if ($this->namespace != ClassBuilder::normalizeDatabaseName($field->getDatabase())) {
				$namespace = "\\Models\\" . ClassBuilder::normalizeDatabaseName($field->getDatabase()) . "\\";
			}

			if ($this->namespace != ClassBuilder::normalizeDatabaseName($field->getLink()->getDatabase())) {
				$namespaceLink = "\\Models\\" . ClassBuilder::normalizeDatabaseName($field->getLink()->getDatabase()) . "\\";
			}


			$this
				->addContentLine("public function $funcName(): $type", 1)
				->addContentLine("{", 1)
				->addContentLine("if (\$this->$name === null) {", 2)
				->addContentLine("\$this->$name = $namespace$objectClass::where($namespace$objectClass::$staticField, \$this->$fieldLink)->getAll();", 3)
				->addContentLine("}", 2)
				->addContentLine("return \$this->$name;", 2)
				->addContentLine("}", 1);
		} else {
			if ($field->getColumnType() === "tinyint(1)") {
				$type = "bool";
			} elseif (in_array($type, ['enum', 'set'])) {
				$type = "\\" . $field->getEnumGenerator()->getNamespace() . "\\" . $field->getEnumGenerator()->getClassName();
			}

			$this
				->addContentLine("public function $funcName(): $nullable$type", 1)
				->addContentLine("{", 1)
				->addContentLine("return \$this->$name;", 2)
				->addContentLine("}", 1);


			if ($field->getLink() != null) {
				$this->addBlankLine();

				$nameLink = lcfirst(ClassBuilder::normalizeClassName($field->getLink()->getReferencedClassName()));
				$typeLink = $field->getLink()->getReferencedClassName();

				if ($this->namespace != ClassBuilder::normalizeDatabaseName($field->getLink()->getDatabase())) {
					$typeLink = "\\Models\\" . ClassBuilder::normalizeDatabaseName($field->getLink()->getDatabase()) . "\\$typeLink";
				}

				$this
					->addContentLine("public function " . ClassBuilder::normalizeMethodName("get_" . $nameLink) . "(): $nullable$typeLink", 1)
					->addContentLine("{", 1)
					->addContentLine("if (\$this->$nameLink === null" . ($field->getCanBeNull() ? " && \$this->$funcName() !== null" : "") . ") {", 2)
					->addContentLine("\$this->$nameLink = $typeLink::findByIdOrFail(\$this->$funcName());", 3)
					->addContentLine("}", 2)
					->addContentLine("return \$this->$nameLink;", 2)
					->addContentLine("}", 1);
			}
		}/**/


		return $this;
	}

	public function addSetter(ClassField $field)
	{

		$nullable = $field->getCanBeNull() || $field->getPrimary() ? "?" : "";
		$name = $field->getName();
		$type = $field->getType();

		if (in_array($type, ['enum', 'set'])) {
			$type = "\\" . $field->getEnumGenerator()->getNamespace() . "\\" . $field->getEnumGenerator()->getClassName();
		} elseif (!in_array($type, array_values(ClassField::TYPES_ASSOC))) {
			$type = "array";
			$name = $name . "List";
		}

		if ($field->getColumnType() === "tinyint(1)") {
			$type = "bool";
		}

		$this
			->addContentLine("public function " . ClassBuilder::normalizeMethodName("set_" . $name) . "($nullable$type $$name): self", 1)
			->addContentLine("{", 1)
			->addContentLine("\$this->$name = $$name;", 2)
			->addBlankLine()
			->addContentLine("return \$this;", 2)
			->addContentLine("}", 1);
		return $this;
	}

	public function addInterfaceFunction(array $fieldList)
	{
		$fieldsStaticToNoStatic = $fieldsIdsStaticToNoStatic = $fieldsIdsKeyValue = [];
		foreach ($fieldList as $fields) {
			foreach ($fields as $field) {
				if (in_array($field->getType(), array_values(ClassField::TYPES_ASSOC))) {
					$fieldsStaticToNoStatic[] = "self::" . strtoupper($field->getName()) . " => " . ($field->getColumnType() == "tinyint(1)" ? "\$this->" . $field->getName() . " ? 1 : 0" : "\$this->" . $field->getName());
					if ($field->getPrimary()) {
						$fieldsIdsStaticToNoStatic[] = "self::" . strtoupper($field->getName()) . " => \$this->" . $field->getName();
						$fieldsIdsKeyValue[] = "[self::" . strtoupper($field->getName()) . ", \$this->" . $field->getName() . "]";
					}
				} elseif (in_array($field->getType(), ['enum', 'set'])) {
					$fieldsStaticToNoStatic[] = "self::" . strtoupper($field->getName()) . " => " . "\$this->" . $field->getName() . "->value";
				}
			}
		}
		$this
			->addBlankLine()
			->addBlankLine()
			->addContentLine("// Interface functions", 1)
			->addBlankLine()
			->addContentLine("public function save(): bool", 1)
			->addContentLine("{", 1)
			->addContentLine("\$dataSave = [" . implode(", ", $fieldsStaticToNoStatic) . "];", 2)
			->addContentLine("\$idsIsNull = false;", 2)
			->addContentLine("foreach (array_filter(\$dataSave, function (\$k) {", 2)
			->addContentLine("return in_array(\$k, self::PRIMARY_KEYS);", 3)
			->addContentLine("}, ARRAY_FILTER_USE_KEY) as \$value) {", 2)
			->addContentLine("if (\$value === null) {", 3)
			->addContentLine("\$idsIsNull = true;", 4)
			->addContentLine("}", 3)
			->addContentLine("}", 2)
			->addContentLine("return \$idsIsNull ? \\" . QueryInsert::class . "::create(self::DATABASE)->setTable(self::TABLE)->setValues(\$dataSave)->execute()->successExecuteRequest : \\" . QueryUpdate::class . "::create(self::DATABASE)->setTable(self::TABLE)->setValues(\$dataSave)->where([" . implode(", ", $fieldsIdsKeyValue) . "])->limit(1)->execute()->successExecuteRequest;", 2)
			->addContentLine("}", 1)
			->addBlankLine()
			->addContentLine("public function delete(): bool", 1)
			->addContentLine("{", 1)
			->addContentLine("return \\" . QueryDelete::class . "::create(self::DATABASE)->setTable(self::TABLE)->where([" . implode(", ", $fieldsIdsKeyValue) . "])->limit(1)->execute()->successExecuteRequest;", 2)
			->addContentLine("}", 1)
			->addBlankLine()
			->addContentLine("public function __toString(): string", 1)
			->addContentLine("{", 1)
			->addContentLine("return json_encode(\$this);", 2)
			->addContentLine("}", 1);

		return $this;
	}

	public function generate(): bool
	{
		$this->addContentLine("}");
		return parent::generate();
	}
}
