<?php

namespace Ovr\PHPReflection;

class Types
{
    /**
     * 1 << 1
     */
    const INT_TYPE = 2;

    /**
     * 1 << 2
     */
    const DOUBLE_TYPE = 4;

    /**
     * 1 << 3
     */
    const STRING_TYPE = 8;

    /**
     * self::INT_TYPE | self::DOUBLE_TYPE
     */
    const NUMBER = 6;

    /**
     * 1 << 4
     */
    const BOOLEAN_TYPE = 16;

    /**
     * 1 << 5
     */
    const ARRAY_TYPE = 32;

    /**
     * 1 << 6
     */
    const RESOURCE_TYPE = 64;

    /**
     * 1 << 7
     */
    const OBJECT_TYPE = 128;

    /**
     * 1 << 8
     */
    const CALLABLE_TYPE = 256;

    /**
     * 1 << 9
     */
    const NULL_TYPE = 512;
    
    /**
     * self::INT_TYPE | self::DOUBLE_TYPE | self::STRING_TYPE | self::BOOLEAN_TYPE | self::ARRAY_TYPE | self::RESOURCE_TYPE | self::OBJECT_TYPE | self::CALLABLE_TYPE | self::NULL_TYPE
     */
    const MIXED = 1022;

    const VOID_TYPE = -1;

    const UNKNOWN_TYPE = -2;
    
    /**
     * @param $var
     * @return int
     */
    public static function getType($var)
    {
        switch (gettype($var)) {
            case 'integer':
                return self::INT_TYPE;
                break;
            case 'double':
                return self::DOUBLE_TYPE;
                break;
        }

        return self::UNKNOWN_TYPE;
    }
}
