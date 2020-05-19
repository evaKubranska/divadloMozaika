<?php
namespace App\Presenters;

use App\Controls\SearchNavbar;
use App\Model\Show;
use Nette\Application\AbortException;
use Nette\Database\Connection;
use Nette\Utils\DateTime;

class SearchPlayResultsPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;

    /**
     * @param String|null $name
     * @param String|null $date
     * @throws \ReflectionException
     */
    public function renderDefault (String $name = null, String $date = null ): void {
        $dateTime =isset($date) ?  DateTime::createFromFormat('d.m.Y', $date): DateTime::from(0);
        $show = new Show($this->connection);
   //     var_dump($show->getAllShow($name, $dateTime)); exit();
        $this->template->result = $show->getAllShow($name, $dateTime);
    }

    /**
     * @return SearchNavbar
     */
    public function createComponentTopBar() : SearchNavbar{
        return new SearchNavbar();
    }

    /**
     * @param $id
     * @throws AbortException
     */
    public function handleDetail($id): void {
        if (isset($id)) {
              $this->redirect('ShowDetail:Default', (int)$id);
        }
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