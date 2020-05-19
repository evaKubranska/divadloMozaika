<?php


namespace App\Presenters;


use App\Controls\SearchNavbar;
use App\Model\Show;
use Nette\Application\AbortException;
use Nette\Database\Connection;

class CustomerHomepagePresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;

    public function renderDefault(): void {
        $show = new Show($this->connection);
        $this->template->res = $show->getShowsLimited();
    }

    public function createComponentTopBar() : SearchNavbar{
        return new SearchNavbar();
    }

    /**
     * @param $id
     * @throws AbortException
     */
    public function handleDetail($id): void {
        if(isset($id)){
            $this->redirect('ShowDetail:Default', (int)$id);
        }
    }


}