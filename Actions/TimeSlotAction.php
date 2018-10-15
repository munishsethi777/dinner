<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
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
 	$timeSlot->setSeq($seq);
 	$id = $timeSlotMgr->saveTimeSlot($timeSlot,$menus);
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
		$message = ErrorUtil::checkReferenceError(LearningPlan::$className,$e);
	}
	$response = new ArrayObject();
	$response["message"] = $message;
	$response["success"] =  $success;
	echo json_encode($response);
}