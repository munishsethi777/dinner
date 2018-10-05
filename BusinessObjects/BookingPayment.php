<?php
class BookingPayment{
	public static $tableName = "bookingpayment";
	public static $className = "BookingPayment";
	private $seq,$bookingseq,$paidon,$transactionid;
	
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
	
	public function setPaidOn($paidOn_){
		$this->paidon = $paidOn_;
	}
	public function getPaidOn(){
		return $this->paidon;
	}
	
	public function setTransactionId($txnId_){
		$this->transactionid = $txnId_;
	}
	public function getTransactionId(){
		return $this->transactionid;
	}
}