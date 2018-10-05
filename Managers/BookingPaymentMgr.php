<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/SessionUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/BookingPayment.php");
class BookingPaymentMgr{
	private static $bookingPaymentMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$bookingPaymentMgr)
		{
			self::$bookingPaymentMgr = new BookingPaymentMgr();
			self::$dataStore = new BeanDataStore(BookingPayment::$className, BookingPayment::$tableName);
			self::$sessionUtil = SessionUtil::getInstance();
		}
		return self::$bookingPaymentMgr;
	}

}
