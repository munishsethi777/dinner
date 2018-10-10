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
}