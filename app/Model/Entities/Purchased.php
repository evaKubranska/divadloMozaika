<?php


namespace App\Model;



use Nette\Neon\Exception;

class Purchased implements BookedTicketStatus {
    private const ID = 2;
    /**
     * @var BookedTicketContext
     */
    private BookedTicketContext $ticketContext;

    public function __construct(BookedTicketContext $ticketContext ) {
        $this->ticketContext = $ticketContext;
    }


    public function cancelBookedTicket():void {
        $this->ticketContext->setBookedTicketStatus(new Cancelled($this->ticketContext));
    }

    public function purchaseBookedTicket() :void {
        throw new Exception('Lístok už bol schválený');
    }

    public function returnBookedTicketStatusId(): int {
        return self::ID;
    }
}