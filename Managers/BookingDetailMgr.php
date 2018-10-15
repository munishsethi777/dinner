<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/SessionUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/BookingDetail.php");
class BookingDetailMgr{
	private static  $bookingDetailMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$bookingDetailMgr)
		{
			self::$bookingDetailMgr = new BookingDetailMgr();
			self::$dataStore = new BeanDataStore(BookingDetail::$className, BookingDetail::$tableName);
			self::$sessionUtil = SessionUtil::getInstance();
		}
		return self::$bookingDetailMgr;
	}
	
	public function saveBookingDetails($bookingId, $menuDetails){
		foreach($menuDetails as $key=>$value){
			if($value == null || $value == 0){
				continue;
			}
			$bookingDetail = new BookingDetail();
			$bookingDetail->setBookingSeq($bookingId);
			$bookingDetail->setMenuSeq($key);
			$bookingDetail->setMembers($value);
			self::$dataStore->save($bookingDetail);
		}
	}
	
	public function saveBookingDetail($bookingId,$menuAndMembers){
		foreach ($menuAndMembers as $selectedSeat){
			$selectedSeatArr = explode("_", $selectedSeat);
			$menuSeq = $selectedSeatArr[0];
			$members = $selectedSeatArr[1];
			$bookingDetail = new BookingDetail();
			$bookingDetail->setBookingSeq($bookingId);
			$bookingDetail->setMembers($members);
			$bookingDetail->setMenuSeq($menuSeq);
			$id = self::$dataStore->save($bookingDetail);
		}
		return $id;
	}
	
	public function deleteBookingDetailInList($bookingSeqs){
		$query = "delete from bookingdetails where bookingseq in ($bookingSeqs)";
		self::$dataStore->executeQuery($query);
	}

}