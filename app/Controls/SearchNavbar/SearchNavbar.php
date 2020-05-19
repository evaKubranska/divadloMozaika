<?php


namespace App\Controls;


use Nette\Application\AbortException;
use Nette\Application\UI\Form;

class SearchNavbar extends BaseControl {

    public function render(): void {
        $this->template->setFile(__DIR__.'/SearchNavbar.latte');
        $this->template->render();
    }

    public function createComponentSearchShowForm(): Form {
        $form = new Form();
        $form->addText('name', '')
            ->setNullable(true);
        $form->addText('dateFrom', '')
            ->setNullable(true);
        $form->addSubmit('submit', 'Hľadaj');
        $form->onSuccess[]=[$this, 'searchShowSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     * @throws AbortException
     */
    public function searchShowSuccess(Form $form): void {
        $values =$form->getValues(TRUE);
        $name = $values['name'];
        $date = $values['dateFrom'];
        $this->presenter->redirect(':SearchPlayResults:Default',$name, $date);
    }
}