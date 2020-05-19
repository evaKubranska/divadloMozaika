<?php


namespace App\Model;


use Nette\Database\Connection;
use ReflectionException;

class Seat extends AEntity
{
    /** @var int  */
    public ?int $idSeat = null;
    /** @var Hall  */
    public ?Hall $hall = null;
    /** @var int  */
    public ?int $row = null;
    /** @var int  */
    public ?int $column = null;
    /** @var SeatType  */
    public ?SeatType $seatType = null;

    public  function __construct(Connection $connection = null) {
        parent::__construct($connection);
    }

    /**
     * @param int $idHall
     * @return array
     * @throws ReflectionException
     */
    public function getAllSeats(int $idHall): array {
        $result =$this->connection->fetchAll('SELECT S.idSeat, ST.category as seatType,  S.row, H.idHall as hall_idHall, '.
                                    'S.column, H.name as hall_name,H.description as hall_description, H.capacity as hall_capacity  FROM SEAT as S '.
                                    ' LEFT JOIN SEAT_TYPE AS ST ON ST.idSeatType = S.seatType '.
                                    ' LEFT JOIN HALL AS H ON H.idHall =  S.idHall WHERE H.idHall = ?', $idHall);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }
}

