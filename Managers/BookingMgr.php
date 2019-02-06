<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Booking.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/BookingDetail.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingDetailMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingAddOnMgr.php");
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
where ((bookings.status != 'Rescheduled' and bookings.status != 'Cancelled') or bookings.status is NULL) and bookingdate = '$date' and timeslot = $timeSlots";
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
	
	public function getBookingId($booking,$totalMembers){
		$location = "BLR";
		$sessionId = $booking->getTimeSlot();
		$date = new DateTime();
		$dateStr = $date->format("dmY");
		$bookingId = $location . "-" . $dateStr ."-". $sessionId ."-". $totalMembers . "-".$booking->getSeq();
		return $bookingId;
	}
	
	public function updateBookingId($booking,$totalMembers){
		$bookingId = $this->getBookingId($booking, $totalMembers);
		$colVal["bookingid"] = $bookingId;
		$condition["seq"] = $booking->getSeq();
		$flag = self::$dataStore->updateByAttributesWithBindParams($colVal,$condition);
		if($flag){
			return $bookingId;
		}
		return null;
	}
	
	
	
	public function getBookingJsonForGrid(){
		$query = "select packages.title as package,occasions.title as occasion,bookings.bookingid,bookings.emailid as emailid,bookings.mobilenumber as mobilenumber,bookings.seq,bookings.bookedon as bookedon,bookings.bookingdate as bookingdate,bookings.transactionid as transactionid, bookings.fullname as fullname,timeslots.title as timeslot from bookings 
inner join timeslots on bookings.timeslot = timeslots.seq
left join packages on bookings.packageseq = packages.seq
left join occasions on packages.occasionseq = occasions.seq";
		$bookings =  self::$dataStore->executeQuery($query,true,false,true);
		$bookingArr = array();
		$bookingMainArr = array();
		$bookingDetailMgr = BookingDetailMgr::getInstance();
		$detailAndMenu = $bookingDetailMgr->getAllBookingDetailsAndMenu();
		$timeSlotsArr = array();
		$decCount = 0;
		foreach ($bookings as $booking){
			$bookingSeq = $booking["seq"];
			$menus = $detailAndMenu[$bookingSeq];
			$bookedOn = $booking["bookedon"];
			$bookedOn = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$bookedOn);
			$bookedOn = $bookedOn->format("d-m-Y H:i");
			$bookedDate = $booking["bookingdate"];
			$bookedDate = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$bookedDate);
			$bookedDate = $bookedDate->format("d-m-Y");
			$arr = array();
			$arr["bookingid"] = $booking["bookingid"];
			$arr["seq"] = $bookingSeq;
			$timeSlot = $booking["timeslot"];
			$arr["timeslots.title"] = $timeSlot;
			$arr["bookedon"] = $bookedOn;
			$arr["bookingdate"] = $bookedDate;
			$arr["fullname"] = $booking["fullname"];
			$arr["transactionid"] = $booking["transactionid"];
			$arr["mobilenumber"] = $booking["mobilenumber"];
			$arr["emailid"] = $booking["emailid"];
			if(!empty($booking["package"])){
				$arr["package"] = $booking["occasion"] ."-" . $booking["package"];
			}else{
				$arr["package"] = "";
			}
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
			$bookingAddOnMgr = BookingAddOnMgr::getInstance();
			$bookingAddOnMgr->deleteBookingAddOnInList($bookingSeqs);
		}
		return $flag;
	}
	
	public function findBySeq($seq){
		$booking = self::$dataStore->findBySeq($seq);
		return $booking;
	}
	
	public function getCouponUsageCount($couponSeq){
		$colVal["couponseq"] = $couponSeq;
		$bookingCount = self::$dataStore->executeCountQuery($colVal);
		return $bookingCount;
	}
	
	private function getBookingWithTimeSlot($bookingId){
		$query = "select bookings.*,timeslots.title from bookings inner join timeslots on bookings.timeslot = timeslots.seq where bookings.seq = $bookingId";
		$bookings = self::$dataStore->executeQuery($query);
		if(!empty($bookings)){
			return $bookings[0];
		}
		return null;
	}
	private function getBookingWithTimeSlotById($bookingId){
		$query = "select bookings.*,timeslots.title from bookings inner join timeslots on bookings.timeslot = timeslots.seq where bookings.bookingid = '$bookingId'";
		$bookings = self::$dataStore->executeQuery($query);
		if(!empty($bookings)){
			return $bookings[0];
		}
		return null;
	}
	
	public function updateBookingStatus($status,$bookigId){
		$colVal["status"] = $status;
		$condition["seq"] = $bookigId;
		self::$dataStore->updateByAttributesWithBindParams($colVal,$condition);
	}
	
	public function getBookingDetail($bookingId){
		$booking = $this->getBookingWithTimeSlot($bookingId);
		$booking = $this->getBookingDetailArr($booking);
		return $booking;
	}
	
	public function getBookingDetailById($bookingId){
		$booking = $this->getBookingWithTimeSlotById($bookingId);
		$booking = $this->getBookingDetailArr($booking);
		return $booking;
	}
	
	private function getBookingDetailArr($booking){
		if(!empty($booking)){
			$bookingDate = $booking["bookingdate"];
			$bookedOn = $booking["bookedon"];
			$bookingDate = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s", $bookingDate);
			$isPast = false;
			$now = new DateTime();
			$now->setTime(0, 0);
			$isPast = $bookingDate <= $now;
			$bookedOn = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s", $bookedOn);
			$bookingDateStr = $bookingDate->format("jS F Y");
			$bookedOnStr = $bookedOn->format("jS F Y H:i a");
			$booking["bookingdate"] = $bookingDateStr;
			$booking["bookedon"] = $bookedOnStr;
			$bookingDtailMgr = BookingDetailMgr::getInstance();
			$bookingDetails = $bookingDtailMgr->getBookingDetailAndMenu($booking["seq"]);
			$bookingAddOnMgr = BookingAddOnMgr::getInstance();
			$bookingAddOn = $bookingAddOnMgr->FindArrByBookingSeq($booking["seq"]);
			$booking["menuDetail"] = $bookingDetails;
			$booking["isPast"] = $isPast;
			$discountPercent = $booking["discountpercent"];
			$amount = $booking["amount"] / 100;
			$booking["amount"] = $amount;
			$booking["bookingAddOn"] = $bookingAddOn;
		}
		return $booking;
	}
	
	public function getBookingByParentId($parentBookingId){
		$colVal["parentbookingseq"] = $parentBookingId;
		$booking = self::$dataStore->executeConditionQuery($colVal);
		if(!empty($booking)){
			return $booking[0];
		}
		return null;
	}
	
	public function getBookingForClosurNotification($timeSlots){
		$currentDate = new DateTime();
		$currentDate = $currentDate->format("Y-m-d");
		$query = "select * from bookings where timeslot in ($timeSlots) and bookingdate = '$currentDate'";
		$bookings = self::$dataStore->executeQuery($query);
		return $bookings;
	}
}