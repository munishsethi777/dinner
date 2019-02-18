<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/SlotDetailMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
$call = "";
if(isset($_GET["call"])){
	$call = $_GET["call"];
}else{
	$call = $_POST["call"];
}

$success = 1;
$message = "";
if($call == "getTimeSlots"){
	$timeSlotMgr = TimeSlotMgr::getInstance();
	$timeSlotsJson = $timeSlotMgr->getTimeSlotsJson();
	echo $timeSlotsJson;
}
if($call == "saveTimeSlot"){
	
 try{
 	$seq = $_POST["seq"];
 	if(empty($seq)){
 		$seq = 0;
 	}
 	$title = $_POST["title"];
 	$description = $_POST["description"];
 	$seats = $_POST["seats"];
 	$menus = $_POST["menus"];
 	
 	$timeSlotMgr = TimeSlotMgr::getInstance();
 	$timeSlot = new TimeSlot();
 	$timeSlot->setDescription($description);
 	$timeSlot->setSeats($seats);
 	$timeSlot->setTitle($title);
 	if(isset($_POST["starton"]) && !empty($_POST["starton"])){
 		$startOn = $_POST["starton"];
 		$startOnDate = DateUtil::StringToDateByGivenFormat("d-m-Y", $startOn);
 		$startOnDate = $startOnDate->setTime(0, 0);
 		$timeSlot->setStartOn($startOnDate);
 	}
 	if(isset($_POST["endon"]) && !empty($_POST["endon"])){
 		$endOn = $_POST["endon"];
 		$endOnDate = DateUtil::StringToDateByGivenFormat("d-m-Y", $endOn);
 		$endOnDate = $endOnDate->setTime(0, 0);
 		$timeSlot->setEndOn($endOnDate);
 	}
 	if(isset($_POST["bookingavailabletill"]) && !empty($_POST["bookingavailabletill"])){
 		$bookingAvailableTill = $_POST["bookingavailabletill"];
 		$bookingAvailableTillTime = date("H:i:s",strtotime($bookingAvailableTill));
 		$timeSlot->setBookingAvailableTill($bookingAvailableTillTime);
 	}
 	$timeSlot->setSeq($seq);
 	$id = $timeSlotMgr->saveTimeSlot($timeSlot,$menus);
 	$slotDetailMgr = SlotDetailMgr::getInstance();
 	if(!empty($id)){
 		$slotDetailMgr->deleteBySlotSeq($id);
 	}
 	if(isset($_POST["hideOnDates"]) && !empty($_POST["hideOnDates"]) && !empty($id)){
 		$hideForDates = explode(",", $_POST["hideOnDates"]);
 		$slotDetailMgr->saveSlotDetail($hideForDates, $id);
 	}
 	$message = "Time Slot saved successfully";
 }catch (Exception $e){
 	$success = 0;
 	$message  = $e->getMessage();
 }
 $response = new ArrayObject();
 $response["success"]  = $success;
 $response["message"]  = $message;
 echo json_encode($response);
}

if($call == "getAllTimeSlots"){
	$timeSlotMgr = TimeSlotMgr::getInstance();
	$timeSlotsJson = $timeSlotMgr->getAllTimeSlotsForGrid();
	echo json_encode($timeSlotsJson);
}

if($call == "deleteTimeSlots"){
	$ids = $_GET["ids"];
	try{
		$timeSlotMgr = TimeSlotMgr::getInstance();
		$flag = $timeSlotMgr->deleteBySeqs($ids);
		$message = "TimeSlot(s) Deleted successfully";
	}catch(Exception $e){
		$success = 0;
		$message = $e->getMessage();
	}
	$response = new ArrayObject();
	$response["message"] = $message;
	$response["success"] =  $success;
	echo json_encode($response);
}