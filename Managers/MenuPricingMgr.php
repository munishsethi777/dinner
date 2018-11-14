<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/MenuPricing.php");
class MenuPricingMgr{
	private static $MenuPricingMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$MenuPricingMgr)
		{
			self::$MenuPricingMgr = new MenuPricingMgr();
			self::$dataStore = new BeanDataStore(MenuPricing::$className, MenuPricing::$tableName);
		}
		return self::$MenuPricingMgr;
	}
	
	
	public function saveMenuPricingFromPost($menuSeq){
		$this->deleteByMenuSeq($menuSeq);
		$priceDates = $_POST["priceDates"];
		foreach ($priceDates as $key=>$priceDate){
			if(empty($priceDate)){
				continue;
			}
			$dates = explode(",", $priceDate);
			$des = $_POST["priceDescription"][$key];
			$price = $_POST["price"][$key];
			foreach ($dates as $date){
				$menuPricing = new MenuPricing();
				$menuPricing->setDescription($des);
				$menuPricing->setPrice($price);
				$menuPricing->setMenuSeq($menuSeq);
				$dateObj = DateUtil::StringToDateByGivenFormat("d-m-Y", $date);
				$dateObj = $dateObj->setTime(0, 0);
				$menuPricing->setDate($dateObj);
				self::$dataStore->save($menuPricing);
			}
		}
	}
	
	public function findMenuPricingArrBySlotSeq($menuSeq){
		$query = "SELECT * FROM menupricing where menuseq = $menuSeq order by seq";
		$menuPricing = self::$dataStore->executeObjectQuery($query);
		$mainArr = array();
		if(!empty($menuPricing)){
			$menuPricingArr = array();
			foreach ($menuPricing as $mp){
				$arr = array();
				$price = $mp->getPrice();
				$dataArr = array();
				if(array_key_exists($price,$menuPricingArr)){
					$arr = $menuPricingArr[$price];
				}
				$dataArr["price"] = $price;
				$dataArr["description"] = $mp->getDescription();
				$dateObj = DateUtil::StringToDateByGivenFormat("Y-m-d", $mp->getDate());
				$formatedDate = $dateObj->format("d-m-Y");
				$dataArr["date"] = $formatedDate;
				array_push($arr, $dataArr);
				$menuPricingArr[$price] = $arr;
			}
			$mainArr = $this->getArray($menuPricingArr);
		}
		return $mainArr;
	}
	
	private function getArray($menuPricingArr){
		$mainArr = array();
		foreach ($menuPricingArr as $menuPricing){
			$arr = array();
			$dates  = array_map(create_function('$o', 'return $o["date"];'), $menuPricing);
			$dates = implode(",", $dates);
			$mp = $menuPricing[0];
			$mp["date"] = $dates;
			array_push($mainArr, $mp);
		}
		return $mainArr;
	}
	
	public function getAllMenuPricingArr(){
		$menuPricing = self::$dataStore->findAll();
		$menuPricingArr = array();
		foreach ($menuPricing as $mp){
			$menuSeq = $mp->getMenuSeq();
			$arr = array();
			if(array_key_exists($menuSeq,$menuPricingArr)){
				$arr = $menuPricingArr[$menuSeq];
			}
			array_push($arr, $mp);
			$menuPricingArr[$menuSeq] = $arr;
		}
		return $menuPricingArr;
	}
	
	public function deleteByMenuSeq($menuSeq){
		$colval["menuseq"] = $menuSeq;
		$flag = self::$dataStore->deleteByAttribute($colval);
		return $flag;
	}
	
	public function getPriceByMenuAndDate($menuSeq,$selectedDate){
		$selectedDate = DateUtil::StringToDateByGivenFormat("d-m-Y", $selectedDate);
		$selectedDate = $selectedDate->format("Y-m-d");
		$colVal["menuSeq"] = $menuSeq;
		$colVal["date"] = $selectedDate; 
		$menuPricing = self::$dataStore->executeConditionQuery($colVal);
		if(!empty($menuPricing)){
			return $menuPricing[0]->getPrice();
		}
		return null;
	}
	
	
}