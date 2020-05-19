<?php


namespace App\Model;


use Nette\Database\Connection;
use ReflectionException;

class Customer extends AEntity {
    /** @var int  */
    public ?int $idCustomer=null;
    /** @var String  */
    public ?String  $firstName=null;
    /** @var String  */
    public ?String $lastName=null;
    /** @var String  */
    public ?String $email=null;
    /** @var String  */
    public ?String $phone=null;
    /** @var String  */
    public ?String $street=null;
    /** @var String  */
    public ?String $houseNumber=null;
    /** @var String  */
    public ?String $zip=null;
    /** @var String  */
    public ?String $city=null;

    public  function __construct( Connection $connection = null) {
        parent::__construct($connection);
    }

    public function createCustomer(String $firstname, String $lastname, String $email, String $phone, String $street, String $houseNumber, String $zip, String $city): int {
        $this->connection->query('INSERT INTO `customer` ( `firstName`, `lastName`, `street`, `houseNumber`, `zip`, `city`, `phone`, `email`) VALUES (?,?,?,?,?,?,?,?)',$firstname, $lastname, $street, $houseNumber, $zip, $city, $phone, $email);
        return  $this->connection->getInsertId();
    }

    /**
     * @param int $idCustomer
     * @return Customer
     * @throws ReflectionException
     */
    public  function  getCustomerDetail(int $idCustomer) :Customer {
        $result = $this->connection->fetch('SELECT * FROM CUSTOMER WHERE idCustomer = ?',$idCustomer);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }
}
