<?php


namespace App\Model;


use MyCLabs\Enum\Enum;

class HallType extends Enum {
    private const STUDIO = 'Štúdio';
    private const OPERA = 'Opera';
    private const CINOHRA = 'Činohra';

    public static function STUDIO(): String {
        return self::STUDIO;
    }

    public static function OPERA(): String {
        return self::OPERA;
    }

    public static function CINOHRA(): String {
        return self::CINOHRA;
    }

    /**
     * @param string $value
     * @return int
     */
    public static function getIntegerKey(String $value):int {
        $hallType = self::toArray();
        $key = $hallType[$value];
        switch ($key) {
            case self::STUDIO:
                $intKey = 1; break;
            case self::OPERA:
                $intKey = 2; break;
            case self::CINOHRA:
                $intKey = 3; break;
            default : $intKey = 0; break;
        }
        return $intKey;
    }

    public  static function getValueByKey($key) {
       $hallType = self::toArray();
        return $hallType[$key];
    }

}