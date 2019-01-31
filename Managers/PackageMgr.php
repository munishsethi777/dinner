<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Package.php");
class PackageMgr{
	private static $PackageMgr;
	private static $dataStore;
	private static $sessionUtil;

	public static function getInstance()
	{
		if (!self::$PackageMgr)
		{
			self::$PackageMgr = new PackageMgr();
			self::$dataStore = new BeanDataStore(Package::$className, Package::$tableName);
		}
		return self::$PackageMgr;
	}

	public function savePackage($package){
		$id = self::$dataStore->save($package);
		return $id;
	}
	
	public function findBySeq($seq){
		$package = self::$dataStore->findBySeq($seq);
		return $package;
	}
	public function findByOccasionSeq($seq){
		$packages = self::$dataStore->executeQuery("select * from packages where occasionseq = ".$seq);
		return $packages;
	}
	public function getAllForGrid(){
		//$query = "select * from packages";
		$query = "select occasions.title occasion,packages.* from packages left join occasions on occasions.seq = packages.occasionseq";
		$packages = self::$dataStore->executeQuery($query,true);
		$mainArr["Rows"] = $packages;
		$mainArr["TotalRows"] = $this->getAllCount();
		return json_encode($mainArr);
	}
	
	public function findAll(){
		$packages = self::$dataStore->findAll();
		return $packages;
	}
	
	public function findAllArrEnabled(){
		$query = "select * from packages where isenabled = 1";
		$packages = self::$dataStore->executeQuery($query);
		return $packages;
	}
	
	public function getAllCount(){
		$query = "select count(*) from packages";
		$count = self::$dataStore->executeCountQueryWithSql($query,true);
		return $count;
	}
	
	public function deleteBySeqs($ids){
		$flag = self::$dataStore->deleteInList($ids);
		return $flag;
	}
	
	public function getPackagePrice($id){
		$colVal["seq"] = $id;
		$attributes[0] = "price";
		$package = self::$dataStore->executeAttributeQuery($attributes, $colVal);
		if(!empty($package)){
			return $package[0][0];
		}
		return 0;
	}
}