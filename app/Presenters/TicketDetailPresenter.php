<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\BookedTicket;
use App\Model\BookedTicketContext;
use App\Model\DocumentGenerator;
use Mpdf\MpdfException;
use Nette\Database\Connection;
use ReflectionException;

class TicketDetailPresenter extends BasePresenter {

    /** @var Connection @inject */
    public Connection $connection;
    /** @var DocumentGenerator*/
    public DocumentGenerator $documentGenerator;
   /** @var BookedTicketContext  */
    public BookedTicketContext $bookedTicketContext;

    private  BookedTicket $bookedTicket;

    protected function startup() :void {
        parent::startup();
        $this->documentGenerator = new DocumentGenerator();
        $this->bookedTicketContext = new BookedTicketContext();
        $this->bookedTicket = new BookedTicket($this->connection, $this->bookedTicketContext);
    }

    /**
     * @param int $idBookedTicket
     * @throws ReflectionException
     */
    public function renderDefault(int $idBookedTicket): void {
        $ticketDetail = $this->bookedTicket->getBookedTicketDetail($idBookedTicket);
        $this->template->bookedTicket = $ticketDetail;
}

    /**
     * @param int $id
     * @throws MpdfException
     * @throws ReflectionException
     */
    public function handleConfirm(int $id): void {
        if(isset($id)) {
            $fileName = $this->bookedTicket->buyTicket($id, $this->documentGenerator);
            if($fileName === null) {
                $this->flashMessage('Lístok sa nepodarilo zakúpiť');
            } else {
                $this->template->fileName = $fileName;
                $this->flashMessage('Lístok bol zakúpený');
            }
        }
    }

    /**
     * @param $idBookedTicket
     * @throws MpdfException
     * @throws ReflectionException
     */
    public function handleStorno ($idBookedTicket): void {
        $fileName = $this->bookedTicket->cancelBookedTicket($idBookedTicket, $this->documentGenerator);
        if($fileName === null) {
            $this->flashMessage('Lístok sa nepodarilo stornovat');
        } else {
            $this->template->fileName = $fileName;
            $this->flashMessage('Lístok bol úspešne stornovaný');
        }
    }
    public function handleBack ($idBookedTicket, $status): void {
        if(isset($idBookedTicket) && $status === 3) {
            $this->bookedTicket->deleteTicket($idBookedTicket);
        }
        $this->redirect('SearchTicket:Default');
    }

}