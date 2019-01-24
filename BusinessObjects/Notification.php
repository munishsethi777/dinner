<?php
class Notification{
	public static $tableName = "notifications";
	public static $className = "Notification";
	private $seq,$senton,$timeslotseq,$emailid,$mobileno,$emailerrordetail,$smserrordetail;
	public function setSeq($seq_){
		$this->seq = $seq_;
	}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setSentOn($sentOn_){
		$this->senton = $sentOn_;
	}
	public function getSentOn(){
		return $this->senton;
	}
	
	public function setTimeSlotSeq($timeSlotSeq_){
		$this->timeslotseq = $timeSlotSeq_;
	}
	public function getTimeSlotSeq(){
		return $this->timeslotseq;
	}
	
	public function setEmailId($emailId_){
		$this->emailid = $emailId_;
	}
	public function getEmailId(){
		return $this->emailid;
	}
	
	public function setMobileNo($mobile_){
		$this->mobileno = $mobile_;
	}
	public function getMobileNo(){
		return $this->mobileno;
	}
	
	public function setEmailErrorDetail($emailError_){
		$this->emailerrordetail = $emailError_;
	}
	public function getEmailErrorDetail(){
		return $this->emailerrordetail;
	}
	
	public function setSmsErrorDetail($mobileError_){
		$this->smserrordetail=$mobileError_;
	}
	public function getSmsErrorDetail(){
		return $this->smserrordetail;
	}
		
}