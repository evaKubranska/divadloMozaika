<?php


namespace App\Presenters;


use App\Controls\SearchNavbar;
use App\Model\Show;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Database\Connection;
use Nette\Utils\DateTime;

class ShowProgramPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;
    private int $month;

    public function __construct() {
        parent::__construct();
        $this->month = (int)DateTime::from(0)->format('n');
    }

    /***
     * @throws \ReflectionException
     */
    public function renderDefault (): void {
        $show = new Show($this->connection);
        $today = DateTime::from(0);
        $months = array(1 => 'Jan.', 2 => 'Feb.', 3 => 'Mar.', 4 => 'Apr.', 5 => 'Máj', 6 => 'Jún', 7 => 'Júl', 8 => 'Aug.', 9 => 'Sep.', 10 => 'Okt.', 11 => 'Nov.', 12 => 'Dec.');
        $transposed = array_slice($months, $today->format('n') - 1, 12, true) + array_slice($months, 0, $today->format('n') - 1, true);
        $this->template->month = $this->month;
        $this->template->months = $transposed;
        $this->template->result = $show->getAllFilteredShow($this->month);
    }

    public function createComponentTopBar() : SearchNavbar {
        return new SearchNavbar();
    }

    /**
     * @param $month
     */
    public function handleSelect($month): void {
       $this->month = $month;
       $this->redrawControl('programTable');
    }

    /**
     * @param $id
     * @throws AbortExceptionAlias
     */
    public function handleDetail($id): void {
        if (isset($id)) {
            $this->redirect('ShowDetail:Default', (int)$id);
        }
    }

    /**
     * @param $id
     * @throws AbortExceptionAlias
     */
    public function handleBuy($id): void {
        if (isset($id)) {
            $this->redirect('ReserveTicket:Default', (int)$id);
        }
    }
}