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
		$tansactionId = $_POST["transactionId"];
		$amount = $_POST["amount"];
		$gst = $_POST["gst"];
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
		$booking->setGSTNumber($gst);
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
if($call == "saveBookingsFromAdmins"){
	try{
		$bookingMgr = BookingMgr::getInstance();
		$bookingDetailMgr = BookingDetailMgr::getInstance();
		$timSlotSeqs = $_POST["timeslotseq"];
		$selectedDate = $_POST["bookingDate"];
		$tansactionId = "";
		foreach ($timSlotSeqs as $timeSlotSeq){
			$mobile = $_POST[$timeSlotSeq."_mobile"];
			$emailId = $_POST[$timeSlotSeq."_email"];
			$fullName = $_POST[$timeSlotSeq."_fullName"];
			$amount = $_POST[$timeSlotSeq."_amount"];
			$totalAmount = 0;
			foreach ($amount as $amt){
				$totalAmount += $amt;
			}
			$booking = new Booking();
			$bookingDate = DateUtil::StringToDateByGivenFormat("d-m-Y", $selectedDate);
			$bookingDate = $bookingDate->setTime(0, 0);
			$booking->setBookedOn(new DateTime());
			$booking->setBookingDate($bookingDate);
			$booking->setEmailId($emailId);
			$booking->setFullName($fullName);
			$booking->setMobileNumber($mobile);
			$booking->setTimeSlot($timeSlotSeq);
			$booking->setAmount($totalAmount);
			$booking->setTransactionId($tansactionId);
			$booking->setGSTNumber("");
			$bookingId = $bookingMgr->saveBooking($booking);
			$booking->setSeq($bookingId);
			$menuPerson = $_POST[$timeSlotSeq."_selectedSeats"];
			$bookingDetailMgr->saveBookingDetail($bookingId, $menuPerson);
			$message = "Booking Saved Successfully";
		}
		
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


