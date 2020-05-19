<?php


namespace App\Model;


use Nette\Utils\DateTime;

class PriceRentCalculator implements IPriceRentCalculator {
    private const PRICE_BASE = 50.00;
    private const EXPENSIVE_DAYS = ['Sat', 'Fri'];


    public  function calculateRentPrice(DateTime $timeFrom, DateTime $timeTo, string $hallType, int $capacity):float {
        $price = $this->calculatePriceByTimeLength($timeFrom, $timeTo);
        $price = $this->calculatePriceByCapacity($capacity, $price);
        $price = $this->calculatePriceByHallType($hallType, $price);
        return $price;
    }

    private function calculatePriceByTimeLength(DateTime $timeFrom, DateTime $timeTo) : float {
        $timeDifference = $timeFrom->diff($timeTo);
        $price = 0;
        if($timeDifference->d > 0 &&  $timeDifference->h === 0) {
            $price = self::PRICE_BASE * 24 * $timeDifference->d;
        } elseif ($timeDifference->d > 0 &&  $timeDifference->h !== 0) {
            $price = self::PRICE_BASE * (24 * $timeDifference->d + $timeDifference->h);
        } else {
            $price = self::PRICE_BASE *$timeDifference->h;
        }
        if(in_array($timeFrom->format('D'), self::EXPENSIVE_DAYS, false) !== false){
            $price *= 1.2;
        }
        return $price;
    }

    private function calculatePriceByCapacity(int $capacity, float $price): float {
        if ($capacity < 80) {
            $price *= 0.8;
        } elseif ($capacity >= 120) {
            $price *= 1.2;
        }
        return $price;
    }


    private function calculatePriceByHallType(String $hallType, float $price): float {
        switch ($hallType) {
            case HallType::STUDIO():
               $price *= 0.9; break;
            case HallType::CINOHRA():
                $price *= 1.5; break;
            case HallType::OPERA():
                $price *= 1.2; break;
        }
        return $price;
    }
}