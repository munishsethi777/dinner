<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/TimeSlot.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/MenuTimeSlot.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
class TimeSlotMgr{
	private static $timeSlotMgr;
	private static $dataStore;
	private static $menuTimeSlotDataStore;
	private static $sessionUtil;
	
	public static function getInstance()
	{
		if (!self::$timeSlotMgr)
		{
			self::$timeSlotMgr = new TimeSlotMgr();
			self::$dataStore = new BeanDataStore(TimeSlot::$className, TimeSlot::$tableName);
			self::$menuTimeSlotDataStore = new BeanDataStore(MenuTimeSlot::$className, MenuTimeSlot::$tableName);
		}
		return self::$timeSlotMgr;
	}
	
	public function getTimeSlotsJson(){
		$selectedDate = $_GET["selectedDate"];
		$selectedDate .= " 00:00:00";
		$query = "select timeslots.description as description,timeslots.seq as timeslotseq , timeslots.title as timeslot , timeslots.time, timeslots.seats ,menus.seq as menuseq ,menus.rate,menus.seq as menuseq, menus.title as menutitle from timeslots
inner JOIN menutimeslots on timeslots.seq = menutimeslots.timeslotsseq inner join menus on menutimeslots.menuseq = menus.seq";
		$timeSlots = self::$dataStore->executeQuery($query);
		$slotArr = array();
		$bookingMgr = BookingMgr::getInstance();
		foreach ($timeSlots as $timeSlot){
			$timeSlotSeq = $timeSlot["timeslotseq"];
			$date = DateUtil::StringToDateByGivenFormat("d-m-Y H:i:s",$selectedDate);
			$dateStr = $date->format("Y-m-d H:i:s");
			$bookedSeats = $bookingMgr->getAvailableSeats($dateStr, $timeSlotSeq);
			$arr = array();
			$arr["seq"] = $timeSlotSeq;
			$arr["timeslot"] = $timeSlot["timeslot"];
			$arr["time"] = $timeSlot["time"];
			$totalSeats = $timeSlot["seats"];
            $arr["seats"] = $totalSeats;
			$arr["description"] = $timeSlot["description"];
			$availableInPercent = 100;
			if($bookedSeats > 0){
				$percent = ($bookedSeats*100)/$totalSeats;
				$availableInPercent -=$percent; 
			}
			$arr["availableInPercent"] = $availableInPercent;
			$arr["seatsAvailable"] = $totalSeats - $bookedSeats;
            if($arr["seatsAvailable"] <=0){
                $arr["seatsAvailable"] = 0;
            }
            $mainMenuArr = array();
			if(array_key_exists($timeSlotSeq, $slotArr)){
				$arr = $slotArr[$timeSlotSeq];
				$mainMenuArr = $arr["menu"];
			}
			$menu = array();
			$menu["menutitle"] = $timeSlot["menutitle"];
			
			$dayName =  $date->format('D');
			if($dayName == "Fri" || $dayName == "Sat" || $dayName == "Sun"){
				$timeSlot["rate"] += 1000;
			}
			
			$menu["rate"] = $timeSlot["rate"];
			$menu["menuseq"] = $timeSlot["menuseq"];
			array_push($mainMenuArr, $menu);
			$arr["menu"] = $mainMenuArr;
			$slotArr[$timeSlotSeq] = $arr;
		}
		$json = json_encode($slotArr);
		return $json;
	}
	
	public function findBySeq($seq){
		$timeSlot = self::$dataStore->findBySeq($seq);
		return $timeSlot;
	}
	
	
	
	public function saveTimeSlot($timeSlot,$menus){
		$id = self::$dataStore->save($timeSlot);
		if($id > 0){
			$this->deleteMenuSlotsInList($id);
			foreach ($menus as $menu){
				$menuTimeSlot = new MenuTimeSlot();
				$menuTimeSlot->setMenuSeq($menu);
				$menuTimeSlot->setTimeSlotsSeq($id);
				self::$menuTimeSlotDataStore->save($menuTimeSlot);
			}
		}
		return $id;
		
	}
	
	public function deleteBySeqs($timeSlotSeqs){
		$flag = self::$dataStore->deleteInList($timeSlotSeqs);
		if($flag){
			$this->deleteMenuSlotsInList($timeSlotSeqs);
		}
		return $flag;
	}
	
	private function deleteMenuSlotsInList($timeSlotSeqs){
		$query = "delete from menutimeslots where timeslotsseq in ($timeSlotSeqs)";
		self::$menuTimeSlotDataStore->executeQuery($query);
		
	}
	public function getAllTimeSlotsForGrid(){
		$timeSlots = self::$dataStore->findAll(true);
		$menuArr = array();
		$menuMgr = MenuMgr::getInstance();
		foreach ($timeSlots as $timeSlot){
			$arr["seq"] = $timeSlot->getSeq();
			$arr["title"] = $timeSlot->getTitle();
			$arr["description"] = $timeSlot->getDescription();
			$arr["seats"] = $timeSlot->getSeats();
			$menus = $menuMgr->getMenusTitleByTimeSlot($timeSlot->getSeq());
			$arr["menus"] = implode(",",$menus);
			array_push($menuArr, $arr);
		}
		$mainArr["Rows"] = $menuArr;
		$mainArr["TotalRows"] = $this->getCount();
		return $mainArr;
	}
	
	private function getCount(){
		$query = "select count(*) from timeslots";
		$count = self::$dataStore->executeCountQueryWithSql($query,true);
		return $count;	
	}
	
	public function findAll(){
		$timeSlots = self::$dataStore->findAll();
		return $timeSlots;
	}
	
	

}
