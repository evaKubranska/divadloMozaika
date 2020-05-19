<?php


namespace App\Presenters;


use App\Model\Rent;
use App\Model\Show;
use App\Model\Ticket;
use Nette\Database\Connection;

class EmployeeHomepagePresenter extends BasePresenter {

    /** @var Connection @inject */
    public Connection $connection;

   public function renderDefault (): void {
        $rent = new Rent($this->connection);
        $this->template->rentCount =  $rent->getReserveRentCount();
        $show = new Show($this->connection);
        $this->template->shows = $show->getShowsLimited();
        $ticket = new Ticket($this->connection);
        $this->template->countReserveTickets = $ticket->getTicketCountByStatus(1);
        $this->template->countBuyTickets = $ticket->getTicketCountByStatus(2);
   }
}