<?php
class BookingDetail{
	public static $tableName = "bookingdetails";
	public static $className = "BookingDetail";
	private $seq,$bookingseq,$menuseq,$members;
	public function setSeq($seq_){
		$this->seq = $seq_;
	}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setBookingSeq($bookingSeq_){
		$this->bookingseq = $bookingSeq_;
	}
	public function getBookingSeq(){
		return $this->bookingseq;
	}
	
	public function setMenuSeq($menuSeq_){
		$this->menuseq = $menuSeq_;
	}
	public function getMenuSeq(){
		return $this->menuseq;
	}
	
	public function setMembers($members_){
		$this->members = $members_;
	}
	public function getMembers(){
		return $this->members;
	}
	
	
}