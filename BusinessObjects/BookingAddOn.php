<?php
class BookingAddOn{
	public static $tableName = "bookingaddons";
	public static $className = "BookingAddOn";
	private $seq,$bookingseq,$addontype,$notes,$price;
	
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
	
	public function setAddOnType($addOnType_){
		$this->addontype = $addOnType_;
	}
	public function getAddOnType(){
		return $this->addontype;
	}
	
	public function setNotes($notes_){
		$this->notes = $notes_;
	}
	public function getNotes(){
		return $this->notes;
	}
	
	public function setPrice($price_){
		$this->price = $price_;
	}
	public function getPrice(){
		return $this->price;
	}
}