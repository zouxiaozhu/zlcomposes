<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2018/12/7
 * Time: 下午6:49
 */

namespace Zl\Compose\Enums;

class Enums
{
    static $enums = [];

    public static function getConstants()
    {
        $class = get_called_class();
        $reflect = new \ReflectionClass($class);
        if (!array_key_exists($class, $reflect->getConstants())) {
            self::$enums[$class] = $reflect->getConstants();
        }

        return self::$enums[$class];
    }
}