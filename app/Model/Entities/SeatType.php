<?php


namespace App\Model;


use MyCLabs\Enum\Enum;

class SeatType extends Enum{
    private const  FIRST = '1. kategória';
    private const SECOND = '2. kategória';
    private const THIRD = '3. kategória';
    private const FOURTH = '4. kategória';

    public static function FIRST(): SeatType
    {
        return new self(self::FIRST);
    }
    public static function SECOND(): SeatType
    {
        return new self(self::SECOND);
    }
    public static function THIRD(): SeatType
    {
        return new self(self::THIRD);
    }
    public static function FOURTH(): SeatType
    {
        return new self(self::FOURTH);
    }


}