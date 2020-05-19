<?php


namespace App\Model;
use Nette\Database\Connection;
use Nette\Utils\DateTime;
use ReflectionException;

class Show extends Usage {
    /** @var int  */
    public ?int $idShow = null;
    /** @var Play  */
    public ?Play $play = null;
    /** @var ShowType  */
    public ?ShowType $showType = null;
    private ?IPriceTicketCalculator $calculator = null;

    public  function __construct(?Connection $connection = null, ?IPriceTicketCalculator $calculator = null) {
        parent::__construct($connection);
        $this->calculator = $calculator;
    }

    /**
     * @param int $idPlay
     * @return array
     * @throws ReflectionException
     */
    public function getShowsForPlay(int $idPlay): array {
        $dt = new DateTime();
       $result = $this->connection->fetchAll('SELECT  S.idShow, U.timeFrom,U.idUsage, U.timeTo, H.name as hall_name, H.capacity as hall_capacity, SH.name as showType FROM `show` AS S ' .
           'LEFT JOIN usage_rent_show AS URS ON URS.idShow = S.idShow ' .
           'LEFT JOIN `usage` AS U ON URS.idUsage = U.idUsage  ' .
           'LEFT JOIN HALL AS H ON U.idHall = H.idHall  ' .
           'LEFT JOIN SHOW_TYPE AS SH ON S.showType = SH.idShowType ' .
           'WHERE S.idPlay = ? AND U.timeFrom > ? ', $idPlay,$dt->format('Y-m-d H:i:s'));

        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param int $idHall
     * @param DateTime $timeFrom
     * @param DateTime $timeTo
     * @param int $idPlay
     * @param string $showType
     * @throws ReflectionException
     */
   public function createShow(int $idHall, DateTime $timeFrom, DateTime $timeTo, int $idPlay, string $showType): void {
        $idShowType =ShowType::getIntegerKey($showType);
        $this->connection->query('INSERT INTO `usage` ( `timeFrom`, `timeTo`, `idHall`) VALUES (?,?,?);',$timeFrom, $timeTo, $idHall);
        $idUsage =$this->connection->getInsertId();
        $this->connection->query('INSERT INTO `show` ( `idPlay`, `showType`) VALUES (?,?)',$idPlay, $idShowType);
        $idShow = $this->connection->getInsertId();
        $this->connection->query('INSERT INTO usage_rent_show (idUsage, idShow) VALUES(?,?)',$idUsage, $idShow);
        $seat = new Seat($this->connection);
        $seats = $seat->getAllSeats($idHall);
        foreach ($seats as $item){
             $ticket = new Ticket($this->connection,$this->calculator);
             $ticket->createTicket($item,$this->getShowDetail($idShow));
        }
   }

   public function removeShow(int $idShow, int $idUsage): void {
        $bookedTicket = new BookedTicket($this->connection);
        $res = $bookedTicket->getAllBookedTicketById($idShow);
        foreach ($res as $result) {
            //zarezervovany
            if($result->ticketStatus === 1) {
                 TicketMailSender::sendCancelReservationMail($result);
                //kupeny
            } elseif($result->ticketStatus === 2){
               TicketMailSender::sendCancelBuyTicketMail($result);
            }
            $this->connection->query('DELETE FROM ticket_booking WHERE idBookedTicket = ?',$result->idBookedTicket);
            $this->connection->query('DELETE FROM booked_ticket WHERE idBookedTicket = ?',$result->idBookedTicket);
            $this->connection->query('DELETE FROM CUSTOMER WHERE idCustomer = ?',$result->customer->idCustomer);
        }
        $this->connection->query('DELETE FROM usage_rent_show WHERE idShow = ?',$idShow);
        $this->connection->query('DELETE FROM `usage` WHERE idUsage = ?', $idUsage);
        $this->connection->query('DELETE FROM ticket WHERE idShow = ?', $idShow);
        $this->connection->query('DELETE FROM `show` WHERE idShow = ?', $idShow);
   }

    /**
     * @param String|null $name
     * @param DateTime $date
     * @return array
     * @throws ReflectionException
     */
   public function getAllShow(?String $name, DateTime $date): array {
        $sql = 'SELECT S.idShow, U.timeFrom, U.timeTo, H.name as hall_name, H.capacity as hall_capacity, ST.name  as showType, P.author as play_author, P.name as play_name, P.description as play_description, P.duration as play_duration, P.idPlay as play_idPlay, P.image as play_image  FROM `show` AS S ' .
            'LEFT JOIN usage_rent_show AS URS ON URS.idShow = S.idShow ' .
            'LEFT JOIN  `usage` AS U ON U.idUsage = URS.idUsage ' .
            'LEFT JOIN  HALL AS H ON U.idHall = H.idHall ' .
            'LEFT JOIN show_type AS ST ON ST.idShowType = S.showType ' .
                "LEFT JOIN  play AS P ON P.idPlay = S.idPlay WHERE U.timeFrom > \"$date\"";
        $sql .= ($name !== null)? ' AND LOWER(P.name ) LIKE LOWER("%' .$name. '%")' : '';
        $sql .= ' ORDER BY  U.timeFrom ASC LIMIT 10';
        $result = $this->connection->fetchAll($sql);
       $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
       return unserialize(serialize($res),['allowed_classes => true']);
   }

    /**
     * @param int $month
     * @return array
     * @throws ReflectionException
     */
   public function  getAllFilteredShow(int $month):array {
       $today = DateTime::from(0);
       $year = ($today->format('n') > $month) ? (int)$today->format('Y')+1 : $today->format('Y');
       $sql = 'SELECT S.idShow, U.timeFrom, U.timeTo, H.name as hall_name, H.capacity as hall_capacity, ST.name  as showType, P.author as play_author, P.name as play_name, P.description as play_description, P.duration as play_duration, P.idPlay as play_idPlay, P.image as play_image FROM `show` AS S ' .
           'LEFT JOIN usage_rent_show AS URS ON URS.idShow = S.idShow ' .
           'LEFT JOIN  `usage` AS U ON U.idUsage = URS.idUsage ' .
           'LEFT JOIN  HALL AS H ON U.idHall = H.idHall ' .
           'LEFT JOIN show_type AS ST ON ST.idShowType = S.showType ' .
           "LEFT JOIN  play AS P ON P.idPlay = S.idPlay WHERE MONTH(U.timeFrom) = $month AND YEAR(U.timeFrom ) = $year";
       $sql .= ' ORDER BY  U.timeFrom ASC LIMIT 10';
       $result = $this->connection->fetchAll($sql);
       if ($result === null) {
           return null;
       }
       $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
       return unserialize(serialize($res),['allowed_classes => true']);
   }


    /**
     * @return array
     * @throws ReflectionException
     */
    public  function getShowsLimited(): array{
        $result= $this->connection->fetchAll('SELECT S.idShow, U.timeFrom, U.timeTo, P.author as play_author, P.name as play_name, P.description as play_description, P.duration as play_duration, P.idPlay as play_idPlay, P.image as play_image FROM `show` AS S '.
            'LEFT JOIN PLAY AS P ON S.idPlay = P.idPlay '.
            'LEFT JOIN usage_rent_show AS URS ON URS.idShow = S.idShow '.
            'LEFT JOIN `usage` AS U ON U.idUsage = URS.idUsage '.
            'WHERE U.timeFrom > NOW() ORDER BY U.timeFrom ASC LIMIT ?', 8);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }


    /**
     * @param int $idShow
     * @return Show
     * @return Show
     * @throws ReflectionException
     */
   public  function getShowDetail(int $idShow) :Show {
        $result = $this->connection->fetch('SELECT S.idShow, U.timeFrom, U.timeTo, H.name as hall_name, H.capacity as hall_capacity, ST.name  as showType, P.author as play_author, P.name as play_name, P.description as play_description, P.duration as play_duration, P.idPlay as play_idPlay,  P.image as play_image FROM `show` AS S ' .
            'LEFT JOIN usage_rent_show AS URS ON URS.idShow = S.idShow ' .
            'LEFT JOIN  `usage` AS U ON U.idUsage = URS.idUsage ' .
            'LEFT JOIN  HALL AS H ON U.idHall = H.idHall ' .
            'LEFT JOIN show_type AS ST ON ST.idShowType = S.showType ' .
            'LEFT JOIN  play AS P ON P.idPlay = S.idPlay WHERE S.idShow = ?', $idShow);
       if ($result === null) {
           return null;
       }
       $res = EntityBuilder::createEntityFromDatabaseResult($result, __CLASS__);
       return unserialize(serialize($res),['allowed_classes => true']);
   }

}