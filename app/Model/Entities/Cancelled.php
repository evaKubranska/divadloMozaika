<?php


namespace App\Model;



use Nette\Neon\Exception;

class Cancelled implements BookedTicketStatus {
    private const ID = 3;
    /**
     * @var BookedTicketContext
     */
    private BookedTicketContext $ticketContext;

    public function __construct(BookedTicketContext $ticketContext) {
        $this->ticketContext = $ticketContext;
    }

    public function cancelBookedTicket():void {
        throw new Exception('Listok už bol zrušený');
    }

    public function purchaseBookedTicket():void{
        throw new Exception('Zrušený lístok nie je možné zakúpiť');
    }

    public function returnBookedTicketStatusId(): int {
      return self::ID;
    }
}