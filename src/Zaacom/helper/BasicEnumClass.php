<?php

namespace Zaacom\helper;

use ReflectionClass;

abstract class BasicEnumClass
{
    private static $constCacheArray = [];

    public static function getValues(): array
    {
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function getKeys(): array
    {
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return array_keys(self::$constCacheArray[$calledClass]);
    }

    public static function isValidName($name, $strict = false)
    {
        $constants = self::getValues();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value, $strict = true)
    {
        $values = array_values(self::getValues());
        return in_array($value, $values, $strict);
    }
}
