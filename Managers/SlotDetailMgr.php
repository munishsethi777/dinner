<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/SlotDetail.php");
require_once($ConstantsArray['dbServerUrl'] ."Enums/SlotDetailAction.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
class SlotDetailMgr{
	private static $SlotDetailMgr;
	private static $dataStore;
	private static $menuTimeSlotDataStore;
	private static $sessionUtil;

	public static function getInstance()
	{
		if (!self::$SlotDetailMgr)
		{
			self::$SlotDetailMgr = new SlotDetailMgr();
			self::$dataStore = new BeanDataStore(SlotDetail::$className, SlotDetail::$tableName);
		}
		return self::$SlotDetailMgr;
	}
	
	public function saveSlotDetail($dates,$slotSeq){
		foreach ($dates as $date){
			$slotDetail = new SlotDetail();
			$slotDetail->setAction(SlotDetailAction::hidden);
			$slotDetail->setSlotSeq($slotSeq);
			$dateObj = DateUtil::StringToDateByGivenFormat("d-m-Y", $date);
			$dateObj = $dateObj->setTime(0, 0);
			$slotDetail->setDate($dateObj);
			self::$dataStore->save($slotDetail);
		}
	}
	
	
	public function findDatesBySlotSeq($slotSeq){
		$colval["slotseq"] = $slotSeq;
		$slotDetails = self::$dataStore->executeConditionQuery($colval);
		$dates = null;
		if(!empty($slotDetails)){
			$dateArr = array();
			foreach ($slotDetails as $slotDetail){
				$date = $slotDetail->getDate();
				$dateObj = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s", $date);
				$formatedDate = $dateObj->format("d-m-Y");
				array_push($dateArr, $formatedDate);
			}
			$dates = implode(",", $dateArr);
		}
		return $dates;
	}
	
	public function deleteBySlotSeq($slotSeq){
		$colval["slotseq"] = $slotSeq;
		$flag = self::$dataStore->deleteByAttribute($colval);
		return $flag;
	}
}