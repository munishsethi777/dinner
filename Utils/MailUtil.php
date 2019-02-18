<?php
require_once('class.phpmailer.php');
//require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/ConfigurationMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/NotificationMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingAddOnMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/PackageMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."vendor/autoload.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/html2PdfUtil.php");
Logger::configure ( $ConstantsArray ['dbServerUrl'] . "log4php/log4php.xml" );
require_once ($ConstantsArray ['dbServerUrl'] . "log4php/Logger.php");
require_once($ConstantsArray['dbServerUrl'] ."StringConstants.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/SMSUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."Enums/NotificationStatus.php");
require_once($ConstantsArray['dbServerUrl'] ."Enums/NotificationType.php");
class MailUtil{
	private static $logger;
	public static function sendOrderEmailClient($booking,$menuPersonsObj,$menuPriceArr,$bookingAddOn){
		self::$logger = Logger::getLogger ( "logger" );
		self::$logger->info("sending sendOrderEmailClient email... ");
		$timeSlotMgr = TimeSlotMgr::getInstance();
		$menuMgr = MenuMgr::getInstance();
		$timeSlot = $timeSlotMgr->findBySeq($booking->getTimeSlot());
		$menus = $menuMgr->findAll();
		$menuPersonArr = array();
		$parentBookingSeq = $booking->getParentBookingSeq();
		$isRescheduled = false;
		$earlierPaidAmount = 0;
		$inconvenienceCharges = 0;
		$inconveniencePercent = 20;
		if(!empty($parentBookingSeq)){
			$bookingMgr = BookingMgr::getInstance();
			$rescheduleBooking = $bookingMgr->getBookingDetail($parentBookingSeq);
			$bookingAddOnMgr = BookingAddOnMgr::getInstance();
			$bookingAddOn = $bookingAddOnMgr->findByBookingSeq($parentBookingSeq);
			if(!empty($bookingAddOn)){
				$cakeAmount = $bookingAddOn->getPrice();
			}
			$earlierPaidAmount = $rescheduleBooking["amount"] + $cakeAmount;
			if($earlierPaidAmount > 0){
				$inconvenienceCharges = ($inconveniencePercent / 100) * $earlierPaidAmount;
			}
		}
		foreach($menus as $menu){
			$menuSeq = $menu->getSeq();
			foreach($menuPersonsObj as $key=>$value){
				if($value == null || $value == 0){
					continue;
				}
				if($menuSeq == $key){
					$titleAndMembers = array("members"=>$value,"title"=>$menu->getTitle());
					$menuPersonArr[$menu->getSeq()]=$titleAndMembers;
				}
			}	
		}
		$packageSeq = $booking->getPackageSeq();
		$package = null;
		if(!empty($packageSeq)){
			$packageMgr = PackageMgr::getInstance();
			$package = $packageMgr->findArrBySeq($packageSeq);
		}
		$bookingDate = $booking->getBookingDate()->format('M d, Y');
		$discountPercent = $booking->getDiscountPercent();
		$discountAmount = $booking->getDiscountAmount();
		$notes = $booking->getNotes();
		$html ='<html><head><link rel="stylesheet" type="text/css"href="1https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"></head>
		<body><divstyle="background-color: grey; width: 100%; color: #676a6c; font-family: open sans, Helvetica Neue, Helvetica, Arial, sans-serif">
		<div style="background-color: white; margin: auto; max-width: 600px; padding: 0px 15px 0px 15px">
		<div style="padding: 15px; background-color: #025e9c; color: white; margin: 0px -15px 0px -15px;">
		<h1 style="margin-top: 0px; margin-bottom: 20px;"><img src="https://1ceipl3l02ez252khj15lgak-wpengine.netdna-ssl.com/wp-content/uploads/2018/10/logo-white-small.png" style="float: left;">
		</h1><p align="right" style="margin: 0px;"><a href="https://www.flydining.com/" style="color: #fff; text-decoration: none;">www.flydining.com</a>
		</p></div><div style="margin-top: 20px;display:flex;padding:15px;">
				<div style="max-width: 25%">
					<img
						src="https://static1.squarespace.com/static/597aa449d482e9e56c97117c/t/59e6cc69a9db0951565bdd72/1508297874678/green+checkmark+Large.png?format=300w"
						width="100%">
				</div>
				<div style="padding:20px 15px;max-width: 75%">
					<p style="margin: 0px; font-size: 24px;">Thank you for your
						booking!</p>
					<p
						style="font-size: 24px; font-weight: bold; margin: 0px; color: #000;">Order
						Id: '.$booking->getBookingId().'</p>
				</div>
			</div><div style="margin: 20px 0 0;display:flex">
				<div style="border-right: 1px solid #f1f1f1;max-width:50%;padding:0px 15px;">
					<h2 style="color: #000;">Flydining</h2>
					<p style="margin: 0px;">Sky Lounge, 24/10, M M Reddy Layout,
						Mariyanpally, Beside Nagavara Lake, Kempapura Main Road, Hebbal,
						Bengaluru, Karnataka - 560024, India</p>
					<p style="margin: 0px;">
						<span style="font-weight: bold;">GST#:</span> 29ADHFS4111J1ZY
					</p>
					<br>
					<p>
						to,<br> <span style="font-weight: bold;">'.$booking->getFullName().'</span><br>'.$booking->getMobileNumber().'<br>' .$booking->getEmailId().
					'</p>
				</div>
				<div style="margin-bottom: 20px;max-width:50%;padding:0px 15px;">';
					$totalPerson = 0;
					foreach($menuPersonArr as $key=>$titleAndMembers){
						$html .='<p style="font-weight: bold; font-size: 21px; color: #000; margin-bottom: 0px;">'.$titleAndMembers["members"]. 'x ' .$titleAndMembers["title"].'</p>';
					}
					
					$html .= '<br>
					<p style="margin: 0px;">Venue:</p>
					<a
						href="https://www.google.co.in/maps/dir//13.0424729,77.6135216/13.0464791,77.6115609/Fly+Dining,+Nagwara+Backside+Lumbini+Garden,+Bengaluru,+Karnataka+560024/@13.0436273,77.6099459,1255m/data=!3m1!1e3!4m11!4m10!1m0!1m0!1m0!1m5!1m1!1s0x3bae173340c3be35:0x19af15ffe521aced!2m2!1d77.6110824!2d13.0471114!3e0">Get
						direction</a><br> <br>
					<p style="margin: 0px;">Time:</p>
					<p style="font-weight: bold; color: #000; font-size: 18px;">'.$timeSlot->getTitle().' | ' . $bookingDate . '</p>
				</div>
			</div><div style="background: #D8F5F5; margin: 0px; padding: 15px;display:flex">
				<p style="margin: 0px;">
					Dear '.$booking->getFullName().',<br> <br> Thank you for choosing Flydining. We will do
					our best to make this experience phenomenal for you. We look
					forward to see you.
				</p>
			</div><div style="margin: 0; background: #f1f1f1; padding: 15px;display:flex">
				<div style="width:100%" class="col-sm-12">
					<p
						style="margin: 0 0 10px 0; font-weight: bold; border-bottom: 1px silver solid;">Order
						Summary</p>
					<table style="width:100%;text-align:left;font-size:12px;vertical-align:top;border-bottom:1px silver solid;">
						<tr>
							<th width="30%" style="text-align:left;vertical-align:top">Item</th>
							<th width="15%" style="text-align:right;vertical-align:top">Rate</th>
							<th width="10%" style="text-align:right;vertical-align:top">Qty</th>
							<th width="15%" style="text-align:right;vertical-align:top">Gross Amt</th>
							<th width="15%" style="text-align:right;vertical-align:top">CGST<br>(+2.5%)</th>
							<th width="15%" style="text-align:right;vertical-align:top">SGST<br>(+2.5%)</th>
						</tr>';
					$totalAmount = 0;
					foreach($menuPersonArr as $key=>$value){
						$rate = $menuPriceArr->$key;
						$amount = $rate * $value["members"];
						$amountWithoutTax = floor(($amount * 100) / 105);
						$taxAmount = $amount - $amountWithoutTax;
						$tax = $taxAmount / 2;
						$rate = $amountWithoutTax / $value["members"];
					
						$amountWithoutTax = number_format($amountWithoutTax,2,'.','');
						$amount = number_format($amount,2,'.','');
						$rate = number_format($rate,2,'.','');
						$tax = number_format($tax,2,'.','');
						$html .= '<tr>
							<td width="30%" style="text-align:left;vertical-align:top">'.$value["title"].'</td>
							<td width="15%" style="text-align:right;vertical-align:top">'.$rate.'</td>
							<td width="10%" style="text-align:right;vertical-align:top">'.$value["members"].'</td>
							<td width="15%" style="text-align:right;vertical-align:top">'.$amount.'</td>
							<td width="15%" style="text-align:right;vertical-align:top">'.$tax.'</td>
							<td width="15%" style="text-align:right;vertical-align:top;padding-bottom:30px">'.$tax.'</td>
							</tr>';
						$totalAmount += $amount;
					}
				   $netAmount = $totalAmount;
				   $totalAmount = number_format($totalAmount,2,'.','');
				   $html .='</table>';
				   	if(!empty($discountPercent) || !empty($discountAmount)){	
				   		if(!empty($discountPercent)){
							$discount = ($discountPercent / 100) * $totalAmount;
				   		}else{
				   			$discount = $discountAmount;
				   		}
				   		if($discount > $netAmount){
				   			$netAmount = 0;
				   		}else{
				   			$netAmount = $netAmount - $discount;
				   		}
						$discount = number_format($discount,2,'.','');
						$html .='<div style="display:flex;width:100%">
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:left">
							<p style="color: #000; font-size: 16px; margin: 0px;">Gross Total</p>
						</div>
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:right;">
							<p style="color: #000; font-size: 16px; text-align: right; margin: 0px;">'.$totalAmount.'/-</p>
						</div>
					</div><div style="display:flex;width:100%;border-bottom:1px silver solid;padding-bottom:10px;">
							<div style="width:50%;padding:10px 0px 0px 0px;text-align:left">
								<p style="color:red; font-size: 16px; margin: 0px;">Discount</p>
							</div>
							<div style="width:50%;padding:10px 0px 0px 0px;text-align:right;">
								<p style="color:red; font-size: 16px; text-align: right; margin: 0px;">'.$discount.'/-</p>
							</div>
						</div>';
					}
					if(!empty($earlierPaidAmount)){
						$netAmount = $netAmount + $inconvenienceCharges;
						$netAmount = $netAmount - $earlierPaidAmount;
						$inconvenienceCharges = number_format($inconvenienceCharges,2,'.','');
						$earlierPaidAmount = number_format($earlierPaidAmount,2,'.','');
						$html .='<div style="display:flex;width:100%">
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:left">
							<p style="color: red; font-size: 16px; margin: 0px;">Inconvenience Charges(20%)</p>
						</div>
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:right;">
							<p style="color: red; font-size: 16px; text-align: right; margin: 0px;">'.$inconvenienceCharges.'/-</p>
						</div>
					</div><div style="display:flex;width:100%;border-bottom:1px silver solid;padding-bottom:10px;">
							<div style="width:50%;padding:10px 0px 0px 0px;text-align:left">
								<p style="color:green; font-size: 16px; margin: 0px;">Earlier Paid Amount</p>
							</div>
							<div style="width:50%;padding:10px 0px 0px 0px;text-align:right;">
								<p style="color:green; font-size: 16px; text-align: right; margin: 0px;">'.$earlierPaidAmount.'/-</p>
							</div>
						</div>';
					}
					if(!empty($bookingAddOn)){
						$cakePrice = $bookingAddOn->getPrice();
						$netAmount = $netAmount + $cakePrice;
						$cakePrice = number_format($cakePrice,2,'.','');
						$html .='<div style="display:flex;width:100%">
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:left">
							<p style="color: #000; font-size: 16px; margin: 0px;">Cake Charges</p>
						</div>
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:right;">
							<p style="color: #000; font-size: 16px; text-align: right; margin: 0px;">'.$cakePrice.'/-</p>
						</div>
					</div>';
					}
					if(!empty($package)){
						$packagePrice = $package["price"];
						$packageName = $package["occasion"] ."-" .$package["title"];
						$packDesc = $package["description"];
						$netAmount = $netAmount + $packagePrice;
						$packagePrice = number_format($packagePrice,2,'.','');
						$html .='<div style="display:flex;width:100%">
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:left">
							<p style="color: #000; font-size: 16px; margin: 0px;">Package('.$packageName.')</p>
							<p style="color: #000; font-size: 13px; margin: 0px;">'.$packDesc.'</p>
						</div>
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:right;">
							<p style="color: #000; font-size: 16px; text-align: right; margin: 0px;">'.$packagePrice.'/-</p>
						</div>
					</div>';
					}
					$netAmount = number_format($netAmount,2,'.','');
					$html .='<div style="display:flex;width:100%">
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:left">
							<p
								style="font-weight: bold; color: #000; font-size: 21px; margin: 0px;">Net Payable Amount</p>
						</div>
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:right;">
							<p style="font-weight: bold; color: #000; font-size: 21px; text-align: right; margin: 0px;">Rs.'.$netAmount.'/-</p>
						</div>
					</div>';
					if(!empty($notes)){
						$html .='<div style="border-top:1px silver solid;display:flex;width:100%">
						<div style="width:50%;padding:10px 0px 0px 0px;text-align:left">
							<p style="color: #000; font-size: 12px; margin: 0px;">Booking Notes : '.$notes.'</p>
						</div>
					</div>';
					}
				$html .='</div>
			</div><div style="margin: 10px 0 0;">
				<p><h4 style="display:block">Important Instructions</h4></p>
				<ul style="font-size: 12px;">
					<li>Be on time- at least a minimum of 60 minutes before your table
						reservation. If you are late for your reservation, no refund will
						be given and no rescheduling will be allowed.</li>
					<li>Dress comfortably. Avoid wearing loose shoes.</li>
					<li>Do not bring large handbags or umbrellas etc with you. We have
						limited storage/locker space.</li>
					<li>Do not make other plans for the night. Depending on the
						weather, we might delay your experience, to ensure you enjoy the
						beautiful Flydining In India.</li>
					<li>Do not bring valuables to the experience.</li>
					<li>Please bring along your Identification Card/ Passport for
						verification purpose.</li>
					<li>Remember, this experience is strictly for individuals that are
						13 years old and above, with a minimum height 145cm and a maximum
						weight of 150kg. Anyone below the age of 18 MUST be accompanied by
						an adult or guardian, or not be allowed to board. Pregnant women
						are also not allowed to experience Flydining In India.</li>
					<li>The ticket is NOT-TRANSFERABLE in anyway and any changes of
						Dinner(s) name/information/menu is not allowed.</li>
					<li>If you are lost or need direction, please call
						+91-769-81-81-000. Kindly take note that we are unable to delay
						the experience for you if you call or message us on this number so
						please do not be late.</li>
					<li>You may reschedule your booking, 20% of the booking amount will be charged as inconvenience fees. <a href="http://www.flydining.com/booking/reschedule.php">Click here</a> to reschedule.</li>
				</ul>
			</div>
			<p align="center" style="font-size: 21px;">For session pictures please contact or whatsapp 	+918130540906</p>
			<div>
				<div align="center">
					<a
						href="https://play.google.com/store/apps/details?id=com.ni.FlyDining"><img
						src="https://1ceipl3l02ez252khj15lgak-wpengine.netdna-ssl.com/wp-content/uploads/2018/10/global-playstore.png"
						width="30%" style="margin: 0 0 10px;"></a>&nbsp;<a
						href="https://www.apple.com/in/ios/app-store/"><img
						src="https://1ceipl3l02ez252khj15lgak-wpengine.netdna-ssl.com/wp-content/uploads/2018/11/Untitled-1.png"
						width="30%" style="margin: 0 0 10px;"></a>
				</div>
			</div>

			<div style="margin: 0 0 10px;">
				<div align="center">
					<p style="margin: 20px 0 0">
						Follow us on: <a href="https://www.facebook.com/flydining"><img
							src="https://ci4.googleusercontent.com/proxy/FwzcgIsBuYYL2YTPtfI17st2JQb_oFfserSYPNYEOqY35orlheD8cNvdNvgNzQsgGwSWzuBV5maD9eE8vsEsiH46fO3YshqOF8zTb0FWKJlDorF7=s0-d-e1-ft#https://in.bmscdn.com/mailers/images/160720bmsreview/facebook.png"></a><a
							href="https://twitter.com/FlyDining"><img
							src="https://ci4.googleusercontent.com/proxy/jcI7YLl108K8e8tJyok7swAAE_aD5vFR0LOGWfm8Nc4DR1fw3X9dkzFnUH_GtyZUMdl8Fjzsa3Y6q_EsJbBzVvu3CMsBNmXxO7SyKKvTXdf9Gas=s0-d-e1-ft#https://in.bmscdn.com/mailers/images/160720bmsreview/twitter.png"></a><a
							href="https://www.youtube.com/channel/UCtmt1xDuJ7_chvSi2jVGiBA"><img
							src="https://ci6.googleusercontent.com/proxy/hzZdxXsakGn7dEyoV3EM33eIpl_IpLrvpENCm3EEQ9y9As_CUFjK-yQj3u1sYsyR3KEA8Zyi-wQD2hF5LFwin6FoDrlaNmaghtEfBg9FvelnBJI=s0-d-e1-ft#https://in.bmscdn.com/mailers/images/160720bmsreview/youtube.png"
							style="padding-left: 5px;"></a><a
							href="https://www.instagram.com/fly.dining/"><img
							src="https://ci3.googleusercontent.com/proxy/hC6s2VDAbzueVXNl2_3gaIwpjdNwR9JBIU0vdpBrHiln9bzJP7yxv8ACczjzHZcnEiKzH037w_sBCM-YYok8vdYByCLBr4YYq1fNSpCBdBYnjzzFYA=s0-d-e1-ft#https://in.bmscdn.com/mailers/images/160720bmsreview/instagram.png"
							style="padding-left: 5px;"></a>
					</p>
					<p style="margin: 20px 0 0;color:red">
						Get reinvited! Write us a review on Google and the best reviewer wins a FlyDining experience for free!
					</p>
				</div>
			</div>


			<p
				style="background: #025e9c; margin-top: 1 0px; color: #fff; padding: 10px;"
				align="center">
				For any further assistance, mail to blr@flydining.com<br>or call:<b>769
					81 81 000, 8130 540 906</b>
			</p>
					</div>
				</div>
			</body>
			</html>';
			$subject = "YOUR FLY DINING BOOKING CONFIRMATION.";
			$emails = array(0=>$booking->getEmailId());
			
			
			$attachments = self::getAttachments($booking,$menuPersonArr,$menuPriceArr,$timeSlot,$bookingAddOn,$inconvenienceCharges,$earlierPaidAmount,$package);
			MailUtil::sendSmtpMail($subject, $html, $emails,StringConstants::IS_SMTP,$attachments);
			$emails = StringConstants::EMAIL_IDS;
			if(!empty($emails)){
				$emails = explode(",", $emails);
				MailUtil::sendSmtpMail($subject, $html, $emails,StringConstants::IS_SMTP);
			}
			MailUtil::sendBookingConfirmSMS($booking, $timeSlot);
			MailUtil::saveCakeOrderNotification($booking, $bookingAddOn, $timeSlot, $menuPersonArr);
	}
	
	private static function sendBookingConfirmSMS($booking,$timeSlot){
		$bookingDate = $booking->getBookingDate()->format('M d, Y');
		$bookingId = $booking->getBookingId();
		$timeSlotTitle = $timeSlot->getTitle();
		$msg = "Your FlyDining booking ID - $bookingId for $bookingDate @ $timeSlotTitle is confirmed. Pls reach 30 minutes before your timeslot. Route & Location - https://goo.gl/rwzvQ8";
		$smsUtil = SMSUtil::getInstance();
		$smsUtil->sendSMS($booking->getMobileNumber(), $msg);
	}
	
	private static function getAttachments($booking,$menuPersonArr,$menuPriceArr,$timeSlot,$bookingAddOn,$inconvenienceCharges,$earlierPaidAmount,$package){
		$invoiceAttachment = self::getInvoiceAttachments($booking, $menuPersonArr, $menuPriceArr,$bookingAddOn,$inconvenienceCharges,$earlierPaidAmount,$package);
		//$confimrationAttachment = self::getBookingConfirmationAttahment($booking, $menuPersonArr,$timeSlot);
		$attachemtns = array("Invoice"=>$invoiceAttachment);
		return $attachemtns;
	}
	
	
	private static function sendNotificationToCakeVendor($booking,$bookingAddOn,$timeSlot,$menuPersonArr){
		if(!empty($bookingAddOn)){
			$configurationMgr = ConfigurationMgr::getInstance();
			$emails = $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_EMAIL);
			$mobiles =  $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_MOBILE);
			$Content =  $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_MESSAGE);
			$bookingDate = $booking->getBookingDate()->format('M d, Y');
			$cakeDetail = $bookingAddOn->getNotes();
			$timeSlotTitle = $timeSlot->getTitle();
			if(!empty($emails)){
				$html = "<p>".$Content."</p>";
				$html .= "<p><b>Cake Details : </b>$cakeDetail</p>";
				$html .= "<p><b>Booking For Date : </b>$bookingDate</p>";
				$html .= "<p><b>Booking TimeSlot : </b>$timeSlotTitle</p>";
				$members = 0;
				foreach($menuPersonArr as $key=>$titleAndMembers){
						$members += intval($titleAndMembers["members"]);
				}
				$html .="<p><b>Total Members : </b>$members</p>";
				$emails = explode(",", $emails);
				$subject = "CAKE BOOKING FOR FLY DINING BOOKING";
				MailUtil::sendSmtpMail($subject, $html, $emails,StringConstants::IS_SMTP);
			}
			if(!empty($mobiles)){
				$msg = "Cake booking for $bookingDate for timeslot $timeSlotTitle. Total Members -$members .  Details: $cakeDetail";
				$smsUtil = SMSUtil::getInstance();
				$smsUtil->sendSMS($mobiles, $msg);
			}
		}
	}

	
	public static function saveCakeOrderNotification($booking,$bookingAddOn,$timeSlot,$menuPersonArr){
		if(!empty($bookingAddOn)){
			$configurationMgr = ConfigurationMgr::getInstance();
			$emails = $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_EMAIL);
			$mobiles =  $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_MOBILE);
			$Content =  $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_MESSAGE);
			$bookingDate = $booking->getBookingDate()->format('M d, Y');
			$cakeDetail = $bookingAddOn->getNotes();
			$timeSlotTitle = $timeSlot->getTitle();
			$notification = null;
			if(!empty($emails) || !empty($mobiles) ){
				$notification = new Notification();
			}
			if(!empty($emails)){
				$html = "<p>".$Content."</p>";
				$html .= "<p><b>Cake Details : </b>$cakeDetail</p>";
				$html .= "<p><b>Booking For Date : </b>$bookingDate</p>";
				$html .= "<p><b>Booking TimeSlot : </b>$timeSlotTitle</p>";
				$members = 0;
				foreach($menuPersonArr as $key=>$titleAndMembers){
					$members += intval($titleAndMembers["members"]);
				}
				$html .="<p><b>Total Members : </b>$members</p>";
				$subject = "CAKE BOOKING FOR FLY DINING BOOKING";
				$notification->setEmailId($emails);
				$notification->setSentOn(new DateTime());
				$notification->setTimeSlotSeq($timeSlot->getSeq());
				$notification->setBookingSeq(0);
				$notification->setStatus(NotificationStatus::pending);
				$notification->setNotificationType(NotificationType::cakeOrder);
				$notification->setEmailHtml($html);
				$notification->setEmailSubject($subject);
			}
			if(!empty($mobiles)){
				$msg = "Cake booking for $bookingDate for timeslot $timeSlotTitle. Total Members -$members .  Details: $cakeDetail";
				$notification->setMobileNo($mobiles);
				$notification->setSMSText($msg);
			}
			$notificationMgr = NotificationMgr::getInstance();
			$id = $notificationMgr->saveNotification($notification);
		}
	}
	
	
	
	private static function getInvoiceAttachments($booking,$menuPersonArr,$menuPriceArr,$bookingAddOn,$inconvenienceCharges,$earlierPaidAmount,$package){
		$bookingDate = $booking->getBookedOn()->format('M d, Y, h:i:s a');
		$html = '<table style="width:100%;margin:auto;border:0px silver solid;padding:0px;font-family:arial;
				line-height:20px" >
			<tr>
				<td style="width:50%;padding:10px;border:1px silver solid">Fly Dining<br>
					#24/10, Nagwara  Backside Lumbini Garden,<br> 
					Bengaluru, Karnataka - 560024, INDIA <br>
					<b>Phone :</b> +91-81325-40906<br>
					<b>Email :</b> nivedika@flydining.com<br>
					<b>Bill No :</b> '.$booking->getBookingId().'<br>
					<b>Date :</b> '.$bookingDate .'<br>
					
					<b>GST No :</b> 29ADHFS4111J1ZY<br>
					<b>Company Name :</b> Sky Lounge<br>
					<b>Company Address :</b> 24/10, M M Reddy Layout, Mariyanpally, Beside Nagavara Lake,<br> 
							Kempapura Main Road, Hebbal, Bengaluru, Karnataka - 560024, INDIA<br>
					<b>Company Registered State :</b> Karnataka<br>
					
				</td>
				<td valign="top" style="width:50%;padding:10px;border:1px silver solid;">
					<b>Customer Name :</b> '.$booking->getFullName().'<br>
					<b>Contact No :</b> '.$booking->getMobileNumber().'<br>
					<b>Email :</b> '.$booking->getEmailId().'<br>
				</td>
			</tr>
		</table>
		<table style="width:100%;margin:auto;border:0px silver solid;padding:0px;font-family:arial;line-height:30px" cellspacing="0" cellpadding="0">
			<tr>
				<th style="padding:10px;border:1px silver solid;margin:0px;width:20%">Item</th>
				<th style="padding:10px;border:1px silver solid;margin:0px;width:10%">Rate</th>
				<th style="padding:10px;border:1px silver solid;margin:0px;width:10%">Qty</th>
				<th style="padding:10px;border:1px silver solid;margin:0px;width:10%">Gross Amount</th>
				<th style="padding:10px;border:1px silver solid;margin:0px;width:10%">CGST Rate</th>
				<th style="padding:10px;border:1px silver solid;margin:0px;width:10%">CGST Amount</th>
				<th style="padding:10px;border:1px silver solid;margin:0px;width:10%">SGST Rate</th>
				<th style="padding:10px;border:1px silver solid;width:10%">SGST Amount</th>
				<th style="padding:10px;border:1px silver solid;width:10%">Total Amount</th>
			</tr>';
			$totalAmount = 0;
			foreach($menuPersonArr as $key=>$value){
				$rate = $menuPriceArr->$key;
				$amount = $rate * $value["members"];
				$amountWithoutTax = floor(($amount * 100) / 105);
				$taxAmount = $amount - $amountWithoutTax;
				$tax = $taxAmount / 2;
				$rate = $amountWithoutTax / $value["members"];
				
				$amountWithoutTax = number_format($amountWithoutTax,2,'.','');
				$amount = number_format($amount,2,'.','');
				$rate = number_format($rate,2,'.','');
				$tax = number_format($tax,2,'.','');
				$html .='<tr style="font-size:13px">
					<td style="padding:10px;border:1px silver solid;width:20%">'.$value["title"].'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;width:10%">'.$rate.'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;width:10%">'.$value["members"].'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;width:10%">'.$amountWithoutTax.'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;width:10%">2.5%</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;width:10%">'.$tax.'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;width:10%">2.5%</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;width:10%">'.$tax.'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;width:10%">'.$amount.'</td>
				</tr>';
				$totalAmount += $amount;
			}
			$netAmount = $totalAmount;
			$totalAmount = number_format($totalAmount,2,'.','');
			$discountPercent = $booking->getDiscountPercent();
			$discountAmount = $booking->getDiscountAmount();
			if(!empty($discountPercent) || !empty($discountAmount)){	
		   		if(!empty($discountPercent)){
					$discount = ($discountPercent / 100) * $totalAmount;
		   		}else{
		   			$discount = $discountAmount;
				}
				if($discount > $netAmount){
					$netAmount = 0;	
				}else{
					$netAmount = $netAmount - $discount;
				}
				$discount = number_format($discount,2,'.','');
				$html .= '<tr style="font-size:13px">
					<td colspan=8 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:right">GROSS AMOUNT</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;font-weight:bold;">'.$totalAmount.'/-</td>
				</tr>
				<tr style="font-size:13px">
					<td colspan=8 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:right">DISCOUNT</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;font-weight:bold;"><font color="red">'.$discount.'/-</font></td>
				</tr>';
			}
			if(!empty($earlierPaidAmount)){
				$netAmount = $netAmount + $inconvenienceCharges;
				$netAmount = $netAmount - $earlierPaidAmount;
				$inconvenienceCharges = number_format($inconvenienceCharges,2,'.','');
				$earlierPaidAmount = number_format($earlierPaidAmount,2,'.','');
				$html .= '<tr style="font-size:13px">
					<td colspan=8 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:right">INCONVENIENCE CHARGES(20%)</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;font-weight:bold;"><font color="red">'.$inconvenienceCharges.'/-</font></td>
				</tr>
				<tr style="font-size:13px">
					<td colspan=8 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:right">EARLIER PAID AMOUNT</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;font-weight:bold;"><font color="green">'.$earlierPaidAmount.'/-</font></td>
				</tr>';
			}
			if(!empty($bookingAddOn)){
				$cakePrice = $bookingAddOn->getPrice();
				$netAmount = $netAmount + $cakePrice;
				$cakePrice = number_format($cakePrice,2,'.','');
				$html .= '<tr style="font-size:13px">
					<td colspan=8 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:right">Cake Charges</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;font-weight:bold;">'.$cakePrice.'/-</td>
				</tr>';
			}
			if(!empty($package)){
				$packagePrice = $package["price"];
				$packageName = $package["occasion"] ."-" .$package["title"];
				$packDesc = $package["description"];
				$netAmount = $netAmount + $packagePrice;
				$packagePrice = number_format($packagePrice,2,'.','');
				$html .= '<tr style="font-size:13px">
					<td colspan=8 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:right">Package('.$packageName.')<br/><span style="font-size:10px !important">'.$packDesc.'</span>
					</td>
					<td style="padding:10px;border:1px silver solid;text-align:right;font-weight:bold;">'.$packagePrice.'/-</td>
				</tr>';
				
			}
			
			$netAmount = number_format($netAmount,2,'.','');
			$html .='<tr style="font-size:13px">
				<td colspan=8 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:right">NET PAYABLE AMOUNT</td>
				<td style="padding:10px;border:1px silver solid;text-align:right;font-weight:bold;">Rs. '.$netAmount.'/-</td>
			</tr>
			<tr style="font-size:13px">
				<td colspan=9 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:Center">
					Note : This is computer generated receipt no sign and stamp required
				</td>
			</tr>
		</table>';
		$attachment = html2PdfUtil::html2Pdf($html);
		
		return $attachment;
	}
	
	public static function sendBookingClosurNotification($html,$msg,$subject,$emails,$mobiles,$timeSlot){
		self::$logger = Logger::getLogger ( "logger" );
		$emailResponse = "";
		$smsResponse = "";
		if(!empty($emails)){
			$emails = explode(",", $emails);
		 	$emailResponse = MailUtil::sendSmtpMail($subject, $html, $emails,StringConstants::IS_SMTP);
		}
		if(!empty($mobiles)){
			$smsUtil = SMSUtil::getInstance();
			$smsResponse = $smsUtil->sendSMS($mobiles, $msg);
		}
		$notification = New Notification();
		$notification->setEmailErrorDetail($emailResponse);
		$notification->setSmsErrorDetail($smsResponse);
		$emails = implode(",", $emails);
		$notification->setEmailId($emails);
		$notification->setMobileNo($mobiles);
		$notification->setSentOn(new DateTime());
		$notification->setTimeSlotSeq($timeSlot->getSeq());
		$notificationMgr = NotificationMgr::getInstance();
		$notificationMgr->saveNotification($notification);
	}
	
	
	public static function sendEmailFromNotification($notification){
		$type = $notification->getNotificationType();
		$html = $notification->getEmailHtml();
		$subject = $notification->getEmailSubject();
		$emails = $notification->getEmailId();
		$emails = explode(",", $emails);
		$mobiles = $notification->getMobileNo();
		$smsText = $notification->getSMSText();
		$emailResponse = null;
		$smsResponse = null;
		if(!empty($emails)){
			$emailResponse = MailUtil::sendSmtpMail($subject, $html, $emails,StringConstants::IS_SMTP);
			if(!empty($emailResponse)){
				$notification->setEmailErrorDetail($emailResponse);
			}
			$notification->setStatus(NotificationStatus::sent);
		}
		if(!empty($mobiles)){
			$smsUtil = SMSUtil::getInstance();
			$smsResponse = $smsUtil->sendSMS($mobiles, $smsText);
			if(!empty($smsResponse)){
				$notification->setSmsErrorDetail($smsResponse);
			}
			$notification->setStatus(NotificationStatus::sent);
		}
		$notificationMgr = NotificationMgr::getInstance();
		$notificationMgr->saveNotification($notification);
		
	}
	
	private static function getBookingConfirmationAttahment($booking,$menuPersonArr,$timeSlot){
		$html = '<table style="width:100%;margin:auto;border:0px silver solid;padding:10px;font-family:arial;line-height:30px;font-size:13px;color:#666666;" >
		 	<tr>
				<td style="width:100%;padding:10px;border:1px silver solid">
					<p>
						<b>BOOKING CONFIRMATION</b><br>
						Dear'. $booking->getFullName()
						.'Please find attached your booking <b>"'.$booking->getBookingId().'"</b> Confirmation in the Flydining on date <b>"'.$booking->getBookingDate()->format('M d, Y').'"</b> at <b>"('.$timeSlot->getTitle().')"</b>.
						Please print this confirmation and bring it with you. Do not hesitate to contact our customer support at hello@flydining.com in regards to
						your booking.
					</p>
					
					<p>
						<b>MAIN COURSE SELECTION:</b><br>';
						foreach($menuPersonArr as $key=>$titleAndMembers){
							$html .= $titleAndMembers["members"] . "x" . $titleAndMembers["title"] . "<br>";	
						}
					$html .='</p>
					<p>
						<b>VENUE :</b><br>
		 				Fly Dining<br>
						#24/10, House of Life,,<br>
						Kempapura Main Road,<br>
						BENGALURU, KARNATAKA 560024<br>
						INDIA.
					</p>
					
					<p>
						<b>IMPORTANT, MUST READ:</b><br>
							1. Be on time- at least a minimum of 60 minutes before your table reservation. If you are late for your reservation, no refund will be given and no rescheduling will be allowed.<br>
							2. Dress comfortably. Avoid wearing loose shoes.<br>
							3. Do not bring large handbags or umbrellas etc with you. We have limited storage/locker space.<br>
							4. Do not make other plans for the night. Depending on the weather, we might delay your experience, to ensure you enjoy the beautiful Flydining In India.<br>
							5. Do not bring valuables to the experience.<br>
							5. Please bring along your Identification Card/ Passport for verification purpose.<br>
							7. Remember, this experience is strictly for individuals that are 13 years old and above, with a minimum height 145cm and a maximum weight of 150kg. Anyone below the age of 18 MUST be accompanied by an adult or guardian, or not be allowed to board. Pregnant women are also not allowed to experience Flydining In India.<br>
							8. The ticket is NOT-TRANSFERABLE in anyway and any changes of Dinner(s) name/information/menu is not allowed.<br>
							9. If you are lost or need direction, please call +91-81325-40906. Kindly take note that we are unable to delay the experience for you if you call or message us on this number so please do not be late.<br>
					</p> 
					
				</td>
			</tr>
		</table>';
		$attachment1 = html2PdfUtil::html2Pdf($html);
		return  $attachment1;
	}
	
	public static function sendSmtpMail($subject,$body,$toEmails,$isSmtp,$attachments = array()){
		if(empty(self::$logger)){
			self::$logger = self::$logger = Logger::getLogger ( "logger" );
		}
		$mail = new PHPMailer();
		//$body = eregi_replace("[\]",'',$body);
		if($isSmtp){
			//$body = eregi_replace("[\]",'',$body);
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->SMTPAuth   = true;                  // enable SMTP authentication
			$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
			$mail->Host       = "xxx";      // sets GMAIL as the SMTP server
			$mail->Port       = xxx;                   // set the SMTP port for the GMAIL server
			$mail->Username   = "xxxx";  // GMAIL username
			$mail->Password   = "xxxx";           // GMAIL password
		}
		$mail->SetFrom('noreply@flydining.com', 'FlyDining');
		$mail->Subject = $subject;
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
		foreach ($toEmails as $toEmail){
			$mail->AddAddress($toEmail);
		}
        $mail->AddBCC(StringConstants::BCC_EMAIL);
        
		foreach($attachments as $name=>$attachment){
			$name .= ".pdf";
			$mail->addStringAttachment($attachment, $name);
		}
		if(!$mail->Send()) {
			self::$logger->info("Mailer Error: " . $mail->ErrorInfo . " for sending email ". $subject);
			return $mail->ErrorInfo;
		} else {
			self::$logger->info("Email Sent for " . $subject);
			return null;
		}
	}	
}
