<?php
namespace App\Model;

use Nette\Database\Connection;

abstract class AEntity {
    protected ?Connection $connection;

    public  function __construct(?Connection $connection = null) {
        $this->connection = $connection;
    }
}