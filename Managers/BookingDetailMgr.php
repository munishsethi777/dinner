<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
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
		}
		return self::$bookingDetailMgr;
	}
	
	public function saveBookingDetails($bookingId, $menuDetails,$menuPriceArr){
		foreach($menuDetails as $key=>$value){
			if($value == null || $value == 0){
				continue;
			}
			$bookingDetail = new BookingDetail();
			$bookingDetail->setBookingSeq($bookingId);
			$bookingDetail->setMenuSeq($key);
			$bookingDetail->setMembers($value);
			$bookingDetail->setMenuPrice($menuPriceArr->$key);
			self::$dataStore->save($bookingDetail);
		}
	}
	
	public function saveBookingDetail($bookingId,$menuAndMembers,$amount){
		$this->deleteBookingDetailInList($bookingId);
		foreach ($menuAndMembers as $key=>$selectedSeat){
			if($selectedSeat > 0){
				$selectedSeatArr = explode("_", $selectedSeat);
				$menuSeq = $selectedSeatArr[0];
				$members = $selectedSeatArr[1];
				$bookingDetail = new BookingDetail();
				$bookingDetail->setBookingSeq($bookingId);
				$bookingDetail->setMembers($members);
				$bookingDetail->setMenuSeq($menuSeq);
				$menuPrice = $amount[$key] / $members;
				$bookingDetail->setMenuPrice($menuPrice);
				$id = self::$dataStore->save($bookingDetail);
			}
		}
		return $id;
	}
	
	public function deleteBookingDetailInList($bookingSeqs){
		$query = "delete from bookingdetails where bookingseq in ($bookingSeqs)";
		self::$dataStore->executeQuery($query);
	}
	
	public function getAllBookingDetailsAndMenu(){
		$query = "select * from bookingdetails inner join menus on bookingdetails.menuseq = menus.seq";
		$bookingDetails = self::$dataStore->executeQuery($query);
		$bookingDetailArr = array();
		foreach ($bookingDetails as $bookingDetail){
			$menuStrArr = "";
			$bookingSeq = $bookingDetail["bookingseq"];
			$members = $bookingDetail["members"];
			$menuTitle = $bookingDetail["title"];
			if(array_key_exists($bookingSeq, $bookingDetailArr)){
				$menuStrArr = $bookingDetailArr[$bookingSeq];
			}
			if(!empty($menuStrArr)){
 				$bookingDetailArr[$bookingSeq] = $menuStrArr . " , " . $members . " - " . $menuTitle;
 			}else{
 				$bookingDetailArr[$bookingSeq] = $members . " - " . $menuTitle;
 			}
 		}
 		return $bookingDetailArr;
	}

	
	public function getDetailByBookingSeqAndTimeSlot($bookingSeq,$timeSlot){
		$query = "SELECT bookingdetails.menuprice,bookingdetails.menuseq,bookingdetails.members FROM `bookingdetails` inner join menutimeslots on bookingdetails.menuseq = menutimeslots.menuseq
where bookingseq = $bookingSeq and menutimeslots.timeslotsseq = $timeSlot";
		$bookingDetail = self::$dataStore->executeQuery($query);
		return $bookingDetail;
	}
	
}