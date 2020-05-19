<?php


namespace App\Model;


interface RentStatus {

    public function approveRentRequest():void;
    public function rejectRentRequest():void;
    public function returnRequestId():int;

}