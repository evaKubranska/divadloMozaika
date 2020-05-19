<?php
namespace App\Model;
use DateTime;
use Nette\Database\Connection;


class Usage extends AEntity {

    /** @var int  */
    public ?int $idUsage = null;
    /** @var Hall  */
    public ?Hall $hall =  null;
    /** @var DateTime  */
    public ?DateTime $timeFrom = null;
    /** @var DateTime  */
    public ?DateTime $timeTo = null;

    public function __construct(?Connection $connection = null) {
        parent::__construct($connection);
    }

}