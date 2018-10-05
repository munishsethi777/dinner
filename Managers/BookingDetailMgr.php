<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/SessionUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/BookingDetail.php");
class BookingMgr{
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

}