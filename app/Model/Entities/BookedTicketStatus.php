<?php


namespace App\Model;


interface BookedTicketStatus {
    public function cancelBookedTicket();
    public function purchaseBookedTicket();
    public function returnBookedTicketStatusId():int;
}