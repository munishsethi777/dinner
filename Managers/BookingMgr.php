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
	
	public function getBookedSeats($date,$timeSlots,$bookingSeq){
		$query = "SELECT sum(bookingdetails.members) as totalcount from bookings inner JOIN bookingdetails on bookings.seq = bookingdetails.bookingseq
		where bookings.seq = $bookingSeq and bookingdate = '$date' and timeslot = $timeSlots";
		return self::$dataStore->executeCountQueryWithSql($query);
	}
	
	
	public function saveBooking($bookingObj){
		$id = self::$dataStore->save($bookingObj);	
		return $id;
	}
	
	public function getBookingJsonForGrid(){
		$query = "select bookings.emailid as emailid,bookings.mobilenumber as mobilenumber,bookings.seq as bookingseq,bookings.bookedon as bookedon,bookings.bookingdate as bookingdate,bookings.transactionid as transactionid, bookings.fullname as fullname,timeslots.title as timeslot from bookings inner join timeslots on bookings.timeslot = timeslots.seq";
		$bookings =  self::$dataStore->executeQuery($query,true,false,true);
		$bookingArr = array();
		$bookingMainArr = array();
		$bookingDetailMgr = BookingDetailMgr::getInstance();
		$detailAndMenu = $bookingDetailMgr->getAllBookingDetailsAndMenu();
		$timeSlotsArr = array();
		$decCount = 0;
		foreach ($bookings as $booking){
			$bookingSeq = $booking["bookingseq"];
			$menus = $detailAndMenu[$bookingSeq];
			$bookedOn = $booking["bookedon"];
			$bookedOn = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$bookedOn);
			$bookedOn = $bookedOn->format("d-m-Y H:i:s");
			$bookedDate = $booking["bookingdate"];
			$bookedDate = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$bookedDate);
			$bookedDate = $bookedDate->format("d-m-Y");
			$arr = array();
			$arr["seq"] = $bookingSeq;
			$timeSlot = $booking["timeslot"];
			$arr["timeslots.title"] = $timeSlot;
			$arr["bookedon"] = $bookedOn;
			$arr["bookingdate"] = $bookedDate;
			$arr["fullname"] = $booking["fullname"];
			$arr["transactionid"] = $booking["transactionid"];
			$arr["mobilenumber"] = $booking["mobilenumber"];
			$arr["emailid"] = $booking["emailid"];
 			$mainMenuArr = array();
 			$arr["menus.title"] = $menus;
			$bookingArr[$bookingSeq] = $arr;
		}
		$bookingMainArr = $this->getArrayForGrid($bookingArr);
		$mainArray["Rows"] = $bookingMainArr;
		$mainArray["TotalRows"] = $this->getBookingCount($detailAndMenu);
		$json = json_encode($mainArray);
		return $json;
	}
	
	public static function isFilter($menuTitle){
		// filter data.
		$flag = false;
		if (isset($_GET['filterscount']))
		{
			$filterscount = $_GET['filterscount'];
	
			if ($filterscount > 0)
			{
				$tmpdatafield = "";
				$tmpfilteroperator = "";
				$flag = true;
				for ($i=0; $i < $filterscount; $i++)
				{
					// get the filter's value.
					$filtervalue = $_GET["filtervalue" . $i];
					// get the filter's condition.
					$filtercondition = $_GET["filtercondition" . $i];
					// get the filter's column.
					$filterdatafield = $_GET["filterdatafield" . $i];
					// get the filter's operator.
					$filteroperator = $_GET["filteroperator" . $i];
	
					if ($tmpdatafield == "")
					{
						$tmpdatafield = $filterdatafield;
					}
					if($filterdatafield == "menus.title"){
						$menusArr = explode(" , ", $menuTitle);
						foreach ($menusArr as $menu){
							$arr = explode(" - ", $menu);
							if($arr[1] == $filtervalue && $filtercondition == "EQUAL"){
								$flag = true;
								return $flag;
							}else{
								$flag = false;
							}
						}
					}
					$tmpfilteroperator = $filteroperator;
					$tmpdatafield = $filterdatafield;
				}
			}
			else{
				$flag = true;
			}
		}else{
			$flag = true;;
		}
		return $flag;
	}
	
	public function getArrayForGrid($bookingArr){
		$mainBookingArr = array();
		foreach ($bookingArr as $booking){
			array_push($mainBookingArr, $booking);
		}
		return $mainBookingArr;
	}
	
	public function getBookingCount($detailAndMenu){
		$query = "select count(DISTINCT bookings.seq) from bookings inner join timeslots on bookings.timeslot = timeslots.seq";
		$count = self::$dataStore->executeCountQueryWithSql($query,true);
		return $count;
	}
	
// 	public function getBookingCount(){
// 		$query = "select count(*) from bookings inner join timeslots on bookings.timeslot = timeslots.seq";
// 		$count = self::$dataStore->executeCountQueryWithSql($query,true);
// 		return $count;
// 	}
	
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