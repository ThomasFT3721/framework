<?php

namespace Zaacom\Models;

class ClassFieldLink
{

    public string $database;

    public string $referencedClassName;
    public string $referencedFieldName;

    public function __construct(string $database, string $referencedClassName, string $referencedFieldName)
    {
        $this->database = $database;
        $this->referencedClassName = ClassBuilder::normalizeClassName($referencedClassName);
        $this->referencedFieldName = $referencedFieldName;
    }

    /**
     * Get the value of database
     */ 
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Get the value of referencedClassName
     */ 
    public function getReferencedClassName()
    {
        return $this->referencedClassName;
    }

    /**
     * Get the value of referencedFieldName
     */ 
    public function getReferencedFieldName()
    {
        return $this->referencedFieldName;
    }
}
