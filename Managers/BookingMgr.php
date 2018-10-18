<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Booking.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/BookingDetail.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingDetailMgr.php");
class BookingMgr{
	private static  $bookingMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$bookingMgr)
		{
			self::$bookingMgr = new BookingMgr();
			self::$dataStore = new BeanDataStore(Booking::$className, Booking::$tableName);
		}
		return self::$bookingMgr;
	}
	
	public function getAvailableSeats($date,$timeSlots){
		$query = "SELECT sum(bookingdetails.members) as totalcount from bookings inner JOIN bookingdetails on bookings.seq = bookingdetails.bookingseq
where bookingdate = '$date' and timeslot = $timeSlots";
		return self::$dataStore->executeCountQueryWithSql($query);
	}
	
	
	public function saveBooking($bookingObj){
		$id = self::$dataStore->save($bookingObj);	
		return $id;
	}
	
	public function getBookingJsonForGrid(){
		$query = "select bookings.emailid as emailid,bookings.mobilenumber as mobilenumber,bookings.seq as bookingseq,bookings.bookedon as bookedon,bookings.bookingdate as bookingdate,bookings.transactionid as transactionid, bookings.fullname as fullname,timeslots.title as timeslot from bookings inner join timeslots on bookings.timeslot = timeslots.seq";
		$bookingDetails =  self::$dataStore->executeQuery($query,true);
		$bookingArr = array();
		$bookingMainArr = array();
		$bookingDetailMgr = BookingDetailMgr::getInstance();
		$detailAndMenu = $bookingDetailMgr->getAllBookingDetailsAndMenu();
		foreach ($bookingDetails as $booking){
			$bookingSeq = $booking["bookingseq"];
			$bookedOn = $booking["bookedon"];
			$bookedOn = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$bookedOn);
			$bookedOn = $bookedOn->format("d-m-Y H:i:s");
			$bookedDate = $booking["bookingdate"];
			$bookedDate = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$bookedDate);
			$bookedDate = $bookedDate->format("d-m-Y");
			$arr = array();
			$arr["seq"] = $bookingSeq;
			$arr["timeslot"] = $booking["timeslot"];
			$arr["bookedon"] = $bookedOn;
			$arr["bookingdate"] = $bookedDate;
			$arr["fullname"] = $booking["fullname"];
			$arr["transactionid"] = $booking["transactionid"];
			$arr["mobilenumber"] = $booking["mobilenumber"];
			$arr["emailid"] = $booking["emailid"];
 			$mainMenuArr = array();
 			$arr["menu"] = $detailAndMenu[$bookingSeq];
			$bookingArr[$bookingSeq] = $arr;
		}
		$bookingMainArr = $this->getArrayForGrid($bookingArr);
		$mainArray["Rows"] = $bookingMainArr;
		$mainArray["TotalRows"] = $this->getBookingCount();
		$json = json_encode($mainArray);
		return $json;
	}
	
	public function getArrayForGrid($bookingArr){
		$mainBookingArr = array();
		foreach ($bookingArr as $booking){
			array_push($mainBookingArr, $booking);
		}
		return $mainBookingArr;
	}
	
	public function getBookingCount(){
		$query = "select count(*) from bookings";
		$count = self::$dataStore->executeCountQueryWithSql($query,true);
		return $count;
	}
	
	public function deleteBySeqs($bookingSeqs){
		$flag = self::$dataStore->deleteInList($bookingSeqs);
		if($flag){
			$bookingDetailMgr = BookingDetailMgr::getInstance();
			$bookingDetailMgr->deleteBookingDetailInList($bookingSeqs);
		}
		return $flag;
	}
	
	public function findBySeq($seq){
		$booking = self::$dataStore->findBySeq($seq);
		return $booking;
	}
}