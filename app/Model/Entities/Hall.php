<?php


namespace App\Model;

use Nette\Database\Connection;
use Nette\Utils\DateTime;
use ReflectionException;

class Hall extends AEntity
{

    /** @var int  */
    public ?int $idHall = null;
    /** @var String */
    public ?String $name = null;
    /** @var int  */
    public ?int $capacity = null;
    /** @var String  */
    public ?String $description = null;
    /** @var HallType  */
    public ?HallType $hallType = null;

    public  function __construct(Connection $connection = null) {
        parent::__construct($connection);
    }

    public function getAllCapacity():array {
        return $this->connection->fetchPairs('SELECT capacity as capacity_key, capacity as capacity_value FROM HALL GROUP BY  capacity');
    }

    /**
     * @param int $idHall
     * @return mixed
     * @throws ReflectionException
     */
    public function getHallDetail(int $idHall):Hall {
        $result =$this->connection->fetch('SELECT H.idHall, H.name, H.capacity, H.description, HT.name as hallType  FROM HALL AS H  ' .
            'LEFT JOIN HALL_TYPE AS HT ON H.hallType = HT.idHallType ' .
            ' WHERE idHall = ?', $idHall);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param DateTime $timeFrom
     * @param DateTime $timeTo
     * @param string $hallType
     * @return array
     * @throws ReflectionException
     */
    public function getFreeHalls(DateTime $timeFrom, DateTime $timeTo, string $hallType) : array{
        $idHallType = HallType::getIntegerKey($hallType);
        $result = $this->connection->fetchAll('SELECT H.idHall, H.name, H.capacity, HT.name as hallType from hall AS H '.
                                                    'LEFT JOIN hall_type AS HT ON HT.idHallType = H.hallType '.
                                                    ' WHERE H.hallType = ? AND H.idHall NOT IN ( SELECT idHall '.
                                                    'FROM `usage` AS u WHERE (u.timeFrom BETWEEN ? AND ?)'.
                                                    'AND (u.timeTo BETWEEN ? AND ?))', $idHallType, $timeFrom, $timeTo, $timeFrom,  $timeTo);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param DateTime $timeFrom
     * @param DateTime $timeTo
     * @param int $capacity
     * @return mixed
     * @throws ReflectionException
     */
    public function getFreeHallsWithCapacity(DateTime $timeFrom, DateTime $timeTo, int $capacity){
        $result = $this->connection->fetchAll('SELECT H.idHall, H.name, H.capacity, HT.name as hallType   from hall AS H ' .
            'LEFT JOIN HALL_TYPE AS HT ON H.hallType = HT.idHallType ' .
            'WHERE  H.capacity >= ? AND H.idHall NOT IN ( SELECT idHall '.
            'FROM `usage` AS u WHERE (u.timeFrom BETWEEN ? AND ?)'.
            'AND (u.timeTo BETWEEN ? AND ?))', $capacity, $timeFrom, $timeTo, $timeFrom,  $timeTo );
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }
}