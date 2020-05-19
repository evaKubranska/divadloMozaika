<?php


namespace App\Model;


use Nette\Utils\DateTime;

interface IPriceTicketCalculator {

    public  function calculateTicketPrice(DateTime $timeFrom, string $showType, string $seatType): float;
}