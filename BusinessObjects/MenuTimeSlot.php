<?php
class MenuTimeSlot{
	public static $tableName = "menutimeslots";
	public static $className = "MenuTimeSlot";
	private $seq,$menuseq,$timeslotseq;
	
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
	
	public function setTimeSlotSeq($tSeq_){
		$this->timeslotseq = $tSeq_;
	}
	public function getTimeSlotSeq(){
		return $this->timeslotseq;
	}
}