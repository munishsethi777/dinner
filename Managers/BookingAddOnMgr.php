<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/BookingAddOn.php");
class BookingAddOnMgr{
	private static  $bookingAddOnMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$bookingAddOnMgr)
		{
			self::$bookingAddOnMgr = new BookingAddOnMgr();
			self::$dataStore = new BeanDataStore(BookingAddOn::$className, BookingAddOn::$tableName);
		}
		return self::$bookingAddOnMgr;
	}
	
	public function saveBookingAddOn($bookingAddOns){
		$id = self::$dataStore->save($bookingAddOns);
		return $id;
	}
	
	public function findByBookingSeq($bookingSeq){
		$colVal["bookingseq"] = $bookingSeq;
		$bookingAddOn = self::$dataStore->executeConditionQuery($colVal);
		if(!empty($bookingAddOn)){
			return $bookingAddOn[0];
		}
		return null;
	}
	
	public function FindArrByBookingSeq($bookingSeq){
		$query = "select * from bookingaddons where bookingseq = $bookingSeq";
		$bookingAddOn = self::$dataStore->executeQuery($query);
		if(!empty($bookingAddOn)){
			return $bookingAddOn[0];
		}
		return null;
	}
	public function deleteBookingAddOnInList($bookingSeqs){
		$query = "delete from bookingaddons where bookingseq in ($bookingSeqs)";
		self::$dataStore->executeQuery($query);
	}
	
	public function deleteByBookingSeq($bookingSeq){
		$colVal["bookingseq"] = $bookingSeq;
		$flag = self::$dataStore->deleteByAttribute($colVal);
	}
}