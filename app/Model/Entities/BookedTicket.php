<?php


namespace App\Model;


use Exception;
use Mpdf\MpdfException;
use Nette\Database\Connection;
use Nette\Utils\DateTime;
use ReflectionException;

class BookedTicket extends AEntity {
    /** @var int  */
    public ?int $idBookedTicket = null;
   /** @var Customer  */
    public ?Customer $customer = null;
    /** @var String  */
    public ?String $createDate = null;
    /** @var float  */
    public ?float $totalSum = null;
    /** @var array  */
    public ?array $ticket = null;
    /** @var int  */
    public  ?int $ticketStatus = null;
    private ?BookedTicketContext $bookedTicketContext;

    public  function __construct( Connection $connection = null,?BookedTicketContext $context = null) {
        parent::__construct($connection);
        $this->bookedTicketContext = $context;
    }

    /**
     * @param int $idShow
     * @return array
     * @throws ReflectionException
     */
    public function getAllBookedTicketById(int $idShow):array {
        $result = $this->connection->fetchAll('SELECT BT.idBookedTicket as idBookedTicket, C.idCustomer as customer_idCustomer, C.firstName as customer_firstName, C.lastName as customer_lastName, C.email as customer_email, ticketStatus, createDate as createDate, totalSum as totalSum FROM BOOKED_TICKET AS BT ' .
            '  LEFT JOIN TICKET_BOOKING AS TB ON TB.idBookedTicket = BT.idBookedTicket   '.
            'LEFT JOIN TICKET AS T ON T.idTicket = TB.idTicket ' .
            ' LEFT JOIN CUSTOMER AS C ON C.idCustomer = BT.idCustomer WHERE T.idShow = ?', $idShow);

        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param int $idBookedTicket
     * @param string $firstName
     * @param string $lastName
     * @param int $bookedTicketStatus
     * @return array
     * @throws ReflectionException
     */
    public function getAllBookedTicket(int $idBookedTicket, string $firstName, string $lastName, int $bookedTicketStatus):array {
            $query = 'SELECT BT.idBookedTicket as idBookedTicket, C.firstName as customer_firstName, C.lastName as customer_lastName FROM BOOKED_TICKET  AS BT ' .
                '  LEFT JOIN TICKET_BOOKING AS TB ON TB.idBookedTicket = BT.idBookedTicket   '.
                ' LEFT JOIN TICKET AS T ON TB.idTicket = T.idTicket ' .
                'LEFT JOIN `show` AS S ON S.idShow = T.idShow   ' .
                'LEFT JOIN PLAY AS P ON S.idPlay = P.idPlay ' .
                'LEFT JOIN CUSTOMER AS C ON C.idCustomer = BT.idCustomer WHERE ';

            $query .= empty($idBookedTicket) ? '' : 'BT.idBookedTicket = ' .$idBookedTicket. ' AND ';
            $query .= empty($firstName) ? '' :  'C.firstName LIKE "' .$firstName. '" AND ';
            $query .= empty($lastName) ? '' : 'C.lastName LIKE "' .$lastName. '" AND ';
            $query .= 'BT.ticketStatus = ' .$bookedTicketStatus. ' GROUP BY BT.idBookedTicket';
            $result =  $this->connection->fetchAll($query);
            $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
            return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param int $idBookedTicket
     * @return BookedTicket
     * @throws ReflectionException
     */
    public function getBookedTicketDetail(int $idBookedTicket) :BookedTicket {
        $result = $this->connection->fetch('SELECT BT.idBookedTicket as idBookedTicket,BT.ticketStatus as ticketStatus, BT.createDate as createDate, '.
            ' BT.totalSum as totalSum, C.firstName as customer_firstName, C.lastName as customer_lastName,   C.phone as customer_phone, C.email as customer_email, C.street as customer_street, '.
            ' C.houseNumber as customer_houseNumber, C.city as customer_city, C.zip as customer_zip '.
            'FROM BOOKED_TICKET AS BT  LEFT JOIN CUSTOMER AS C ON C.idCustomer = BT.idCustomer '.
            'WHERE BT.idBookedTicket = ?',$idBookedTicket);
        if($result === null) {
            return  null;
        }
        $res = EntityBuilder::createEntityFromDatabaseResult($result, __CLASS__);
        $array = unserialize(serialize($res),['allowed_classes => true']);
        $ticket = new Ticket($this->connection);
        $ticketArray = $ticket->getTicketDetailByBookedTicket($idBookedTicket);
        $array->ticket = $ticketArray;
        return $array;
    }

    /**
     * @param int $idBookedTicket
     * @param DocumentGenerator $documentGenerator
     * @return String
     * @return String|null
     * @throws ReflectionException
     * @throws MpdfException
     */
    public  function buyTicket(int $idBookedTicket, DocumentGenerator $documentGenerator): ?String {
    $document = null;
    $res = $this->connection->fetch('SELECT ticketStatus FROM BOOKED_TICKET WHERE idBookedTicket = ?', $idBookedTicket);
    if ($res === null) {
        return null;
    }
    $currentStatus = (int)$res->offsetGet('ticketStatus');
    if ($currentStatus === 2) {
        $this->bookedTicketContext->setBookedTicketStatus($this->bookedTicketContext->getPurchasedState());
    }
    $success = true;
    try {
        $this->bookedTicketContext->purchaseBookedTicketStatus();
    } catch (Exception $e) {
        $success = false;
    }
    if($success) {
        $id = $this->bookedTicketContext->returnBookedTicketStatusId();
        $this->connection->query('UPDATE BOOKED_TICKET SET ticketStatus = ? WHERE idBookedTicket = ?',$id,$idBookedTicket );
        $document = $documentGenerator->generateBuyTicketDocumentation($this->getBookedTicketDetail($idBookedTicket));
    }
    return $document;
}

    /**
     * @param $idBookedTicket
     * @param DocumentGenerator $documentGenerator
     * @return String
     * @throws ReflectionException
     * @throws MpdfException
     */
    public  function cancelBookedTicket($idBookedTicket,  DocumentGenerator $documentGenerator): ?String {
        $document = null;
        $res = $this->connection->fetch('SELECT ticketStatus FROM BOOKED_TICKET WHERE idBookedTicket = ?', $idBookedTicket);
        if ($res === null) {
            return null;
        }
        $currentStatus = (int)$res->offsetGet('ticketStatus');
        if ($currentStatus === 2) {
           $this->bookedTicketContext->setBookedTicketStatus($this->bookedTicketContext->getPurchasedState());
        } elseif ($currentStatus === 3) {
            $this->bookedTicketContext->setBookedTicketStatus($this->bookedTicketContext->getCancelledState());
        }
        $success = true;
        try {
            $this->bookedTicketContext->cancelBookedTicketStatus();
        } catch (Exception $e) {
            $success = false;
        }
        if($success) {
            $id = $this->bookedTicketContext->returnBookedTicketStatusId();
            $this->connection->query('UPDATE BOOKED_TICKET SET ticketStatus = ? WHERE idBookedTicket = ?',$id,$idBookedTicket );
            $document = $documentGenerator->generateCancelTicketDocumentation($this->getBookedTicketDetail($idBookedTicket));
        }
        return $document;
    }

    public function deleteTicket(int $idBookedTicket) :void {
        $this->connection->query('DELETE FROM ticket_booking WHERE idBookedTicket = ?', $idBookedTicket);
        $this->connection->query('DELETE FROM BOOKED_TICKET WHERE idBookedTicket = ?', $idBookedTicket);
    }
    /**
     * @param array $ticketsId
     * @param int $idCustomer
     * @throws ReflectionException
     */
    public  function reserveBookedTicket(array $ticketsId, int $idCustomer): void {
        $ticket = new Ticket($this->connection);
        $this->totalSum = $ticket->getTotalSum($ticketsId);
        $this->createDate = DateTime::from(0);
        $ticketStatusId = $this->bookedTicketContext->returnBookedTicketStatusId();
        $this->connection->query('INSERT INTO `BOOKED_TICKET` ( `idCustomer`, `createDate`,`ticketStatus`, `totalSum`) VALUES (?,?,?,?);',$idCustomer, $this->createDate, $ticketStatusId, $this->totalSum);
        $this->idBookedTicket =$this->connection->getInsertId();
        foreach ($ticketsId as $ticket) {
            $this->connection->query('INSERT INTO `TICKET_BOOKING` ( `idTicket`, `idBookedTicket`) VALUES (?,?)', $ticket, $this->idBookedTicket );
        }
        $customer = new Customer($this->connection);
        $customer = $customer->getCustomerDetail($idCustomer);
        TicketMailSender::sendReservationTicketMail($customer);
    }
}