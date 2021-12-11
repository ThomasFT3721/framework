<?php

namespace Zaacom\models;


use Exception;
use Zaacom\filesystem\ClassGenerator;

class ClassBuilder
{

    public string $database;
    public string $table;
    public ClassGenerator $classGenerator;
    public array $fields = [];

    public function __construct(string $table, string $database)
    {
        $this->table = $table;
        $this->database = $database;
        $this->classGenerator = new ClassGenerator($this->table, $this->database);
    }


    public function addField(ClassField $field)
    {
        if (!array_key_exists($field->getDatabase(), $this->fields)) {
            $this->fields[$field->getDatabase()] = [];
        }
        $this->fields[$field->getDatabase()][$field->getName()] = $field;
    }

	/**
	 * @throws Exception
	 */
	public function generateFile()
    {

        $this->classGenerator->addStaticFields($this->fields);

        $this->classGenerator->addBlankLine();

        foreach ($this->fields as $fields) {
            foreach ($fields as $field) {
                $this->classGenerator->addField($field);
            }
        }

        $this->classGenerator->createConstructor($this->fields);

        foreach ($this->fields as $fields) {
            foreach ($fields as $field) {
                if ($field->getGetter()) {
                    $this->classGenerator
                        ->addBlankLine();
                    $this->classGenerator
                        ->addIsser($field);
                    $this->classGenerator
                        ->addGetter($field);
                }
                if ($field->getSetter()) {
                    $this->classGenerator
                        ->addBlankLine();
                    $this->classGenerator
                        ->addSetter($field);
                }
            }
        }
        return $this->classGenerator->addInterfaceFunction($this->fields)->generate();
    }

    public static function normalizeClassName($name): string
	{
        $words = explode('_', strtolower($name));

        $name = '';
        foreach ($words as $word) {
            $name .= ucfirst(trim($word));
        }
        return $name;
    }

    public static function normalizeDatabaseName($name): string
	{
        $words = explode('_', strtolower($name));

        $name = '';
        foreach ($words as $word) {
            $name .= ucfirst(trim($word));
        }
        return $name;
    }

    public static function normalizeMethodName($name): string
	{
        $words = explode('_', $name);

        $name = '';
        foreach ($words as $word) {
            $name .= ucfirst(trim($word));
        }
        return lcfirst($name);
    }
}
