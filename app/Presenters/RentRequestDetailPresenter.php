<?php
declare(strict_types=1);
namespace App\Presenters;
use App\Model\Rent;
use App\Model\RentContext;
use Nette\Application\AbortException;
use Nette\Database\Connection;


class RentRequestDetailPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;
    public  Rent $rent;
    public RentContext $rentContext;

    public function startup() :void {
        parent::startup(); // TODO: Change the autogenerated stub
        $this->rentContext = new RentContext();
        $this->rent = new Rent($this->connection, null, $this->rentContext);
    }

    public function renderDefault (int $idRent ) : void {
       $this->template->res = $this->rent->getRentDetail($idRent);
    }

    /**
     * @param $idRent
     * @throws AbortException
     */
    public function handleConfirm($idRent): void {
        if(isset($idRent)) {
            $this->rent->approveRent((int)$idRent);
            $this->flashMessage('Žiadosť bola schválená, zákazník bol informovaný emialom ', 'info');
            $this->redirect(':EmployeeHomepage:default');
        }
    }

    /**
     * @param $idRent
     * @throws AbortException
     * @throws \ReflectionException
     */
    public function handleCancel($idRent): void {
        if(isset($idRent)) {
            $this->rent->rejectRent((int)$idRent);
                $this->flashMessage('Žiadosť bola zamietnutá, zákazník bol informovaný emialom ', 'info');
                $this->redirect(':EmployeeHomepage:default');
            }
    }


}