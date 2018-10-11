<?php
class Booking{
	public static $tableName = "bookings";
	public static $className = "Booking";
	
	private $seq,$bookedon,$fullname,$mobilenumber,$emailid,$gstnumber,$timeslot,$bookingdate,$transactionid,$amount;
	
	public function setSeq($seq_){
		$this->seq = $seq_;
	}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setBookedOn($bookedOn_){
		$this->bookedon = $bookedOn_;
	}
	
	public function getBookedOn(){
		return $this->bookedon;
	}
	
	public function setFullName($fullName_){
		$this->fullname = $fullName_;
	}
	public function getFullName(){
		return $this->fullname;
	}
	
	public function setMobileNumber($mobileNumber_){
		$this->mobilenumber = $mobileNumber_;
	}
	public function getMobileNumber(){
		return $this->mobilenumber;
	}
	
	public function setEmailId($emailId_){
		$this->emailid = $emailId_;
	}
	public function getEmailId(){
		return $this->emailid;
	}
	
	public function setGSTNumber($gstNumber_){
		$this->gstnumber = $gstNumber_;
	}
	
	public function getGSTNumber(){
		return $this->gstnumber;
	}
	
	public function setTimeSlot($timeSlot_){
		$this->timeslot = $timeSlot_;
	}
	public function getTimeSlot(){
		return $this->timeslot;
	}
	public function setBookingDate($bookingDate){
		$this->bookingdate = $bookingDate;
	}
	
	public function getBookingDate(){
		return $this->bookingdate;
	}
	
	public function setTransactionId($transactionId_){
		$this->transactionid = $transactionId_;
	}
	public function getTransactionId(){
		return $this->transactionid;
	}
	
	public function setAmount($amount_){
		$this->amount = $amount_;
	}
	public function getAmount(){
		return $this->amount;
	}
}