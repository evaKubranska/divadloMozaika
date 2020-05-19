<?php


namespace App\Model;
use Nette\Database\Connection;
use Nette\Utils\DateTime;
use ReflectionException;

class Ticket extends AEntity {
    /** @var int  */
    public ?int $idTicket = null;
    /** @var Seat  */
    public ?Seat $seat = null;
    /** @var Show  */
    public ?Show $show = null;
    /** @var float  */
    public ?float $price = null;
    private ?IPriceTicketCalculator $calculator = null;

    public  function __construct(?Connection $connection = null, ?IPriceTicketCalculator $calculator = null) {
        parent::__construct($connection);
        $this->calculator = $calculator;
    }

    public function getFreeTicket(int $idShow):array {
        return $this->connection->fetchPairs('SELECT T.idTicket as id, T.idTicket as ticket FROM TICKET AS T  WHERE T.idShow = ?  AND T.idTicket NOT IN (SELECT idTicket FROM ticket_booking) ', $idShow);
    }

    public function getFreeTicketByShow(int $idShow) :array {
        $result = $this->connection->fetchAll('SELECT T.idTicket AS idTicket, T.idSeat AS seat_idSeat,  S.row AS seat_row, S.column AS seat_column, ST.category AS seat_seatType, T.price AS price, P.name AS show_play_name  FROM TICKET AS T '.
            ' LEFT JOIN SEAT AS S ON S.idSeat = T.idSeat '.
            ' LEFT JOIN seat_type AS ST ON S.seatType = ST.idSeatType '.
            ' LEFT JOIN `show` AS SH ON SH.idShow = T.idShow '.
            ' LEFT JOIN `play` AS P ON SH.idPlay = P.idPlay '.
            ' WHERE T.idShow = ? ORDER BY S.row DESC, S.column ASC', $idShow);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }


    public function createTicket(Seat $seat, Show $show): void {
    $price = $this->calculator->calculateTicketPrice(  DateTime::from($show->timeFrom), $show->showType, $seat->seatType);
    $this->connection->query('INSERT INTO TICKET (idSeat, idShow, price) VALUES (?,?,?)',$seat->idSeat, $show->idShow,$price);
}

    /**
     * @param int $idBookedTicket
     * @return array
     * @throws ReflectionException
     */
    public function getAllTickets(int $idBookedTicket):array {
        $result = $this->connection->fetchAll('SELECT T.idTicket, T.price,  S.idSeat as seat_idSeat, S.row as seat_row, S.column as seat_column, SH.idShow as show_idShow, U.timeFrom as show_timeFrom, U.timeTo as show_timeTo, P.author as show_play_author, P.name as show_play_name, P.description as show_play_description, P.duration as show_play_duration, P.idPlay as show_play_idPlay, H.name as seat_hall_name   FROM TICKET AS T'.
            'LEFT JOIN SEAT AS S ON S.idSeat = T.idSeat '.
            'LEFT JOIN `SHOW` AS SH ON SH.idShow = T.idShow '.
            'LEFT JOIN PLAY AS P ON P.idPlay = SH.idPlay  '.
            'LEFT JOIN HALL AS H ON H.idHall = S.idHall '.
            'LEFT JOIN usage_rent_show AS URS ON URS.idShow = SH.idShow '.
            'LEFT JOIN `usage` AS U ON U.idUsage = URS.idUsage WHERE T.idBookedTicket = ? ',$idBookedTicket);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param int $idTicket
     * @return Ticket
     * @throws ReflectionException
     */
    public function getTicketDetail(int $idTicket) :Ticket{
        $result = $this->connection->fetch('SELECT T.idTicket AS idTicket, T.idSeat AS seat_idSeat,  S.row AS seat_row, S.column AS seat_column, ST.category AS seat_seatType, T.price AS price, P.name AS show_play_name  FROM TICKET AS T '.
                                                   ' LEFT JOIN SEAT AS S ON S.idSeat = T.idSeat'.
                                                    ' LEFT JOIN seat_type AS ST ON S.seatType = ST.idSeatType'.
                                                    ' LEFT JOIN `show` AS SH ON SH.idShow = T.idShow'.
                                                    ' LEFT JOIN `play` AS P ON SH.idPlay = P.idPlay'.
                                                    ' WHERE T.idTicket = ?', $idTicket);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param int $idBookedTicket
     * @return array
     * @throws ReflectionException
     */
    public function getTicketDetailByBookedTicket(int $idBookedTicket) :array {
        $result = $this->connection->fetchAll('SELECT T.idTicket AS idTicket, T.idSeat AS seat_idSeat,  S.row AS seat_row, '.
                                                 ' S.column AS seat_column, ST.category AS seat_seatType, T.price AS price, P.name AS show_play_name, '.
                                                 ' P.author as show_play_author,P.duration as show_play_duration, U.timeFrom AS show_timeFrom, U.timeTo AS show_timeTo, '.
                                                 ' H.name AS seat_hall_name '.
                                                'FROM TICKET AS T '.
                                                'LEFT JOIN SEAT AS S ON S.idSeat = T.idSeat '.
                                                'LEFT JOIN seat_type AS ST ON S.seatType = ST.idSeatType '.
                                                'LEFT JOIN `show` AS SH ON SH.idShow = T.idShow '.
                                                'LEFT JOIN `play` AS P ON SH.idPlay = P.idPlay '.
                                                'LEFT JOIN ticket_booking AS TB ON TB.idTicket = T.idTicket '.
                                                'LEFT JOIN usage_rent_show AS URS  ON URS.idShow = SH.idShow '.
                                                'LEFT JOIN `usage` AS U ON U.idUsage = URS.idUsage '.
                                                'LEFT JOIN hall AS H ON H.idHall = S.idHall '.
                                                'WHERE TB.idBookedTicket = ?', $idBookedTicket);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param array $ticketsId
     * @return float
     */
    public function getTotalSum(array $ticketsId) :float {
       $response = $this->connection->fetch('SELECT SUM(price)  as totalSum FROM TICKET WHERE idTicket IN (?)', $ticketsId);
        if ($response === null) {
            return 0;
        }
       return (float)$response->offsetGet('totalSum');
    }

    public function getTicketCountByStatus(int $status): array {
        return $this->connection->fetchPairs('SELECT T.idShow,  COUNT(T.idTicket) FROM TICKET AS T '.
            'LEFT JOIN ticket_booking AS TB ON TB.idTicket = T.idTicket '.
            'LEFT JOIN booked_ticket AS BT ON BT.idBookedTicket = TB.idBookedTicket '.
            'WHERE BT.ticketStatus = ? '.
            'GROUP BY  T.idShow,  BT.ticketStatus',$status);
    }
}