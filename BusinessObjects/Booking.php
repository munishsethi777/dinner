<?php
class Booking{
	public static $tableName = "bookings";
	public static $className = "Booking";
	
	private $seq,$bookedon,$fullname,$mobilenumber,$emailid,$gstnumber,$menu,$timeslot;
	
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
	
	public function setFullName($fullName){
		$this->fullname = $fullName;
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
	
	public function setMenu($menu_){
		$this->menu = $menu_;
	}
	public function getMenu(){
		return $this->menu;
	}
	
	public function setTimeSlot($timeSlot_){
		$this->timeslot = $timeSlot_;
	}
	public function getTimeSlot(){
		return $this->timeslot;
	}
	
	
}