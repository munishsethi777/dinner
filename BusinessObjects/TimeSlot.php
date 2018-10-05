<?php
class TimeSlot{
	public static $tableName = "timeSlots";
	public static $className = "TimeSlot";
	private $seq,$title,$seats;
	
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
		
}