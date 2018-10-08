<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Menu.php");
class MenuMgr{
	private static $menuMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$menuMgr)
		{
			self::$menuMgr = new MenuMgr();
			self::$dataStore = new BeanDataStore(Menu::$className, Menu::$tableName);
		}
		return self::$menuMgr;
	}
	
	public function findBySeq($seq){
		$menu = self::$dataStore->findBySeq($seq);
		return $menu;
	}

}