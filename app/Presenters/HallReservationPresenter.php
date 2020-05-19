<?php


namespace App\Presenters;


use App\Model\Hall;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Connection;
use Nette\Utils\DateTime;

class HallReservationPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;


    public function createComponentSearchLeaseHallForm(): Form {
        $form = $this->createForm();
        $hall = new Hall($this->connection);
        $capacity = $hall->getAllCapacity();
        $form->addText('dateFrom', 'Čas od')
            ->setRequired('Povinne')
            ->setHtmlType('text');
        $form->addText('dateTo', 'Čas do')
            ->setRequired('Povinne')
            ->setHtmlType('Date');
        $form->addSelect('capacity', 'Typ saly', $capacity)
            ->setRequired('povinne');
        $form->addSubmit('submit', 'Hľadaj');
        $form->onSuccess[]=[$this, 'searchShowFormSucceeded'];
        return $form;
    }

    public function searchShowFormSucceeded(Form $form): void {
        $values =$form->getValues(TRUE);
        $dateFrom = DateTime::createFromFormat('d.m.Y H:i', $values['dateFrom']);
        $dateTo = DateTime::createFromFormat('d.m.Y H:i', $values['dateTo']);
        $capacity =$values['capacity'];
        $hall = new Hall($this->connection);
        $res = $hall->getFreeHallsWithCapacity($dateFrom, $dateTo, (int)$capacity);
        $this->template->response = $res;
        $this->template->dateFrom = (string)$dateFrom;
        $this->template->dateTo = (string)$dateTo;
    }

    /**
     * @param $id
     * @param $dateFrom
     * @param $dateTo
     * @throws AbortException
     */
    public function handleSelect($id, $dateFrom, $dateTo): void {
        if(isset($id, $dateFrom, $dateTo)){
          $this->redirect('RentLeaseDetail:Default', (int)$id, $dateFrom, $dateTo);
        }
    }
}