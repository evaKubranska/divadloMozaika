<?php


namespace App\Model;



use Nette\Neon\Exception;

class Rejected implements RentStatus {
    private const  ID = 3;
    /**
     * @var RentContext
     */
    private RentContext $rentContext;

    public function __construct(RentContext $rentContext) {
        $this->rentContext = $rentContext;
    }

    public function approveRentRequest(): void {
        throw new Exception('Zamietunuzú žiadosť už nie je možné shváliť');
    }

    public function rejectRentRequest(): void {
        throw new Exception('Žiadosť už bola zamietnutá');
    }

    public function returnRequestId(): int {
        return self::ID;
    }
}