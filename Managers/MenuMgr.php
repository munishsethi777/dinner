<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Menu.php");
require_once($ConstantsArray['dbServerUrl'] ."StringConstants.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
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
	
	public function getAllForTimeSlot(){
		$menus = $this->findAll();
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
	public function getAllMenuTitle(){
		$menus = $this->findAll(true);
		$mainArr = array();
		foreach ($menus as $menu){
			array_push($mainArr, $menu->getTitle());
		}
		sort($mainArr);
		return $mainArr;
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
		$query = "select menus.title, menus.seq from menus inner join menutimeslots on menus.seq = menutimeslots.menuseq where menutimeslots.timeslotsseq = $timeSlotSeq";
		$menus = self::$dataStore->executeQuery($query);
		if(!empty($menus)){
			$menuTitles = array();
			foreach ($menus as $menu){
				$menuTitles[$menu["seq"]] = $menu["title"];
			}
			return $menuTitles;
		}
		return null;
	}
	
	public function getMenusSeqsByTimeSlot($timeSlotSeq){
		$query = "select * from menus inner join menutimeslots on menus.seq = menutimeslots.menuseq where menutimeslots.timeslotsseq = $timeSlotSeq";
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
	
	
	public function getMenusAndSeats($selectedDate,$timeSlotSeq,$bookingSeq){
		$selectedDate .= " 00:00:00";
		$date = DateUtil::StringToDateByGivenFormat("d-m-Y H:i:s",$selectedDate);
		$dbFormat = $date->format("Y-m-d");
		$query = "select menupricing.price as specialprice,menus.rate,menus.title, menus.seq,timeslots.seats from menus inner join menutimeslots on menus.seq = menutimeslots.menuseq inner join timeslots on menutimeslots.timeslotsseq = timeslots.seq left join menupricing on menus.seq = menupricing.menuseq and menupricing.date = '$dbFormat' where menutimeslots.timeslotsseq = $timeSlotSeq";
		$menus = self::$dataStore->executeQuery($query);
		if(!empty($menus)){
			$menuTitles = array();
			foreach ($menus as $menu){
				$menuSpecialPrice = $menu["specialprice"];
				if(!empty($menuSpecialPrice)){
					$menu["rate"] = $menuSpecialPrice;
				}
				$menuTitles[$menu["seq"]] = $menu;
			}
			
			$dateStr = $date->format("Y-m-d H:i:s");
			$bookingMgr = BookingMgr::getInstance();
			$totalSeats = $menus[0]["seats"];
			$totalBookedSeats = $bookingMgr->getAvailableSeats($dateStr, $timeSlotSeq);
			$bookedSeats = 0;
			if($bookingSeq != ""){
				$bookedSeats = $bookingMgr->getBookedSeats($dateStr, $timeSlotSeq,$bookingSeq);
			}
			$availableSeats = intval($totalSeats);
			if(!empty($totalBookedSeats)){
				$availableSeats -= intval($totalBookedSeats); 
			}
			$mainArr["menus"] = $menuTitles;
			$mainArr["availableSeats"] = $availableSeats;
			$mainArr["totalSeats"] = $totalSeats;
			$mainArr["totalSelectedSeats"] = $totalBookedSeats;
			$mainArr["selectedSeats"] = $bookedSeats;
			return $mainArr;
		}
		return null;
	}
	
	public function findBySeqInList($seqs){
		$query = "select * from menus where seq in ($seqs)";
		$menus = self::$dataStore->executeQuery($query);
		$menus = $this->_group_by($menus, "seq");
		return $menus;
	}
	
	function _group_by($array, $key) {
		$return = array();
		foreach($array as $val) {
			$return[$val[$key]][] = $val;
		}
		return $return;
	}

}