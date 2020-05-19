<?php
declare(strict_types=1);

namespace App\Presenters;


use App\Model\Play;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Connection;

class SearchPlayPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;

    public function createComponentSearchPlayForm(): Form {
    $form = $this->createForm();
    $form->addText('name', '')
        ->setRequired('Prosím vyplňte názov');
    $form->addSubmit('submit', 'Hľadaj');
    $form->onSuccess[]=[$this, 'searchShowPlaySucceeded'];
    $form = $this->makeBootstrapInline($form);
    return $form;
}

    public function searchShowPlaySucceeded(Form $form): void {
        $values =$form->getValues(TRUE);
        $play = new Play($this->connection);
        $this->template->response =  $play->getAllPlayByName((String)$values['name']);;
    }

    /**
     * @param $id
     * @throws AbortException
     */
    public function handleSelect ($id): void {
        if(isset($id)){
            $this->redirect('PlayDetail:Default', (int)$id);
        }
    }

}