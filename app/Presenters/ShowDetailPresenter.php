<?php


namespace App\Presenters;


use App\Controls\SearchNavbar;
use App\Model\Show;
use Nette\Application\AbortException;
use Nette\Database\Connection;
use ReflectionException;

class ShowDetailPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;

    /**
     * @param int $idShow
     * @throws ReflectionException
     */
    public function renderDefault(int $idShow):void {
        $show = new Show($this->connection);
        $this->template->show = $show->getShowDetail($idShow);
    }
    public function createComponentTopBar() : SearchNavbar{
        return new SearchNavbar();
    }

    /**
     * @param $id
     * @throws AbortException
     */
    public function handleBuy($id): void {
            if (isset($id)) {
                $this->redirect('ReserveTicket:Default', (int)$id);
            }
    }
}