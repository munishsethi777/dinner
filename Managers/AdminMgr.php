<?php
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Admin.php");
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
class AdminMgr{
	private static $adminMgr;
	private static $adminDataStore;
	
	public static function getInstance()
	{
		if (!self::$adminMgr)
		{
			self::$adminMgr = new AdminMgr();
			self::$adminDataStore = new BeanDataStore(Admin::$className,Admin::$tableName);
		}
		return self::$adminMgr;
	}
	
	public function logInAdmin($username, $password){
		$conditionVal["username"] = $username;
		$admin = self::$adminDataStore->executeConditionQuery($conditionVal);
		if(!empty($admin)){
			return $admin[0];
		}
		return null;
	}
	
	public function getAllAdmins(){
		$admins = self::$adminDataStore->findAll();
		return $admins;
	}
	
	public function isPasswordExist($password){
		$sessionUtil = SessionUtil::getInstance();
		$adminSeq = $sessionUtil->getAdminLoggedInSeq();
		$params["password"] = $password;
		$params["seq"] = $adminSeq;
		$count = self::$adminDataStore->executeCountQuery($params);
		return $count > 0;
	}
	
	public function ChangePassword($password){
		$sessionUtil = SessionUtil::getInstance();
		$adminSeq = $sessionUtil->getAdminLoggedInSeq();
		$attr["password"] = $password;
		$condition["seq"] = $adminSeq;
		self::$adminDataStore->updateByAttributesWithBindParams($attr,$condition);
	}
	
	public function toArray($admin){
		$adminArr = array();
		$adminArr["seq"] = $admin->getSeq();
		$adminArr["username"] = $admin->getUserName();
		$adminArr["name"] = $admin->getName();
		return $adminArr;
	}
}