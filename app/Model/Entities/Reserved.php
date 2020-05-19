<?php

namespace App\Model;

class Reserved implements BookedTicketStatus {
    private const ID=1;
    /**
     * @var BookedTicketContext
     */
    private BookedTicketContext $ticketContext;

    public function __construct(BookedTicketContext $ticketContext) {
        $this->ticketContext = $ticketContext;
    }
    public function cancelBookedTicket() :void {
        $this->ticketContext->setBookedTicketStatus(new Cancelled($this->ticketContext));
    }

    public function purchaseBookedTicket():void {
        $this->ticketContext->setBookedTicketStatus(new Purchased($this->ticketContext));
    }

    public function returnBookedTicketStatusId(): int {
        return self::ID;
    }
}