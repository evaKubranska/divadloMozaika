<?php


namespace App\Presenters;


use App\Model\Hall;
use App\Model\PriceRentCalculator;
use App\Model\Rent;
use App\Model\RentContext;
use Nette\Application\AbortException;
use Nette\Database\Connection;
use Nette\Utils\DateTime;
use ReflectionException;

class RentLeaseDetailPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;
    private int $idHall;
    private DateTime $timeFrom;
    private DateTime $timeTo;
    private float $price;
    /** @var RentContext  */
    public RentContext $rentContext;
    public PriceRentCalculator $calculator;

    public function startup() :void {
        parent::startup();
        $this->idHall = $this->getParameter('idHall');
        $this->timeFrom = DateTime::from($this->getParameter('timeFrom'));
        $this->timeTo = DateTime::from($this->getParameter('timeTo'));
    }

    /**
     * @param int $idHall
     * @param String $timeFrom
     * @param String $timeTo
     * @throws ReflectionException
     */
    public function renderDefault (int $idHall, String $timeFrom, String $timeTo) : void {
        $hall = new Hall($this->connection);
        $hallDetail = $hall->getHallDetail($idHall);
        $this->template->detail  = $hallDetail;
        $this->template->timeFrom = $timeFrom;
        $this->template->timeTo = $timeTo;
        $this->calculator = new PriceRentCalculator();
        $rent = new Rent($this->connection, $this->calculator);
        $this->template->price = $rent->calculatePrice($hallDetail,  DateTime::from($timeFrom), DateTime::from( $timeTo));
    }

    /**
     * @param $idHall
     * @param $timeFrom
     * @param $timeTo
     * @param $price
     * @throws AbortException
     */
    public function handleSelect($idHall, $timeFrom, $timeTo, $price): void {
        if(isset($idHall, $timeFrom, $timeTo, $price)) {
            $this->redirect('RentPersonalInfo:Default', (int)$idHall, $timeFrom, $timeTo,(float)$price);
        }
    }
}