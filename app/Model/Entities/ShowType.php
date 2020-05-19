<?php


namespace App\Model;


use MyCLabs\Enum\Enum;

class ShowType extends Enum {
    private const PREMIERE = 'Premiéra';
    private const REPRISION = 'Repríza';
    private const DERRIENIERE = 'Derniéra';

    public static function PREMIERE(): ShowType
    {
        return new self(self::PREMIERE);
    }
    public static function REPRISION(): ShowType
    {
        return new self(self::REPRISION);
    }
    public static function DERRIENIERE(): ShowType
    {
        return new self(self::DERRIENIERE);
    }

    public  static function getValueByKey($key) {
        $showType = self::toArray();
        return $showType[$key];
    }

    public static function getIntegerKey($value):int {
        switch ($value) {
            case self::PREMIERE:
                $intKey = 1; break;
            case self::DERRIENIERE:
                $intKey = 3; break;
            default : $intKey = 2; break;
        }
        return $intKey;
    }

}