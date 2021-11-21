<?php

namespace Controllers;

use App\Models\Database;
use App\Views\ViewsHandler;
use App\Controllers\BaseController;
use App\Models\ClassBuilder;
use App\Models\ClassField;
use App\Models\ClassFieldLink;
use App\Tools\Files\ClassGenerator;
use App\Settings\EnvironmentVariables\EnvironmentVariable;
use App\Settings\EnvironmentVariables\EnvironmentVariablesIdentifiers;

class TestController extends BaseController
{

    public function test()
    {
        function SQLToPHPType(string $type)
        {
            return [
                "tinyint" => "int",
                "smallint" => "int",
                "mediumint" => "int",
                "int" => "int",
                "bigint" => "int",
                "decimal" => "float",
                "float" => "float",
                "double" => "float",
                "bit" => "int",
                "date" => "\DateTime",
                "datetime" => "\DateTime",
                "timestamp" => "\DateTime",
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
                "enum" => "string",
                "set" => "string",
                "json" => "string",
            ][$type];
        }
        function normalizeClassName($className)
        {
            $words = explode('_', strtolower($className));

            $className = '';
            foreach ($words as $word) {
                $className .= ucfirst(trim($word));
            }
            return $className;
        }

        $databases = ["framework_other_base", EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_DATABASE)];
        $classList = [];
        foreach ($databases as $databaseName) {
            $tableNames = Database::getValues("information_schema", "SELECT `table_name` FROM `TABLES` WHERE `TABLE_SCHEMA` LIKE '$databaseName'");
            foreach ($tableNames as $tableName) {
                $columns = Database::getData("information_schema", "SELECT * FROM `COLUMNS` WHERE `TABLE_SCHEMA` LIKE '$databaseName' AND `TABLE_NAME` LIKE '$tableName' ORDER BY `ordinal_position` ASC");

                $classBuilder = new ClassBuilder($tableName, $databaseName);

                foreach ($columns as $column) {
                    $fields = [];
                    $field = new ClassField($databaseName, $column['COLUMN_NAME'], $column["DATA_TYPE"], $column["IS_NULLABLE"] != "NO");
                    $field
                        ->isPrimary($column["COLUMN_KEY"] == "PRI")
                        ->addComment($column["COLUMN_TYPE"])
                        ->addSetter();

                    if ($column["COLUMN_COMMENT"] != "") {
                        $field->addComment($column["COLUMN_COMMENT"]);
                    }

                    $link = Database::getData("information_schema", "SELECT * FROM `KEY_COLUMN_USAGE` WHERE `TABLE_SCHEMA` LIKE '$databaseName' AND `TABLE_NAME` LIKE '$tableName' AND `CONSTRAINT_NAME` NOT LIKE 'PRIMARY' AND `COLUMN_NAME` LIKE '" . $column["COLUMN_NAME"] . "'");
                    if (count($link) > 0) {
                        $link = $link[0];
                        $field->setLink(new ClassFieldLink($link['REFERENCED_TABLE_SCHEMA'], $link['REFERENCED_TABLE_NAME'], $link['REFERENCED_COLUMN_NAME']));
                    }
                    $fields[] = $field;

                    $links = Database::getData("information_schema", "SELECT * FROM `KEY_COLUMN_USAGE` WHERE `REFERENCED_TABLE_SCHEMA` LIKE '$databaseName' AND `REFERENCED_TABLE_NAME` LIKE '$tableName' AND `REFERENCED_COLUMN_NAME` LIKE '" . $column["COLUMN_NAME"] . "'");

                    if (count($links) > 0) {
                        foreach ($links as $link) {
                            $fields[] = (new ClassField($link["TABLE_SCHEMA"], lcfirst(ClassBuilder::normalizeClassName($link["TABLE_NAME"])), $link["TABLE_NAME"], true));
                        }
                    }
                    foreach ($fields as $field) {
                        $classBuilder->addField($field);
                    }
                }
                $classBuilder->generateFile();

                $classList[] = $classBuilder;
            }
        }
        
        return ViewsHandler::render("test.html", ["data" => json_decode(json_encode($classList), true)]);
    }
}
