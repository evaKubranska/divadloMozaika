<?php


namespace App\Model;


use Nette\Utils\DateTime;

interface IPriceRentCalculator {

    public function calculateRentPrice(DateTime $timeFrom, DateTime $timeTo, string $hallType, int $capacity):float;
}