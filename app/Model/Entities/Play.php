<?php

namespace App\Model;
use Nette\Database\Connection;
use ReflectionException;

class Play extends AEntity {
    /** @var int  */
    public ?int $idPlay = null;
    /** @var String  */
    public ?String $name = null;
    /** @var String  */
    public ?String $description = null;
    /** @var int  */
    public ?int $duration = null;
    /** @var String  */
    public ?String $author = null;
    /** @var String  */
    public ?String $image = null;

    public function __construct(?Connection $connection = null) {
        parent::__construct($connection);
    }

    public function createPlay(String $name, String $author, int $duration, String $description, String $image):void {
        $this->connection->query('INSERT INTO PLAY (author, name, description, duration, image) VALUES(?,?,?,?, ?)', $author, $name, $description, $duration, $image);
    }

    public  function getAllPlays(): array{
       return $this->connection->fetchPairs('SELECT idPlay, name FROM PLAY');
    }

    /**
     * @param String $name
     * @return array
     * @throws ReflectionException
     */
    public function getAllPlayByName(String $name):array {
        $name = '%' .strtolower($name). '%';
        $result= $this->connection->fetchAll('SELECT * FROM PLAY where name like LOWER(?)',$name);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityCollectionFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    /**
     * @param int $idPlay
     * @return mixed
     * @throws ReflectionException
     */
    public function  getPlayDetail(int $idPlay):Play {
        $result= $this->connection->fetch('SELECT idPlay as idPlay, author as author, description as description, image as image, name as name, duration as duration  FROM PLAY where idPlay = ? ',$idPlay);
        if ($result === null) {
            return null;
        }
        $res = EntityBuilder::createEntityFromDatabaseResult($result, __CLASS__);
        return unserialize(serialize($res),['allowed_classes => true']);
    }

    public function modifyPlay(int $idPlay, $name, string $author, int $duration, $description):void{
        $this->connection->query('UPDATE PLAY SET author = ?, name = ?, description = ?, duration = ? WHERE idPlay = ? ',$author, $name, $description,$duration, $idPlay );
    }

    public function modifyImage(String $image, int $idPlay):void{
        $this->connection->query('UPDATE PLAY SET image = ? WHERE idPlay = ? ',$image,$idPlay);
    }
}