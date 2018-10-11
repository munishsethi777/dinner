<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingDetailMgr.php");

require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/MailUtil.php");

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
		$bookingDetailMgr = BookingDetailMgr::getInstance();
		
		$timSlotSeq = $_POST["timeslotSeq"];
		$mobile = $_POST["mobile"];
		$emailId = $_POST["email"];
		$fullName = $_POST["fullName"];
		$selectedDate = $_POST["selectedDate"];
		$menuPersonsStr = $_POST["menuPersons"];
		$tansactionId = "222";//$_POST["transactionId"];
		$amount = "23";//$_POST["amount"];
		$menuPersonsObj = json_decode($menuPersonsStr);
		$booking = new Booking();

		$bookingDate = DateUtil::StringToDateByGivenFormat("d-m-Y", $selectedDate);
		$bookingDate = $bookingDate->setTime(0, 0);
		
		$booking->setBookedOn(new DateTime());
		$booking->setBookingDate($bookingDate);
		$booking->setEmailId($emailId);
		$booking->setFullName($fullName);
		$booking->setMobileNumber($mobile);
		$booking->setTimeSlot($timSlotSeq);
		$booking->setAmount($amount);
		$booking->setTransactionId($tansactionId);
		$bookingId = $bookingMgr->saveBooking($booking);
		$booking->setSeq($bookingId);
		$bookingDetailMgr->saveBookingDetails($bookingId, $menuPersonsObj);
		MailUtil::sendOrderEmailClient($booking,$menuPersonsObj);
		$message = "Booking Saved Successfully";
		}catch(Exception $e){
			$success = 0;
			$message  = $e->getMessage();
		}
		$response = new ArrayObject();
		$response["success"]  = $success;
		$response["message"]  = $message;
		echo json_encode($response);
	return;
}

if($call == "getBookings"){
	$bookingMgr = BookingMgr::getInstance();
	$bookingJson = $bookingMgr->getBookingJsonForGrid();
	echo $bookingJson;
}
