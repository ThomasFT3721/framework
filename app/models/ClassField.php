<?php

namespace App\Models;

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
        "date" => "\\" . \App\Tools\DateTime::class,
        "datetime" => "\\" . \App\Tools\DateTime::class,
        "timestamp" => "\\" . \App\Tools\DateTime::class,
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
    ];

    public string $database;
    public string $name;
    public string $type;
    public bool $canBeNull;
    public bool $primaryKey = false;
    public ?string $comment = null;
    public ?ClassFieldLink $link = null;
    public bool $getter = true;
    public bool $setter = false;


    public function __construct(string $database, string $name, string $type, bool $canBeNull = true)
    {
        $this->database = $database;
        $this->name = $name;
        $this->type = self::SQLToPHPType($type);
        $this->canBeNull = $canBeNull;
    }

    /**
     * Get the value of database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
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
}
