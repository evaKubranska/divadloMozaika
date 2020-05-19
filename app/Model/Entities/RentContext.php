<?php


namespace App\Model;


class RentContext {
    private RentStatus $pendingEvaluation;
    private  RentStatus $approved;
    private RentStatus $rejected;
    private RentStatus $rentStatus;

    public function __construct() {
       $this->pendingEvaluation = new PendingEvaluation($this);
       $this->approved = new Approved($this);
       $this->rejected = new Rejected($this);
       $this->rentStatus = $this->pendingEvaluation;
    }

    /**
     * @return RentStatus
     */
    public function getRentStatus(): RentStatus
    {
        return $this->rentStatus;
    }

    /**
     * @param RentStatus $rentStatus
     */
    public function setRentStatus(RentStatus $rentStatus): void
    {
        $this->rentStatus = $rentStatus;
    }

    public function approveRentRequest():void {
        $this->rentStatus->approveRentRequest($this);
    }

    public function rejectRentRequest():void{
        $this->rentStatus->rejectRentRequest($this);
    }

    public function returnRequestId() :int {
        return $this->rentStatus->returnRequestId();
    }
    public function getPendingEvaluationStatus():RentStatus{
        return $this->pendingEvaluation;
    }
    public function getApprovedStatus():RentStatus{
        return $this->approved;
    }
    public function getRejectedStatus():RentStatus{
        return $this->rejected;
    }
}