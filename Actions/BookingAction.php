<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
$call = "";
if(isset($_GET["call"])){
	$call = $_GET["call"];
}else{
	$call = $_POST["call"];
}
$success = 1;
$message = "";
if($call == "saveBooking"){
	try{
	$bookingMgr = BookingMgr::getInstance();
	$timSlotSeq = $_POST["timeSlotSeq"];
	$mobile = $_POS["mobile"];
	$emailId = $_POST["email"];
	$fullName = $_POST["fullName"];
	$selectedDate = $_POST["selectedDate"];
	$booking = new Booking();
	$bookedOn = DateUtil::StringToDateByGivenFormat("d-m-Y", $selectedDate);
	$booking->setBookedOn($bookedOn);
	$booking->setEmailId($emailId);
	$booking->setFullName($fullName);
	$booking->setMobileNumber($mobile);
	$booking->setTimeSlot($timSlotSeq);
	$bookingMgr->saveBooking($booking);
	$message = "Booking Saved Successfully";
	}catch(Exception $e){
		$success = 0;
		$message  = $e->getMessage();
	}
	$response = new ArrayObject();
	$response["success"]  = $success;
	$response["message"]  = $message;
	echo json_encode($response);
	}

