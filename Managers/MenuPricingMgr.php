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
	
	
	public function saveMenuPricing($dates,$menuSeq){
		$this->deleteByMenuSeq($menuSeq);
		$des = $_POST["priceDescription"];
		$price = $_POST["price"];
		foreach ($dates as $date){
			$menuPricing = new MenuPricing();
			$menuPricing->setDescription($des);
			$menuPricing->setPrice($price);
			$menuPricing->setMenuSeq($menuSeq);
			$dateObj = DateUtil::StringToDateByGivenFormat("d-m-y", $date);
			$dateObj = $dateObj->setTime(0, 0);
			
			$menuPricing->setDate($dateObj);
			self::$dataStore->save($menuPricing);
		}
	}
	
	public function findMenuPricingArrBySlotSeq($menuSeq){
		$colval["menuseq"] = $menuSeq;
		$menuPricing = self::$dataStore->executeConditionQuery($colval);
		$menuPricingArr = array();
		if(!empty($menuPricing)){
			$dateArr = array();
			$price = 0;
			$des = null;
			foreach ($menuPricing as $mp){
				$arr = array();
				$date = $mp->getDate();
				$dateObj = DateUtil::StringToDateByGivenFormat("Y-m-d", $date);
				$formatedDate = $dateObj->format("d-m-y");
				array_push($dateArr, $formatedDate);
				$price = $mp->getPrice();
				$des = $mp->getDescription();
			}
			$dates = implode(", ", $dateArr);
			$menuPricingArr["dates"] = $dates;
			$menuPricingArr["price"] = $price;
			$menuPricingArr["description"] = $des;
		}
		return $menuPricingArr;
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