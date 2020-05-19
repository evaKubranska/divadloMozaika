<?php
declare(strict_types=1);
namespace App\Presenters;
use App\Model\Rent;
use Nette\Application\AbortException;
use Nette\Database\Connection;

class RentRequestsPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;

    public function renderDefault(): void {
        $rent = new Rent($this->connection);
        $this->template->rent = $rent->getAllReserveRent(1);
    }

    /**
     * @param $idRent
     * @throws AbortException
     */
    public function handleSelect($idRent): void {
        if(isset($idRent)) {
            $this->redirect('RentRequestDetail:Default', (int)$idRent);
        }
    }
}