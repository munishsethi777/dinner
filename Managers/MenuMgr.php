<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/SessionUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Menu.php");
class MenuMgr{
	private static $menuMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$menuMgr)
		{
			self::$menuMgr = new BookingPaymentMgr();
			self::$dataStore = new BeanDataStore(Menu::$className, Menu::$tableName);
			self::$sessionUtil = SessionUtil::getInstance();
		}
		return self::$menuMgr;
	}

}