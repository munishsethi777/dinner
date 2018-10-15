<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Menu.php");
require_once($ConstantsArray['dbServerUrl'] ."StringConstants.php");
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
	
	public function findAll($isApplyFilter = false){
		$menus = self::$dataStore->findAll($isApplyFilter);
		return $menus;
	}
	
	public function getRateByMenuSeq($menuSeq){
		$colVal["seq"] = $menuSeq;
		$attr[0] = "rate";
		$rate = self::$dataStore->executeAttributeQuery($attr, $colVal);
		if(!empty($rate[0])){
			return $rate[0]["rate"];
		}
		return 0;
	}
	
	public function saveMenu($menu){
		$id = self::$dataStore->save($menu);
		return $id;
	}
	
	public function getAllMenusForGrid(){
		$menus = $this->findAll(true);
		$mainArr = array();
		foreach ($menus as $menu){
			$arr["seq"] = $menu->getSeq();
			$arr["title"] = $menu->getTitle();
			$arr["rate"] = $menu->getRate();
			$arr["description"] = $menu->getDescription();
			$arr["isenabled"] = !empty($menu->getIsEnabled());
			$arr["imageName"] = $menu->getImageName();
			array_push($mainArr, $arr);
 		}
 		$jsonArr["Rows"] =  $mainArr;
 		$jsonArr["TotalRows"] = $this->getCount();
 		return $jsonArr;
	}
	
	public function getCount(){
		$query = "select count(*) from menus";
		$count = self::$dataStore->executeCountQueryWithSql($query,true);
		return $count;
	}
	
	public function deleteBySeqs($ids,$imageNames) {
		$flag = self::$dataStore->deleteInList ( $ids );
		if ($flag) {
			$idArr = explode ( ",", $ids );
			$images = explode ( ",", $imageNames );
			$i = 0;
			foreach ( $idArr as $id ) {
				$imageName = $images[$i];
				if(!empty($imageName)){
					$this->deleteMenuImage($id,$imageName);
				}
				$i++;
			}
		}
		return $flag;
	}
	
	private function deleteMenuImage($id,$imageName){
		$path = StringConstants::ROOT_PATH . "images/menuImages/".$id . ".".$imageName;
		FileUtil::deletefile($path);
	}
	
	public function getMenusTitleByTimeSlot($timeSlotSeq){
		$query = "select * from menus inner join menutimeslots on menus.seq = menutimeslots.menuseq where menutimeslots.timeslotseq = $timeSlotSeq";
		$menus = self::$dataStore->executeQuery($query);
		if(!empty($menus)){
			$menuTitles = array();
			foreach ($menus as $menu){
				array_push($menuTitles, $menu["title"]);
			}
			return implode(",", $menuTitles);
		}
		return null;
	}
	
public function getMenusSeqsByTimeSlot($timeSlotSeq){
		$query = "select * from menus inner join menutimeslots on menus.seq = menutimeslots.menuseq where menutimeslots.timeslotseq = $timeSlotSeq";
		$menus = self::$dataStore->executeQuery($query);
		if(!empty($menus)){
			$menuSeqs = array();
			foreach ($menus as $menu){
				array_push($menuSeqs, $menu["menuseq"]);
			}
			return $menuSeqs;
		}
		return array();
	}
	
	
	
	

}