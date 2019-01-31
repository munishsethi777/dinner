<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Notification.php");
class NotificationMgr{
	private static $notificationMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$notificationMgr)
		{
			self::$notificationMgr = new NotificationMgr();
			self::$dataStore = new BeanDataStore(Notification::$className, Notification::$tableName);
		}
		return self::$notificationMgr;
	}
	
	public function saveNotification($notificationObj){
		$id = self::$dataStore->save($notificationObj);
		return $id;
	}
	
	public function getPendingNotifications(){
		$colval["status"] = NotificationStatus::pending;
		$pendingNotifications = self::$dataStore->executeConditionQuery($colval);
		return $pendingNotifications;
	}
	
	
}