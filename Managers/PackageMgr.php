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
	public function findArrBySeq($seq){
		$query = "select packages.*,occasions.title as occasion from packages inner join occasions on packages.occasionseq = occasions.seq where packages.seq = $seq ";
		$package = self::$dataStore->executeQuery($query);
		if(!empty($package)){
			return $package[0];
		}
		return null;
	}
	public function findByOccasionSeq($seq){
		$packages = self::$dataStore->executeQuery("select * from packages where occasionseq = ".$seq);
		return $packages;
	}
	public function getAllForGrid(){
		$query = "select occasions.title as occasion ,packages.* from packages left join occasions on occasions.seq = packages.occasionseq";
		$packages = self::$dataStore->executeQuery($query,true);
		$pacArr = array();
		foreach ($packages as $package){
			$package["occasions.title"] = $package["occasion"];
			$package["packages.title"] = $package["title"];
			$package["packages.description"] = $package["description"];
			$package["packages.createdon"] = $package["createdon"];
			$package["packages.lastmodifiedon"] = $package["lastmodifiedon"];
			$package["packages.isenabled"] = $package["isenabled"];
			array_push($pacArr, $package);
		}
		$mainArr["Rows"] = $pacArr;
		$mainArr["TotalRows"] = $this->getAllCount();
		return json_encode($mainArr);
	}
	
	public function getAllWithOccasions(){
		$query = "select occasions.title occasion,packages.* from packages left join occasions on occasions.seq = packages.occasionseq";
		$packages = self::$dataStore->executeQuery($query,true);
		return $packages;
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
		$query = "select count(*) from packages inner join occasions on occasions.seq = packages.occasionseq";
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