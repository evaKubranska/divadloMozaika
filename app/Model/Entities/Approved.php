<?php


namespace App\Model;


use Nette\Neon\Exception;

class Approved implements RentStatus {
    private const  ID = 2;
    /**
     * @var RentContext
     */
    private RentContext $rentContext;

    public function __construct(RentContext $rentContext) {
        $this->rentContext = $rentContext;
    }


    public function approveRentRequest(): void {
        throw new Exception('Žiadosť už bola schválená');
    }

    public function rejectRentRequest(): void {
        throw new Exception('Schválenú žiadosť už nie je možné zrušiť');
    }

    public function returnRequestId(): int {
        return self::ID;
    }
}