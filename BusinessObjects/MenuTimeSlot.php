<?php
class MenuTimeSlot{
	public static $tableName = "menutimeslots";
	public static $className = "MenuTimeSlot";
	private $seq,$menuseq,$timeslotsseq;
	
	public function setSeq($seq){
		$this->seq = $seq;
	}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setMenuSeq($menuSeq_){
		$this->menuseq = $menuSeq_;
	}
	public function getMenuSeq(){
		return $this->menuseq;
	}
	
	public function setTimeSlotsSeq($tSeq_){
		$this->timeslotsseq = $tSeq_;
	}
	public function getTimeSlotsSeq(){
		return $this->timeslotsseq;
	}
}