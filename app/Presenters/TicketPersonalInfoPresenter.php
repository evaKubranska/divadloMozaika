<?php


namespace App\Presenters;


use App\Model\BookedTicket;
use App\Model\BookedTicketContext;
use App\Model\Customer;
use Nette\Application\AbortException;
use Nette\Database\Connection;
use Nette\Forms\Form;

class TicketPersonalInfoPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;
    private array $tickets;
    public BookedTicketContext $ticketContext;

    public function startup() :void {
        parent::startup(); // TODO: Change the autogenerated stub
        $this->tickets = $this->getParameter('ticketsId');
    }

    public function renderDefault(array $ticketsId):void {
    }

    public function createComponentTicketPersonalInfoForm() :Form{
        $form = $this->createForm();
        $form->addText('firstName', 'Meno:')
            ->setRequired('Prosím vyplňte meno');
        $form->addText('lastName', 'Priezvisko:')
            ->setRequired('Prosím vyplňte priezvisko');
        $form->addText('phone', 'Telefónne číslo:')
            ->setEmptyValue('+42')
            ->setHtmlType('phone')
            ->setHtmlAttribute('placeholder', 'Prosím  vyplňte telefónne číslo');
        $form->addEmail('email', 'Email:')
            ->addRule(Form::FILLED, 'Prosím vyplňte email')
            ->addRule(Form::EMAIL, 'Email nemá správný formát');
        $form->addText('street', 'Ulica:')
            ->addRule(Form::FILLED, 'Prosím vyplňte ulicu');
        $form->addText('houseNumber', 'Číslo domu:')
            ->addRule(Form::FILLED, 'Prosím vyplňte číslo domu');
        $form->addText('zip', 'PSČ:')
            ->addRule(Form::FILLED, 'Prosím vyplňte PSČ');
        $form->addText('city', 'Mesto:')
            ->addRule(Form::FILLED, 'Prosím vyplňte mesto');
        $form->addSubmit('back', 'Zrušiť')
            ->setValidationScope([])
            ->onClick[]= [$this, 'backPersonalInfoForm'];
        $form->addSubmit('submit', 'Rezervovať');
        $form->onSuccess[]=[$this, 'PersonalInfoFormSucceeded'];
        $form = $this->makeBootstrapPersonalInfo($form);
        return $form;
    }
    /**
     * @throws AbortException
     */
    public function backPersonalInfoForm():void{
        $this->redirect(':CustomerHomepage:default');
    }

    /**
     * @param Form $form
     * @throws AbortException
     * @throws \ReflectionException
     */
    public function PersonalInfoFormSucceeded(Form $form): void {
        $values = $form->getValues(TRUE);
        $customer = new Customer($this->connection);
        $idCustomer = $customer->createCustomer($values['firstName'], $values['lastName'], $values['email'], $values['phone'], $values['street'], $values['houseNumber'], $values['zip'], $values['city']);
        $this->ticketContext = new BookedTicketContext();
        $bookedTicket = new BookedTicket($this->connection, $this->ticketContext);
        $bookedTicket->reserveBookedTicket($this->tickets, $idCustomer);
        $this->flashMessage('Rezervácia bola prijatá, email s pokynmi bol zaslaný na zadanú emailovú adresu', 'success');
        $this->redirect(':CustomerHomepage:default');
    }
}