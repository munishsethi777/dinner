<?php
class TimeSlot{
	public static $tableName = "timeslots";
	public static $className = "TimeSlot";
	private $seq,$title,$seats,$time,$description;
	
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
}