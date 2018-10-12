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
