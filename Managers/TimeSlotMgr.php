<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/TimeSlot.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/MenuTimeSlot.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuPricingMgr.php");
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
		$date = DateUtil::StringToDateByGivenFormat("d-m-Y H:i:s",$selectedDate);
		$dateStr = $date->format("Y-m-d H:i:s");
		$query = "select timeslots.starton,timeslots.endon,timeslots.bookingavailabletill, timeslots.description as description,timeslots.seq as timeslotseq , timeslots.title as timeslot , timeslots.time, timeslots.seats ,menus.seq as menuseq ,menus.rate,menus.seq as menuseq, menus.title as menutitle from timeslots
inner JOIN menutimeslots on timeslots.seq = menutimeslots.timeslotsseq inner join menus on menutimeslots.menuseq = menus.seq where timeslots.seq not in (select slotdetails.slotseq from slotdetails where date = '$dateStr')";
		$timeSlots = self::$dataStore->executeQuery($query);
		$slotArr = array();
		$bookingMgr = BookingMgr::getInstance();
		$menuPricingMgr = MenuPricingMgr::getInstance();
		$menuPricings = $menuPricingMgr->getAllMenuPricingArr();
		foreach ($timeSlots as $timeSlot){
			
			$startOn = $timeSlot["starton"];
			if(!empty($startOn)){
				$startOnDate = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$startOn);
				if($startOnDate > $date){
					continue;
				}
			}
			$endOn = $timeSlot["endon"];
			if(!empty($endOn)){
				$endOnDate = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$endOn);
				if($endOnDate < $date){
					continue;
				}
			}
			$bookingTill = $timeSlot["bookingavailabletill"];
			$currentDate = new DateTime();
			if(!empty($bookingTill) && $date < $currentDate){
				$currentTime = time();
				$bookingTillTime = strtotime($bookingTill);
				if ($currentTime > $bookingTillTime) {
					continue;
				}
			}
			$timeSlotSeq = $timeSlot["timeslotseq"];
			
			$bookedSeats = $bookingMgr->getAvailableSeats($dateStr, $timeSlotSeq);
			$arr = array();
			$arr["seq"] = $timeSlotSeq;
			$arr["timeslot"] = $timeSlot["timeslot"];
			$arr["time"] = $timeSlot["time"];
			$totalSeats = $timeSlot["seats"];
            $arr["seats"] = $totalSeats;
			$arr["description"] = $timeSlot["description"];
			$arr["msg"] = self::getMsg();
			
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
			//if($dayName == "Fri" || $dayName == "Sat" || $dayName == "Sun"){
				//$timeSlot["rate"] += 1000;
			//}
			$menuPricingArr = null;
			if(!empty($menuPricings) && array_key_exists($timeSlot["menuseq"], $menuPricings)){
				$menuPricingArr = $menuPricings[$timeSlot["menuseq"]];
			}
			$rate = $this->getMenuPrice($date, $menuPricingArr, $timeSlot["rate"]) ;
			
			//temp code to inflate and rebate
				$menu["discountedRate"] = number_format((float)round($rate), 2, '.', '') ;
				$rate = ($rate) + ($rate * 40/100);
			//temp code ends
			
			$menu["rate"] = number_format((float)round($rate), 2, '.', '') ;
			$menu["menuseq"] = $timeSlot["menuseq"];
			
			array_push($mainMenuArr, $menu);
			$arr["menu"] = $mainMenuArr;
			$slotArr[$timeSlotSeq] = $arr;
		}
		$json = json_encode($slotArr);
		return $json;
	}
	
	private function getMenuPrice($date,$menuPricingArr,$rate){
		if(!empty($menuPricingArr)){
			foreach($menuPricingArr as $menuPricing){
				$pricingDate = $menuPricing->getDate();
				$price = $menuPricing->getPrice();
				$pricingDateObj = DateUtil::StringToDateByGivenFormat("Y-m-d", $pricingDate);
				$pricingDateObj = $pricingDateObj->setTime(0, 0);
				if($pricingDateObj == $date){
					return $price;
				}
			}
		}
		return $rate;
	}
	
	private function getMsg(){
		$randomNumbers = array();
		for($i=1;$i<=15;$i++){
			$randomNumbers[$i] = $i;
		}
		
		$str1 = '<br><h4><i class="fa fa-clock-o" aria-hidden="true"></i><small> Booked '.array_rand($randomNumbers,1) .' times in last 24 hrs</small></h4>';
		$str2 = '<br><h4><i class="fa fa-clock-o" aria-hidden="true"></i><small> Latest Booking: Yesterday</small></h4>';
		$str3 = '<br><h4><i class="fa fa-clock-o" aria-hidden="true"></i><small> Latest Booking: 10mins ago</small></h4>';
		$str4 = '<br><h4 class="text-danger"><i class="fa fa-clock-o" aria-hidden="true"></i><small class="text-danger"> Just Booked</small></h4>';
		$str5 = '<br><h4 class="text-danger"><i class="fa fa-clock-o" aria-hidden="true"></i><small class="text-danger"> Booking Fast</small></h4>';
		$str6 = '<br><h4 class="text-danger"><i class="fa fa-clock-o" aria-hidden="true"></i><small class="text-danger"> High in demand</small></h4>';
		$str7 = '<br><h4><i class="fa fa-clock-o" aria-hidden="true"></i><small> '.array_rand($randomNumbers,1) .' people watching</small></h4>';
		$str8 = '<br><h4><i class="fa fa-clock-o" aria-hidden="true"></i><small> '.array_rand($randomNumbers,1) .' people watching</small></h4>';
		$str9= "";
		$str10= "";
		$arr = array($str1,$str2,$str3,$str4,$str5,$str6,$str7,$str8,$str9,$str10);
		$random_keys=array_rand($arr,1);
		return $arr[$random_keys];
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
			$arr["availabletill"] = $timeSlot->getBookingAvailableTill();
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
