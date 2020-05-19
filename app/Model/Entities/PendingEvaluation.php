<?php


namespace App\Model;


class PendingEvaluation implements RentStatus {
    private const  ID = 1;
    /**
     * @var RentContext
     */
    private RentContext $rentContext;

    public function __construct(RentContext $rentContext) {
        $this->rentContext = $rentContext;
    }

    public function approveRentRequest(): void {
            $this->rentContext->setRentStatus(new Approved($this->rentContext));
    }

    public function rejectRentRequest(): void {
        $this->rentContext->setRentStatus(new Rejected($this->rentContext));
    }

    public function returnRequestId(): int {
       return self::ID;
    }
}