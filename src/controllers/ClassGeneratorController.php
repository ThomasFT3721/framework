<?php

namespace Zaacom\controllers;


use Zaacom\environment\EnvironmentVariable;
use Zaacom\environment\EnvironmentVariablesIdentifiers;
use Zaacom\models\ClassBuilder;
use Zaacom\models\ClassField;
use Zaacom\models\ClassFieldLink;
use Zaacom\models\DataBase;
use Zaacom\views\ViewHandler;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class ClassGeneratorController extends BaseController
{

	public function index()
	{
		$databases = EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_DATABASES);
		$classList = [];
		foreach ($databases as $databaseName) {
			$tableNames = DataBase::getValues("information_schema", "SELECT `table_name` FROM `TABLES` WHERE `TABLE_SCHEMA` LIKE '$databaseName'");
			$classList[$databaseName] = [];
			foreach ($tableNames as $tableName) {
				$classList[$databaseName][$tableName] = (new ClassBuilder($tableName, $databaseName))->classGenerator->fileExist();
			}
		}
		return ViewHandler::render("/models/index.twig", "Generate class", ["classList" => $classList], "framework_base.twig");
	}

	public function generate()
	{
		$databases = json_decode(EnvironmentVariable::get(EnvironmentVariablesIdentifiers::DB_DATABASES));
		$classList = [];
		if (array_key_exists('class', $_POST)) {
			foreach ($databases as $databaseName) {
				if (array_key_exists($databaseName, $_POST['class'])) {
					$tableNames = DataBase::getValues("information_schema", "SELECT `table_name` FROM `TABLES` WHERE `TABLE_SCHEMA` LIKE '$databaseName'");
					foreach ($tableNames as $tableName) {
						if (array_key_exists($tableName, $_POST['class'][$databaseName])) {
							$classBuilder = new ClassBuilder($tableName, $databaseName);

							$columns = DataBase::getData("information_schema", "SELECT * FROM `COLUMNS` WHERE `TABLE_SCHEMA` LIKE '$databaseName' AND `TABLE_NAME` LIKE '$tableName' ORDER BY `ordinal_position` ASC");
							foreach ($columns as $column) {
								$fields = [];
								$field = new ClassField($databaseName, $column['COLUMN_NAME'], $column["DATA_TYPE"], $column["COLUMN_TYPE"], $column['COLUMN_NAME'], $column["IS_NULLABLE"] != "NO");
								$field
									->isPrimary($column["COLUMN_KEY"] == "PRI")
									->addComment($column["COLUMN_TYPE"])
									->addSetter();

								if ($column["COLUMN_COMMENT"] != "") {
									$field->addComment($column["COLUMN_COMMENT"]);
								}

								$link = DataBase::getData("information_schema", "SELECT * FROM `KEY_COLUMN_USAGE` WHERE `TABLE_SCHEMA` LIKE '$databaseName' AND `TABLE_NAME` LIKE '$tableName' AND `CONSTRAINT_NAME` NOT LIKE 'PRIMARY' AND `COLUMN_NAME` LIKE '" . $column["COLUMN_NAME"] . "'");
								if (count($link) > 0) {
									$link = $link[0];
									$field->setLink(new ClassFieldLink($link['REFERENCED_TABLE_SCHEMA'], $link['REFERENCED_TABLE_NAME'], $link['REFERENCED_COLUMN_NAME']));
								}
								$fields[] = $field;

								$links = DataBase::getData("information_schema", "SELECT * FROM `KEY_COLUMN_USAGE` WHERE `REFERENCED_TABLE_SCHEMA` LIKE '$databaseName' AND `REFERENCED_TABLE_NAME` LIKE '$tableName' AND `REFERENCED_COLUMN_NAME` LIKE '" . $column["COLUMN_NAME"] . "'");

								if (count($links) > 0) {
									foreach ($links as $link) {
										$fields[] = (new ClassField(
											$link["TABLE_SCHEMA"],
											lcfirst(ClassBuilder::normalizeClassName($link["TABLE_NAME"])),
											$link["TABLE_NAME"],
											true,
											$link["COLUMN_NAME"]
										))->setLink(new ClassFieldLink($databaseName, ClassBuilder::normalizeClassName($tableName), $column["COLUMN_NAME"]));
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
				}
			}
		}
		return count($classList) . " class generated";
	}
}
