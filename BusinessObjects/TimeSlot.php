<?php
class TimeSlot{
	public static $tableName = "timeslots";
	public static $className = "TimeSlot";
	private $seq,$title,$seats,$time,$description,$starton,$endon,$bookingavailabletill;
	
	public function setSeq($seq_){
			$this->seq = $seq_;
		}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setTitle($title_){
		$this->title = $title_;
	}
	public function getTitle(){
		return $this->title;
	}
	
	public function setSeats($seats_){
		$this->seats = $seats_;
	}
	public function getSeats(){
		return $this->seats;
	}
	
	public function setTime($time_){
		$this->$time = $time_;
	}
	public function getTime(){
		return $this->time;
	}
	
	public function setDescription($description_){
		$this->description = $description_;
	}
	public function getDescription(){
		return $this->description;
	}	
	
	public function setStartOn($startOn_){
		$this->starton = $startOn_;
	}
	public function getStartOn(){
		return $this->starton;
	}
	
	public function setEndOn($endOn_){
		$this->endon = $endOn_;
	}
	public function getEndOn(){
		return $this->endon;
	}
	
	public function setBookingAvailableTill($bookingTill_){
		$this->bookingavailabletill = $bookingTill_;
	}
	public function getBookingAvailableTill(){
		return $this->bookingavailabletill;
	}
	
}