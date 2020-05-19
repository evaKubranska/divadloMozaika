<?php


namespace App\Model;
use Exception;
use Nette\Database\Connection;
use Nette\Utils\DateTime;

class Rent extends Usage  {
    /** @var int  */
    public ?int $idRent = null;
    /** @var String  */
    public ?String $description = null;
    /** @var float  */
    public ?float $price = null;
    /** @var Customer  */
    public ?Customer $customer = null;
    private ?RentContext $rentContext = null;
    private ?IPriceRentCalculator $calculator = null;

    /**
     * Rent constructor.
     * @param Connection|null $connection
     * @param IPriceRentCalculator|null $calculator
     * @param RentContext|null $rentContext
     */
    public  function __construct(?Connection $connection = null, ?IPriceRentCalculator $calculator = null, ?RentContext $rentContext = null) {
        parent::__construct($connection);
        $this->calculator = $calculator;
        $this->rentContext = $rentContext;
    }

    /**
     * @param Hall $hall
     * @param DateTime $timeFrom
     * @param DateTime $timeTo
     * @return float
     */
    public function calculatePrice(Hall $hall, DateTime $timeFrom, DateTime $timeTo):float {
        $this->price =  $this->calculator->calculateRentPrice($timeFrom, $timeTo, $hall->hallType, $hall->capacity);
        return $this->price;
    }

    /**
     * @param int $idHall
     * @param DateTime $timeFrom
     * @param DateTime $timeTo
     * @param float $price
     * @param int $idCustomer
     * @param String $description
     */
    public function reserveRent(int $idHall, DateTime $timeFrom, DateTime $timeTo, float $price, int $idCustomer, String $description):void {
        $this->connection->query('INSERT INTO `usage` ( `timeFrom`, `timeTo`, `idHall`) VALUES (?,?,?);',$timeFrom, $timeTo, $idHall);
        $this->idUsage =$this->connection->getInsertId();
        $rentStatusId = $this->rentContext->returnRequestId();
        $this->connection->query('INSERT INTO `rent` ( `idCustomer`, `rentStatus`, `description`, `price`) VALUES (?,?,?,?);',$idCustomer, $rentStatusId, $description, $price);
        $this->idRent = $this->connection->getInsertId();
        $this->connection->query('INSERT INTO usage_rent_show (idUsage, idRent) VALUES(?,?)',$this->idUsage, $this->idRent);
    }

    /**
     * @param int $idRent
     * @throws \ReflectionException
     */
    public function rejectRent(int $idRent): void {
        $res = $this->connection->fetch('SELECT rentStatus, idCustomer FROM RENT WHERE idRent = ?', $idRent);
        $currentStatus = (int)$res->offsetGet('rentStatus');
        $idCustomer = (int)$res->offsetGet('idCustomer');
        if($currentStatus === 2) {
            $this->rentContext->setRentStatus($this->rentContext->getApprovedStatus());
        } elseif($currentStatus === 3) {
            $this->rentContext->setRentStatus($this->rentContext->getRejectedStatus());
        }
        $success = true;
        try {
            $this->rentContext->rejectRentRequest();
        } catch (Exception $e) {
            $success = false;
        }
        if($success){
            $id = $this->rentContext->getRentStatus()->returnRequestId();
            $this->connection->query('UPDATE RENT SET rentStatus = ? WHERE idRent = ?', $id,$idRent);
            $customer = new Customer($this->connection);
            $customer = $customer->getCustomerDetail($idCustomer);
            RentMailSender::sendRejectionMail($customer);
        }
    }
    public function approveRent(int $idRent): void {
        $res = $this->connection->fetch('SELECT rentStatus, idCustomer FROM RENT WHERE idRent = ?', $idRent);
        $currentStatus = (int)$res->offsetGet('rentStatus');
        $idCustomer = (int)$res->offsetGet('idCustomer');
        if($currentStatus === 2) {
            $this->rentContext->setRentStatus($this->rentContext->getApprovedStatus());
        } elseif($currentStatus === 3) {
            $this->rentContext->setRentStatus($this->rentContext->getRejectedStatus());
        }
        $success = true;
        try {
                $this->rentContext->approveRentRequest();
        } catch (Exception $e) {
            $success = false;
        }
        if($success){
            $id = $this->rentContext->getRentStatus()->returnRequestId();
            $this->connection->query('UPDATE RENT SET rentStatus = ? WHERE idRent = ?', $id,$idRent);
            $customer = new Customer($this->connection);
            $customer = $customer->getCustomerDetail($idCustomer);
            RentMailSender::sendAcceptationMail($customer);
        }
    }

    /**
     * @param int $rentStatus
     * @return array
     * @throws \ReflectionException
     */
    public function getAllReserveRent(int $rentStatus): array {
        $result =$this->connection->fetchAll('SELECT H.idHall as hall_idHall, H.name as hall_name, H.capacity as hall_capacity, HT.name as hall_hallType, U.timeFrom, U.timeTo, R.idRent, R.idCustomer as customer_idCustomer ' .
            'FROM usage_rent_show AS URS ' .
            'LEFT JOIN RENT AS R ON URS.idRent = R.idRent ' .
            'LEFT JOIN `usage` AS U ON URS.idUsage = U.idUsage ' .
            'LEFT JOIN  hall AS H ON U.idHall = H.idHall ' .
            'LEFT JOIN  hall_type AS HT ON H.hallType = HT.idHallType ' .
            'WHERE R.rentStatus = ?', $rentStatus );
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param int $idRent
     * @return Rent
     * @throws \ReflectionException
     */
    public function getRentDetail(int $idRent): Rent {
        $result = $this->connection->fetch('SELECT U.timeFrom, U.timeTo, H.name as hall_name, H.description as hall_description,  HT.name as hall_hallType, H.capacity as hall_capacity, R.description, C.firstName as customer_firstName, C.lastName as customer_lastName, '.
            'C.phone as customer_phone, C.email as customer_email, C.street as customer_street, C.houseNumber as customer_houseNumber, '.
            'C.city as customer_city, C.zip as customer_zip  FROM usage_rent_show AS URS ' .
            ' LEFT JOIN RENT AS R ON URS.idRent = R.idRent ' .
            ' LEFT JOIN `usage` AS U ON URS.idUsage = U.idUsage ' .
            ' LEFT JOIN  hall AS H ON U.idHall = H.idHall ' .
            ' LEFT JOIN  hall_type AS HT ON H.hallType = HT.idHallType ' .
            ' LEFT JOIN customer AS C ON C.idCustomer = R.idCustomer ' .
            ' WHERE  R.idRent =? ', $idRent);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    public function getReserveRentCount() : int {
        $res = $this->connection->fetch('SELECT COUNT(idRent) as countRent FROM RENT WHERE rentStatus = ? ', 1);
        if($res === null) {
            return 0;
        }
        return  (int)$res->offsetGet('countRent');
    }

}