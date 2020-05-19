<?php


namespace App\Model;


use Nette\Utils\DateTime;

class PriceTicketCalculator implements IPriceTicketCalculator {

    private  const PRICE_BASE = 10.00;


    public function calculateTicketPrice(DateTime $timeFrom, string $showType, string $seatType): float {
         $price = $this->calcultePriceByTime($timeFrom);
         $price = $this->calculatePriceByShowType($showType, $price);
         $price = $this->calculatePriceBySeatType($seatType, $price);
         return $price;
    }

    private  function calcultePriceByTime(string $timeFrom): float {
        $price =0;
        $time = date('H:i:s',strtotime($timeFrom));
        if($time <= date('H:i:s',strtotime('14:00:00'))){
            $price= self::PRICE_BASE* 0.6;
        } elseif ($time >= date('H:i:s',strtotime('17:00:00'))) {
            $price= self::PRICE_BASE*1.2;
        }
        return $price;
    }

    private function calculatePriceByShowType (string $showType, float $price): float {
        switch ($showType){
            case ShowType::PREMIERE():
                $price *= 1.4;
                break;
            case ShowType::DERRIENIERE():
                $price *= 1.2;
                break;
        }
        return $price;
    }

    private function calculatePriceBySeatType (string $seatType, float $price): float {
        switch ($seatType){
            case SeatType::FIRST():
                $price *= 1.2;
                break;
            case SeatType::SECOND():
                $price *= 1.1;
                break;
            case SeatType::FOURTH():
                $price *= 0.8;
                break;
        }
        return $price;
    }

}