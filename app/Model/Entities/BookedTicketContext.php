<?php


namespace App\Model;


class BookedTicketContext {
    private BookedTicketStatus $purchased;
    private BookedTicketStatus $cancelled;
    private BookedTicketStatus $reserved;
    private BookedTicketStatus $bookedTicketStatus;

    public function __construct() {
      $this->purchased = new Purchased($this);
      $this->cancelled = new Cancelled($this);
      $this->reserved = new Reserved($this);
      $this->bookedTicketStatus = $this->reserved;
    }

    /**
     * @return BookedTicketStatus
     */
    public function getBookedTicketStatus(): BookedTicketStatus
    {
        return $this->bookedTicketStatus;
    }

    /**
     * @param BookedTicketStatus $bookedTicketStatus
     */
    public function setBookedTicketStatus($bookedTicketStatus): void {
        $this->bookedTicketStatus = $bookedTicketStatus;
    }

    public function  purchaseBookedTicketStatus():void{
        $this->bookedTicketStatus->purchaseBookedTicket();
    }
    public function cancelBookedTicketStatus():void{
        $this->bookedTicketStatus->cancelBookedTicket();
    }

    public function returnBookedTicketStatusId():int {
       return $this->bookedTicketStatus->returnBookedTicketStatusId();
    }

    public function getReservedState() :BookedTicketStatus {
        return $this->reserved;
    }
    public function getPurchasedState() :BookedTicketStatus {
        return $this->purchased;
    }
    public function getCancelledState() :BookedTicketStatus {
        return $this->cancelled;
    }



}