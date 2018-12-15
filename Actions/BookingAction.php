<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Enums/BookingStatus.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingDetailMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/MailUtil.php");
require_once ($ConstantsArray ['dbServerUrl'] . "log4php/Logger.php");
Logger::configure ( $ConstantsArray ['dbServerUrl'] . "log4php/log4php.xml" );
$logger = Logger::getLogger ( "logger" );

$call = "";
$data = array();
if(isset($_GET["call"])){
	$call = $_GET["call"];
	$data = $_GET;
}else{
	$call = $_POST["call"];
	$data = $_POST;
}
$success = 1;
$message = "";
if($call == "saveBooking"){
	try{
		$data = json_encode($data);
		$logger->info("Booking Save initialize with request data - " . $data);
        $bookingMgr = BookingMgr::getInstance();
		$bookingDetailMgr = BookingDetailMgr::getInstance();
		$rescheduleBookingId = 0;
		if(isset($_POST["rescheduleBookingId"]) && !empty($_POST["rescheduleBookingId"])){
			$rescheduleBookingId = $_POST["rescheduleBookingId"];
		}
		$timSlotSeq = $_POST["timeslotseq"];
		$mobile = $_POST["mobile"];
		$emailId = $_POST["email"];
		$fullName = $_POST["fullName"];
		$selectedDate = $_POST["selectedDate"];
		$menuPersonsStr = $_POST["menuMembers"];
		$menuPriceStr = $_POST["menuPrice"];
		$tansactionId = $_POST["transactionId"];
		$amount = $_POST["amount"];
		$companyNumber = "";
		$companyMobile = "";
		$companyName = "";
		$country = $_POST["country"];
		$dateOfBirth = $_POST["dateofbirth"];
		$couponSeq = $_POST["couponSeq"]; 
		$discountPercent = $_POST["discountPercent"];
		$dateOfBirth = DateUtil::StringToDateByGivenFormat("d-m-Y", $dateOfBirth);
		$dateOfBirth = $dateOfBirth->setTime(0, 0);
		$gst = "";
		$gstState = "";
		if(isset($_POST["companyInfo"])){
			$companyName = $_POST["companyName"];
			$companyMobile = $_POST["companyNumber"];
			$gst = $_POST["gst"];
			$gstState = $_POST["companyState"];
		}
		$menuPersonsObj = json_decode($menuPersonsStr);
		$menuPriceArr = json_decode($menuPriceStr);
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
		$booking->setCompanyMobile($companyMobile);
		$booking->setCompanyName($companyName);
		$booking->setCouponSeq($couponSeq);
		$booking->setDiscountPercent($discountPercent);
		$booking->setGSTNumber($gst);
		$booking->setGstState($gstState);
		$booking->setCountry($country);
		$booking->setDateOfBirth($dateOfBirth);
		$booking->setParentBookingSeq($rescheduleBookingId);
		$bookingId = $bookingMgr->saveBooking($booking);
		$booking->setSeq($bookingId);
		$bookingDetailMgr->saveBookingDetails($bookingId, $menuPersonsObj,$menuPriceArr);
		if(!empty($rescheduleBookingId)){
			$bookingMgr->updateBookingStatus(BookingStatus::rescheduled, $rescheduleBookingId);
		}
		MailUtil::sendOrderEmailClient($booking,$menuPersonsObj,$menuPriceArr);
		$message = "Booking Saved Successfully";
		session_start();
		$_SESSION["bookingid"] = $bookingId;
		}catch(Exception $e){
			$success = 0;
			$message  = $e->getMessage();
			$logger->error ( "Error occured in BookingAction during Action - saveBooking:" . $e );
		}
		header("Location: ../thankyou.php");
		//$response = new ArrayObject();
		//$response["success"]  = $success;
		//$response["message"]  = $message;
		//echo json_encode($response);
		//return;
}
if($call == "saveBookingsFromAdmins"){
	try{
		$bookingMgr = BookingMgr::getInstance();
		$bookingDetailMgr = BookingDetailMgr::getInstance();
		$seq = $_POST["seq"];
		if(empty($seq)){
			$seq = 0;
		}
		$timeSlotSeq = $_POST["timeSlot"];
		$selectedDate = $_POST["bookingDate"];
		$mobile = $_POST["mobile"];
		$emailId = $_POST["email"];
		$fullName = $_POST["fullName"];
		$companyName = $_POST["companyname"];
		$companyMobile = $_POST["companymobile"];
		$tansactionId = $_POST["paymentid"];
		$gstNo = $_POST["gstno"];
		$gstState = $_POST["companyState"];
		$dateOfBirth = $_POST["dateofbirth"];
		$country = $_POST["country"];
		$dateOfBirth = DateUtil::StringToDateByGivenFormat("d-m-Y", $dateOfBirth);
		$dateOfBirth = $dateOfBirth->setTime(0, 0);
		$menuPerson = $_POST["selectedSeats"];
		$couponSeqAndPercent = $_POST["couponSeq"];
		$couponSeq = 0;
		$couponPercent = 0;
		if(!empty($couponSeqAndPercent)){
			$couponSeqAndPercent = explode("_", $couponSeqAndPercent);
			$couponSeq = $couponSeqAndPercent[0];
			$couponPercent = $couponSeqAndPercent[1];
		}
		$sum = array_sum($menuPerson);
		if(array_sum($menuPerson) == 0){
			return ;
		}
		$amount = $_POST["amount"];
		$totalAmount = 0;
		foreach ($amount as $amt){
			$totalAmount += $amt;
		}
		$bookingStatus = $_POST["status"];
		$parentBookingSeq = $_POST["parentbookingseq"];
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
		$booking->setGSTNumber($gstNo);
		$booking->setSeq($seq);
		$booking->setCompanyMobile($companyMobile);
		$booking->setCompanyName($companyName);
		$booking->setGstState($gstState);
		$booking->setCountry($country);
		$booking->setDateOfBirth($dateOfBirth);
		$booking->setCouponSeq($couponSeq);
		$booking->setDiscountPercent($couponPercent);
		$booking->setStatus($bookingStatus);
		$booking->setParentBookingSeq($parentBookingSeq);
		$bookingId = $bookingMgr->saveBooking($booking);
		$booking->setSeq($bookingId);
		
		$bookingDetailMgr->saveBookingDetail($bookingId, $menuPerson,$amount);
		$message = "Booking Saved Successfully";
	}catch(Exception $e){
		$success = 0;
		$message  = $e->getMessage();
		$logger->error ( "Error occured in BookingAction during Action - saveBookingsFromAdmins :" . $e );
	}
	$response = new ArrayObject();
	$response["success"]  = $success;
	$response["message"]  = $message;
	echo json_encode($response);
	return;
}
// if($call == "saveBookingsFromAdmins"){
// 	try{
// 		$bookingMgr = BookingMgr::getInstance();
// 		$bookingDetailMgr = BookingDetailMgr::getInstance();
// 		$timSlotSeqs = $_POST["timeslotseq"];
// 		$selectedDate = $_POST["bookingDate"];
// 		$mobile = $_POST["mobile"];
// 		$emailId = $_POST["email"];
// 		$fullName = $_POST["fullName"];

// 		$tansactionId = $_POST["paymentid"];
// 		$gstNo = $_POST["gstno"];
// 		foreach ($timSlotSeqs as $timeSlotSeq){
// 			$menuPerson = $_POST[$timeSlotSeq."_selectedSeats"];
// 			$sum = array_sum($menuPerson);
// 			if(array_sum($menuPerson) == 0){
// 				continue;
// 			}
// 			$amount = $_POST[$timeSlotSeq."_amount"];
// 			$totalAmount = 0;
// 			foreach ($amount as $amt){
// 				$totalAmount += $amt;
// 			}
// 			$booking = new Booking();
// 			$bookingDate = DateUtil::StringToDateByGivenFormat("d-m-Y", $selectedDate);
// 			$bookingDate = $bookingDate->setTime(0, 0);
// 			$booking->setBookedOn(new DateTime());
// 			$booking->setBookingDate($bookingDate);
// 			$booking->setEmailId($emailId);
// 			$booking->setFullName($fullName);
// 			$booking->setMobileNumber($mobile);
// 			$booking->setTimeSlot($timeSlotSeq);
// 			$booking->setAmount($totalAmount);
// 			$booking->setTransactionId($tansactionId);
// 			$booking->setGSTNumber($gstNo);
// 			$bookingId = $bookingMgr->saveBooking($booking);
// 			$booking->setSeq($bookingId);
				
				
// 			$bookingDetailMgr->saveBookingDetail($bookingId, $menuPerson);
// 			$message = "Booking Saved Successfully";
// 		}

// 	}catch(Exception $e){
// 		$success = 0;
// 		$message  = $e->getMessage();
// 	}
// 	$response = new ArrayObject();
// 	$response["success"]  = $success;
// 	$response["message"]  = $message;
// 	echo json_encode($response);
// 	return;
// }
if($call == "getBookings"){
	$bookingMgr = BookingMgr::getInstance();
	$bookingJson = $bookingMgr->getBookingJsonForGrid();
	echo $bookingJson;
}

if($call == "deleteBooking"){
	$ids = $_GET["ids"];
	try{
		$bookingMgr = BookingMgr::getInstance();
		$flag = $bookingMgr->deleteBySeqs($ids);
		$message = "Booking(s) Deleted successfully";
	}catch(Exception $e){
		$success = 0;
		$message = ErrorUtil::checkReferenceError(LearningPlan::$className,$e);
		$logger->error ( "Error occured in BookingAction during Action - deleteBooking :" . $e );
	}
	$response = new ArrayObject();
	$response["message"] = $message;
	$response["success"] =  $success;
	echo json_encode($response);
}

if($call == "getBookingDetail"){
	$bookingDetail = array();
	try{
		$bookingId = $_GET["id"];
		$bookingMgr = BookingMgr::getInstance();
		$bookingDetail = $bookingMgr->getBookingDetail($bookingId);
		if(empty($bookingDetail)){
			$success = 0;
			$message = "Booking Not Found";
		}
	}catch (Exception $e){
		$success = 0;
		$logger->error ( "Error occured in BookingAction during Action - getBookingDetail :" . $e );
		$message = $e->getMessage();
	}
	$response = new ArrayObject();
	$response["message"] = $message;
	$response["success"] =  $success;
	$response["bookingDetail"] = $bookingDetail;
	echo json_encode($response);
}



