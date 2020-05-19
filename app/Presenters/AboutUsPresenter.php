<?php


namespace App\Presenters;


use App\Controls\SearchNavbar;

class AboutUsPresenter extends BasePresenter {

    public function createComponentTopBar() : SearchNavbar{
        return new SearchNavbar();
    }
}