<?php
declare(strict_types=1);
namespace App\Presenters;
use App\Model\Hall;
use App\Model\HallType;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Connection;
use Nette\Utils\DateTime;

class SearchHallForPlayPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;

    public function createComponentSearchHallForm(): Form {
        $form = $this->createForm();
        $hallType = HallType::toArray();
        $form->addText('dateFrom', 'Čas od')
            ->setRequired('Čas od je povinné pole')
            ->setHtmlType('Date');
        $form->addText('dateTo', 'Čas do')
            ->setRequired('Čas do je povinné pole')
            ->setHtmlType('Date');
        $form->addSelect('hallType', 'Typ sály', $hallType)
            ->setDefaultValue('STUDIO');
        $form->addSubmit('submit', 'Hľadať');
        $form->onSuccess[]=[$this, 'searchShowFormSucceeded'];
        return $form;
    }

    public function searchShowFormSucceeded(Form $form): void {
       $values =$form->getValues(TRUE);
       $dateFrom = DateTime::createFromFormat('d.m.Y H:i', $values['dateFrom']);
       $dateTo = DateTime::createFromFormat('d.m.Y H:i', $values['dateTo']);
       $hallType =$values['hallType'];
       $hall = new Hall( $this->connection);
       $this->template->response = $hall->getFreeHalls($dateFrom, $dateTo, (string)$hallType);
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
           $this->redirect('AddShow:Default', (int)$id, $dateFrom, $dateTo);
    }

}