<?php

namespace Zaacom\Models;

use Zaacom\Filesystem\ClassGenerator;

class ClassBuilder
{

    public string $database;
    public string $table;
    public array $fields = [];

    public function __construct(string $table, string $database)
    {
        $this->table = $table;
        $this->database = $database;
    }


    public function addField(ClassField $field)
    {
        if (!array_key_exists($field->getDatabase(), $this->fields)) {
            $this->fields[$field->getDatabase()] = [];
        }
        $this->fields[$field->getDatabase()][$field->getName()] = $field;
    }

    public function generateFile()
    {
        $classGenerator = new ClassGenerator($this->table, $this->database);

        $classGenerator->addStaticFields($this->fields);

        $classGenerator->addBlankLine();

        foreach ($this->fields as $fields) {
            foreach ($fields as $field) {
                $classGenerator->addField($field);
            }
        }

        $classGenerator->createConstructor($this->fields);

        foreach ($this->fields as $fields) {
            foreach ($fields as $field) {
                if ($field->getGetter()) {
                    $classGenerator
                        ->addBlankLine();
                    $classGenerator
                        ->addGetter($field);
                }
                if ($field->getSetter()) {
                    $classGenerator
                        ->addBlankLine();
                    $classGenerator
                        ->addSetter($field);
                }
            }
        }
        return $classGenerator->addInterfaceFunction($this->fields)->generate();
    }

    public static function normalizeClassName($name)
    {
        $words = explode('_', strtolower($name));

        $name = '';
        foreach ($words as $word) {
            $name .= ucfirst(trim($word));
        }
        return $name;
    }

    public static function normalizeDatabaseName($name)
    {
        $words = explode('_', strtolower($name));

        $name = '';
        foreach ($words as $word) {
            $name .= ucfirst(trim($word));
        }
        return $name;
    }

    
    public static function normalizeMethodName($name)
    {
        $words = explode('_', $name);

        $name = '';
        foreach ($words as $word) {
            $name .= ucfirst(trim($word));
        }
        return lcfirst($name);
    }
}
