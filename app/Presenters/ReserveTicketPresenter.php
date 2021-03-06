<?php


namespace App\Presenters;

use App\Model\Show;
use App\Model\Ticket;
use Nette\Application\AbortException;
use Nette\Database\Connection;
use ReflectionException;

class ReserveTicketPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;
    private int $idShow;


    protected function startup():void {
        parent::startup(); // TODO: Change the autogenerated stub
        $this->idShow = $this->getParameter('idShow');
    }

    /**
     * @param int $idShow
     * @throws AbortException
     * @throws ReflectionException
     */
    public function renderDefault(int $idShow):void {
        $ticket = new Ticket($this->connection);
        $tickets = $ticket->getFreeTicketByShow($idShow);
        $reserveTicket = $ticket->getFreeTicket($idShow);
        if($reserveTicket === null) {
            $this->flashMessage('Pre vybrané predstavenie nie sú k dispozícii žiadne lístky');
            $this->redirect('CustomerHomepage:default');
        } else {
            $this->template->tickets = $tickets;
            $this->template->reserveTicket = $reserveTicket;
        }
        $show = new Show($this->connection);
        $this->template->show = $show->getShowDetail($this->idShow);
    }

    /**
     * @throws AbortException
     */
    public function handleSave():void {
        $ticketsId = $this->request->getPost('ticketsId');
        $ticketsIdDecoded = json_decode($ticketsId, true, 512, JSON_THROW_ON_ERROR);
        if(empty($ticketsIdDecoded)) {
            $this->flashMessage('Najprv vyberte lístky', 'info');
        } else {
            $this->redirect(':TicketPersonalInfo:default', ['ticketsId' => $ticketsIdDecoded]);
        }
    }

}