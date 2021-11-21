<?php

namespace App\Tools\Files;

use App\Models\ClassBuilder;
use App\Models\ClassField;
use App\Models\ClassMethod;

class ClassGenerator extends FileGenerator
{

    private string $namespace;

    public function __construct(string $table, string $database)
    {
        $this->namespace = ClassBuilder::normalizeDatabaseName($database);
        $className = ClassBuilder::normalizeClassName($table);
        parent::__construct($className . ".php", "", "");
        $this->setBasePath("/models");
        $this->addContentLine("<?php")
            ->addBlankLine()
            ->addContentLine("namespace Models\\" . $this->namespace . ";")
            ->addBlankLine()
            ->addContentLine("class $className implements \App\Models\Model")
            ->addContentLine("{")
            ->addContentLine("const DATABASE = \"$database\";", 1)
            ->addContentLine("const TABLE = \"$table\";", 1);
    }

    public function createConstructor(array $fieldList)
    {
        $this->addBlankLine();

        $assigned = $nullElements = $specialTypes = [];

        foreach ($fieldList as $database => $fields) {
            foreach ($fields as $field) {
                $name = $field->getName();
                $type = $field->getType();

                if (in_array($type, array_values(ClassField::TYPES_ASSOC))) {
                    $assigned[$name] = $field->getCanBeNull();
                } else {
                    $nullElements[] = $field->getName() . "List";
                }

                if (in_array($type, ["\\" . \App\Tools\DateTime::class])) {
                    $specialTypes[$name] = $type;
                }

                if ($field->getLink() != null) {
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
        $nullable = $field->getCanBeNull() ? "?" : "";
        $name = $field->getName();
        $type = $field->getType();

        if (!in_array($type, array_values(ClassField::TYPES_ASSOC))) {
            $type = "array";
            $name = $name . "List";
        }

        $this->addContentLine("private $nullable$type \$$name;", 1);

        if ($field->getLink() != null) {
            $nameLink = lcfirst(ClassBuilder::normalizeClassName($field->getLink()->getReferencedClassName()));
            $typeLink = $field->getLink()->getReferencedClassName();
            if ($this->namespace != ClassBuilder::normalizeDatabaseName($field->getLink()->getDatabase())) {
                $typeLink = "\\Models\\" . ClassBuilder::normalizeDatabaseName($field->getLink()->getDatabase()) . "\\$typeLink";
            }

            $this->addContentLine("private ?$typeLink \$$nameLink;", 1);
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

        if (in_array($type, array_values(ClassField::TYPES_ASSOC))) {
            if ($field->getPrimary()) {
                $this->addContentLine("//PRIMARY_KEY", 1);
            }
            $this->addContentLine("const " . strtoupper($name) . " = \"$name\";" . ($comment != "" ? " //" . $comment : ""), 1);
        }

        return $this;
    }

    public function addGetter(ClassField $field)
    {
        $nullable = $field->getCanBeNull() ? "?" : "";
        $name = $field->getName();
        $type = $field->getType();
        $funcName = ClassBuilder::normalizeMethodName("get_" . $name);

        if (!in_array($type, array_values(ClassField::TYPES_ASSOC))) {
            $type = "array";
            $objectClass = ucfirst($name);
            $name = $name . "List";
            $funcName = $funcName . "List";
            $namespace = "";

            if ($this->namespace != ClassBuilder::normalizeDatabaseName($field->getDatabase())) {
                $namespace = "\\Models\\" . ClassBuilder::normalizeDatabaseName($field->getDatabase()) . "\\";
            }

            $this
                ->addContentLine("public function $funcName(): $nullable$type", 1)
                ->addContentLine("{", 1)
                ->addContentLine("if (\$this->$name === null) {", 2)
                ->addContentLine("\$this->$name = $namespace$objectClass::all();", 3)
                ->addContentLine("}", 2)
                ->addContentLine("return \$this->$name;", 2)
                ->addContentLine("}", 1);
        } else {
            $this
                ->addContentLine("public function $funcName(): $nullable$type", 1)
                ->addContentLine("{", 1)
                ->addContentLine("return \$this->$name;", 2)
                ->addContentLine("}", 1);
        }

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

        return $this;
    }

    public function addSetter(ClassField $field)
    {

        $nullable = $field->getCanBeNull() ? "?" : "";
        $name = $field->getName();
        $type = $field->getType();

        if (!in_array($type, array_values(ClassField::TYPES_ASSOC))) {
            $type = "array";
            $name = $name . "List";
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
        $this
            ->addBlankLine()
            ->addBlankLine()
            ->addContentLine("// Interface functions", 1)
            ->addBlankLine()
            ->addContentLine("public static function findById(mixed ...\$ids): self|false", 1)
            ->addContentLine("{", 1)
            ->addContentLine("if (count(self::PRIMARY_KEYS) != count(\$ids)) {", 2)
            ->addContentLine("throw new \InvalidArgumentException(\"Too few arguments to function \" . __CLASS__ . \"::\" . __FUNCTION__ . \"(), \" . count(\$ids) . \" passed and exactly \" . count(self::PRIMARY_KEYS) . \" expected\");", 3)
            ->addContentLine("}", 2)
            ->addContentLine("\$where = [];", 2)
            ->addContentLine("foreach (self::PRIMARY_KEYS as \$key => \$value) {", 2)
            ->addContentLine("\$where[] = [\$value, \$ids[\$key]];", 3)
            ->addContentLine("}", 2)
            ->addContentLine("return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->where(\$where)->get(__CLASS__);", 2)
            ->addContentLine("}", 1)
            ->addBlankLine()
            ->addContentLine("public static function findByIdOrFail(mixed ...\$ids): self", 1)
            ->addContentLine("{", 1)
            ->addContentLine("\$obj = self::findById(...\$ids);", 2)
            ->addContentLine("if (\$obj === false) {", 2)
            ->addContentLine("throw new \Exception('A faire l\'erreur');", 3)
            ->addContentLine("}", 2)
            ->addContentLine("return \$obj;", 2)
            ->addContentLine("}", 1)
            ->addBlankLine()
            ->addContentLine("public static function all(): array", 1)
            ->addContentLine("{", 1)
            ->addContentLine("return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->getAll(__CLASS__);", 2)
            ->addContentLine("}", 1)
            ->addBlankLine()
            ->addContentLine("public static function each(callable \$callable): array", 1)
            ->addContentLine("{", 1)
            ->addContentLine("return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->each(\$callable, __CLASS__);", 2)
            ->addContentLine("}", 1)
            ->addBlankLine()
            ->addContentLine("public static function where(mixed ...\$parameters): \App\Models\QuerySelect", 1)
            ->addContentLine("{", 1)
            ->addContentLine("return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->setClass(__CLASS__)->where(...\$parameters);", 2)
            ->addContentLine("}", 1)
            ->addBlankLine()
            ->addContentLine("public static function orWhere(mixed ...\$parameters): \App\Models\QuerySelect", 1)
            ->addContentLine("{", 1)
            ->addContentLine("return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->setClass(__CLASS__)->orWhere(...\$parameters);", 2)
            ->addContentLine("}", 1);

        return $this;
    }

    public function generate()
    {
        $this->addContentLine("}");
        return parent::generate();
    }
}

/*


    public static function findById(mixed ...$ids): self
    {
        $where = "1";
        foreach (self::PRIMARY_KEYS as $key => $value) {
            $where .= " AND `$value` =" . $ids[$key];
        }
        $pdoStatement = QuerySelect::create(self::DATABASE)->from(self::TABLE)->where($where)->execute();
        print_r($pdoStatement->fetchObject(__CLASS__));
        throw new \Exception('erreur');
    }

    public static function findByIdOrFail(mixed ...$ids): self
    {
        self::findById(...$ids);
        throw new \Exception('erreur');
    }
    */