<?php
$docroot1 = $_SERVER["DOCUMENT_ROOT"] ."/booking_old/";
require_once($docroot1."IConstants.inc");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Notification.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/ConfigurationMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "log4php/Logger.php");
Logger::configure ( $ConstantsArray ['dbServerUrl'] . "log4php/log4php.xml" );
require_once($ConstantsArray['dbServerUrl'] ."Utils/MailUtil.php");
function runDinnerCron(){
    date_default_timezone_set("Asia/Kolkata");
	$logger = Logger::getLogger ( "logger" );
    $currentDate = new DateTime();
    $logger->info("Cron Run On - " . $currentDate->format("d-m-y H:i"));
	$configurationMgr = ConfigurationMgr::getInstance();
	$emails = $configurationMgr->getConfiguration(Configuration::$BOOKING_CLOSUR_EMAIL);
	$mobiles =  $configurationMgr->getConfiguration(Configuration::$BOOKING_CLOSUR_MOBILE); 
	if(empty($emails) && empty($mobiles)){
		return;
	}
	$timeSlotMgr = TimeSlotMgr::getInstance();
	$timeSlotSeqs = $timeSlotMgr->getTimeSlotSeqsForNotification();
	if(!empty($timeSlotSeqs)){
		$timeSlotSeqs = array_map(create_function('$o', 'return $o["seq"];'), $timeSlotSeqs);
		$timeSlotSeqs = implode(",", $timeSlotSeqs);
		$bookingMgr = BookingMgr::getInstance();
		$timeSlotMgr = TimeSlotMgr::getInstance();
		$bookings = $bookingMgr->getBookingForClosurNotification($timeSlotSeqs);
		if($bookings){
			$timeSlotBookings = _group_by($bookings, "timeslot");
			$bookingDetailMgr = BookingDetailMgr::getInstance();
			foreach ($timeSlotBookings as $key=>$bookings){
				$timeSlot = $timeSlotMgr->findBySeq($key);
				$timeSlotTitle = $timeSlot->getTitle();
				$html = "<p>Following is the booking summary for the slot $timeSlotTitle<p><br>";
				$menuMembersArr = array();
				$totalMember = 0;
				$menuHtml = "";
				foreach ($bookings as $booking){
					$bookingDateStr = $booking["bookingdate"];
					$bookingSeq = $booking["seq"];
					$bookingDetails = $bookingDetailMgr->getBookingDetailAndMenu($bookingSeq);
					foreach ($bookingDetails as $bookingDetail){
						$menuTitle = $bookingDetail["title"];
						$menuMembers = $bookingDetail["members"];
						$member = 0;
						if(array_key_exists($menuTitle, $menuMembersArr)){
							$member =  $menuMembersArr[$menuTitle];
						}
						$member = $member + $menuMembers;
						$menuMembersArr[$menuTitle] = $member;
						$totalMember += $member;
					}
				}
				$html .= "<p><b>Members : </b>";
				$menuHtml = "";
				foreach ($menuMembersArr as $key=>$menuMember){
					$menuHtml .= "$key X $menuMember,";
				}
				$html .= rtrim($menuHtml,',');
				$html .= "</p>";
				$html .= "<p><b>Total Members : </b>$totalMember";
				$bookingDate = DateUtil::StringToDateByGivenFormat("Y-m-d h:i:s", $bookingDateStr);
				$bookingDate = $bookingDate->format('M d, Y');
				$html .= "<p><b>Date : </b>$bookingDate</p>";
				$html .= "<p><b>Time Slot : </b>$timeSlotTitle</p>";
				$sms = "Booked $menuHtml for $bookingDate - $timeSlotTitle ";
				$subject = "Booking summary for $bookingDate -  $timeSlotTitle";
				MailUtil::sendBookingClosurNotification($html, $sms, $subject,$emails,$mobiles,$timeSlot);
                $logger->info("Notification Sent Successfully to - " . $emails);
                
			}
		}
	}else{
		$logger->info("Time Slot Seqs Not Found for notifications");
		return;
	}
}
function _group_by($array, $key) {
	$return = array();
	foreach($array as $val) {
		$return[$val[$key]][] = $val;
	}
	return $return;
}
