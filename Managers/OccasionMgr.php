<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Occasion.php");
class OccasionMgr{
	private static $occasionMgr;
	private static $dataStore;
	private static $sessionUtil;

	public static function getInstance()
	{
		if (!self::$occasionMgr)
		{
			self::$occasionMgr = new OccasionMgr();
			self::$dataStore = new BeanDataStore(Occasion::$className, Occasion::$tableName);
		}
		return self::$occasionMgr;
	}

	public function saveOccasion($occasion){
		$id = self::$dataStore->save($occasion);
		return $id;
	}
	
	public function findBySeq($seq){
		$occasion = self::$dataStore->findBySeq($seq);
		return $occasion;
	}
	
	public function getAllForGrid(){
		$query = "select * from occasions";
		$coupons = self::$dataStore->executeQuery($query,true);
		$mainArr["Rows"] = $coupons;
		$mainArr["TotalRows"] = $this->getAllCount();
		return json_encode($mainArr);
	}
	
	
	public function getAllCount(){
		$query = "select count(*) from occasions";
		$count = self::$dataStore->executeCountQueryWithSql($query,true);
		return $count;
	}
	
	public function deleteBySeqs($ids){
		$flag = self::$dataStore->deleteInList($ids);
		return $flag;
	}
	
	public function findAll(){
		$occasions = self::$dataStore->findAll();
		return $occasions;
	}
}