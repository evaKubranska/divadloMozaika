<?php
declare(strict_types=1);

namespace App\Presenters;


use App\Model\BookedTicket;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Connection;

class SearchTicketPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;

    public function createComponentSearchTicketFrom(): Form {
        $ticketType = [
            '1' => 'zarezervovaný',
            '2' => 'zakúpený'
        ];
        $form = $this->createForm();
        $form->addGroup();
        $form->addText('idTicket', 'Číslo lístka')
        ->setHtmlType('number');
        $form->addSelect('ticket_type', 'Stav lístka:')
            ->setItems($ticketType, true);
        $form->addGroup();
        $form->addText('name', 'meno');
        $form->addText('surname', 'priezvisko');
        $form->addGroup();
        $form->addSubmit('submit', 'Hľadať');
        $form->onSuccess[]=[$this, 'searchTicketSucceeded'];
        $form = $this->makeBootstrapSearch($form);
        return $form;
    }


    public function searchTicketSucceeded(Form $form): void
    {
        $values = $form->getValues(TRUE);
        $idTicket = (int)$values['idTicket'];
        $ticketType = (int)$values['ticket_type'];
        $firstName = $values['name'];
        $lastName = $values['surname'];
        $bookedTicket = new BookedTicket($this->connection);
        $result = $bookedTicket->getAllBookedTicket($idTicket, (string)$firstName, (string)$lastName, $ticketType);
        if(empty($result)){
            $this->template->answear = 'Vyhľadávacím kritériám neodpovedajú žiadne výsledky';
        } else {
            $this->template->tickets = $result;
        }
    }

    /**
     * @param $id
     * @throws AbortException
     */
    public function handleSelect ($id): void
    {
        if(isset($id)){
            $this->redirect('TicketDetail:Default', (int)$id);
        }
    }
}