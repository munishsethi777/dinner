<?php
class SlotDetail{
	private $seq,$slotseq,$date,$action;
	public static $tableName = "slotdetails";
	public static $className = "SlotDetail";
	public function setSeq($seq_){
		$this->seq = $seq_;
	}
	public function getSeq(){
		return $this->seq;
	}
	public function setSlotSeq($slotSeq_){
		$this->slotseq =$slotSeq_;
	}
	public function getSlotSeq(){
		return $this->slotseq;
	}
	
	public function setDate($date_){
		$this->date = $date_;
	}
	public function getDate(){
		return $this->date;
	}
	
	public function setAction($action_){
		$this->action = $action_;
	}
	public function getAction(){
		return $this->action;
	}
}