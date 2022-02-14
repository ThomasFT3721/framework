<?php

namespace Zaacom\models;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
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
	public function getDatabase(): string
	{
		return $this->database;
	}

	/**
	 * Get the value of referencedClassName
	 */
	public function getReferencedClassName(): string
	{
		return $this->referencedClassName;
	}

	/**
	 * Get the value of referencedFieldName
	 */
	public function getReferencedFieldName(): string
	{
		return $this->referencedFieldName;
	}
}
