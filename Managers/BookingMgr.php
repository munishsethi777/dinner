<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Booking.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/BookingDetail.php");
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
where bookedon = '$date' and timeslot = $timeSlots";
		return self::$dataStore->executeCountQueryWithSql($query);
	}
	
	
	public function saveBooking($bookingObj){
		$id = self::$dataStore->save($bookingObj);	
		return $id;
	}
	
	public function getBookingJsonForGrid(){
		$query = "select bookings.emailid as email,bookings.mobilenumber as mobile,bookings.seq as bookingseq,bookings.bookedon as bookedon,bookings.bookingdate as bookingdate,bookingpayments.transactionid as transactionid, bookings.fullname as fullname,bookingdetails.members as members,timeslots.title as timeslot,menus.title as menutitle from bookings
inner join bookingdetails on bookings.seq = bookingdetails.bookingseq inner join bookingpayments on bookings.seq = bookingpayments.bookingseq
inner join timeslots on bookings.timeslot = timeslots.seq inner join menus on bookingdetails.menuseq = menus.seq";
		$bookingDetails =  self::$dataStore->executeQuery($query,true);
		$bookingArr = array();
		$bookingMainArr = array();
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
			$arr["mobile"] = $booking["mobile"];
			$arr["email"] = $booking["email"];
 			$mainMenuArr = array();
			$menuStrArr = "";
			if(array_key_exists($bookingSeq, $bookingArr)){
				$arr = $bookingArr[$bookingSeq];
				$menuStrArr = $arr["menu"];
			}
			$menu = array();
			$menuTitle = $booking["menutitle"];
			$members = $booking["members"];
			if(!empty($menuStrArr)){
				$arr["menu"] = $menuStrArr . " , " . $members . " - " . $menuTitle;
			}else{
				$arr["menu"] = $members . " - " . $menuTitle;
			}
			$bookingArr[$bookingSeq] = $arr;
			array_push($bookingMainArr, $arr);
		}
		$mainArray["Rows"] = $bookingMainArr;
		$mainArray["TotalRows"] = $this->getBookingCount();
		$json = json_encode($mainArray);
		return $json;
	}
	
	public function getBookingCount(){
		$query = "select count(*) from bookings";
		$count = self::$dataStore->executeCountQueryWithSql($query,true);
		return $count;
	}
}