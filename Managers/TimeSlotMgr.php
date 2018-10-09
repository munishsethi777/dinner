<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/TimeSlot.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
class TimeSlotMgr{
	private static $timeSlotMgr;
	private static $dataStore;
	private static $sessionUtil;
	public static function getInstance()
	{
		if (!self::$timeSlotMgr)
		{
			self::$timeSlotMgr = new TimeSlotMgr();
			self::$dataStore = new BeanDataStore(TimeSlot::$className, TimeSlot::$tableName);
		}
		return self::$timeSlotMgr;
	}
	
	public function getTimeSlotsJson(){
		$selectedDate = $_GET["selectedDate"];
		$selectedDate .= " 00:00:00";
		$query = "select timeslots.seq as timeslotseq , timeslots.title as timeslot , timeslots.time, timeslots.seats ,menus.seq as menuseq ,menus.rate,menus.seq as menuseq, menus.title as menutitle from timeslots
inner JOIN menutimeslots on timeslots.seq = menutimeslots.timeslotsseq inner join menus on menutimeslots.menuseq = menus.seq";
		$timeSlots = self::$dataStore->executeQuery($query);
		$slotArr = array();
		$bookingMgr = BookingMgr::getInstance();
		foreach ($timeSlots as $timeSlot){
			$timeSlotSeq = $timeSlot["timeslotseq"];
			$date = DateUtil::StringToDateByGivenFormat("d-m-Y H:i:s",$selectedDate);
			$date = $date->format("Y-m-d H:i:s");
			$bookedSeats = $bookingMgr->getAvailableSeats($date, $timeSlotSeq);
			$arr = array();
			$arr["seq"] = $timeSlotSeq;
			$arr["timeslot"] = $timeSlot["timeslot"];
			$arr["time"] = $timeSlot["time"];
			$totalSeats = $timeSlot["seats"];
			$arr["seats"] = $totalSeats;
			$availableInPercent = 100;
			if($bookedSeats > 0){
				$percent = ($bookedSeats*100)/$totalSeats;
				$availableInPercent -=$percent; 
			}
			$arr["availableInPercent"] = $availableInPercent;
			$arr["seatsAvailable"] = $totalSeats - $bookedSeats;
			$mainMenuArr = array();
			if(array_key_exists($timeSlotSeq, $slotArr)){
				$arr = $slotArr[$timeSlotSeq];
				$mainMenuArr = $arr["menu"];
			}
			$menu = array();
			$menu["menutitle"] = $timeSlot["menutitle"];
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
	
	

}