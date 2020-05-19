<?php
declare(strict_types=1);

namespace App\Presenters;
use App\Model\Hall;
use App\Model\Play;
use App\Model\PriceTicketCalculator;
use App\Model\Show;
use App\Model\ShowType;
use Nette\Application\AbortException;
use Nette\Database\Connection;
use Nette\Utils\DateTime;
use Nette\Application\UI\Form;
use ReflectionException;

class AddShowPresenter  extends BasePresenter {

    private ?int $idHall = null;
    private DateTime $dateFrom ;
    private DateTime $dateTo;
     /** @var Connection @inject */
    public Connection $connection;
    /** @var PriceTicketCalculator */
    public PriceTicketCalculator $calculator;

    /**
     * @param int|null $idHall
     * @param String|null $dateFrom
     * @param String|null $dateTo
     * @throws ReflectionException
     */
    public function renderDefault (int $idHall = null, String $dateFrom = null, String $dateTo = null) : void {
        $dateFrom = DateTime::from($dateFrom);
        $dateTo = DateTime::from( $dateTo);
        $this->idHall=$idHall;
        $this->dateFrom =$dateFrom;
        $this->dateTo = $dateTo;
        $hall = new Hall($this->connection);
        $this->template->post = $hall->getHallDetail($this->idHall);
    }

    /**
     * @return Form
     */
    protected function createComponentAddShowForm(): Form {
        $this->dateFrom = $this->dateFrom ?? DateTime::from(0);
        $this->dateTo = $this->dateTo ?? DateTime::from(0);
        $play = new Play($this->connection);
        $listPlay = $play->getAllPlays();
        $form = $this->createForm();
        $form->addHidden('id_hall')
            ->setDefaultValue($this->idHall);
        $form->addHidden('dateFrom')
            ->setDefaultValue($this->dateFrom);
        $form->addHidden('dateTo')
            ->setDefaultValue($this->dateTo);
        $form->addSelect('play', 'Inscenácia:')
                ->setPrompt('Vyberte inscenáciu')
                ->setRequired('Prosím vyberte inscenáciu')
                ->setItems($listPlay, true);
        $typeShow = ShowType::toArray();
        $form->addRadioList('showType', 'Typ predstavenia:', $typeShow)
            ->setRequired('Prosím vyberte typ predstavenia');
        $form->addSubmit('back', 'Späť')
            ->setValidationScope([])
            ->onClick[]= [$this, 'backAddShowFrom'];
        $form->addSubmit('submit', 'Pridať');
        $form->onSuccess[]=[$this, 'addShowFormSucceeded'];
        $form = $this->makeBootstrap4AddShow($form);
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function backAddShowFrom():void {
        $this->redirect(':EmployeeHomepage:default');
    }

    /**
     * @param Form $form
     * @throws AbortException
     * @throws ReflectionException
     */
    public function addShowFormSucceeded(Form $form): void {
        $values = $form->getValues(TRUE);
        $showType = ShowType::getValueByKey($values['showType']);
        $idPlay = (int)$values['play'];
        $this->idHall = (int)$values['id_hall'];
        $this->dateFrom = DateTime::from( $values['dateFrom']);
        $this->dateTo = DateTime::from( $values['dateTo']);
        $this->calculator = new PriceTicketCalculator();
        $show = new Show($this->connection, $this->calculator);
        $show->createShow($this->idHall, $this->dateFrom, $this->dateTo, $idPlay, $showType);
        $this->flashMessage('Predstavenie bolo úspešne pridané ', 'info');
        $this->redirect(':EmployeeHomepage:default');

    }
}