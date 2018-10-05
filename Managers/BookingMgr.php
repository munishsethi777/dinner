<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/SessionUtil.php");
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
			self::$sessionUtil = SessionUtil::getInstance();
		}
		return self::$bookingMgr;
	}
	
}