<?php
require_once('class.phpmailer.php');
//require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."vendor/autoload.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/html2PdfUtil.php");
class MailUtil{
	
	
	public static function sendOrderEmailClient($booking,$menuPersonsObj,$menuPriceArr){
		$timeSlotMgr = TimeSlotMgr::getInstance();
		$menuMgr = MenuMgr::getInstance();
		
		$timeSlot = $timeSlotMgr->findBySeq($booking->getTimeSlot());
		$menus = $menuMgr->findAll();
		$menuPersonArr = array();
		
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
		$html = '<div style="background-color:grey;width:100%;color:#676a6c;font-family:open sans, Helvetica Neue, Helvetica, Arial, sans-serif">
		<div style="background-color:white;margin:auto;max-width:600px;padding:0px 15px 0px 15px">
		<div style="padding:15px;background-color:#1ab394;color:white;margin:0px -15px 0px -15px;">
		<h1 style="margin-top: 20px;margin-bottom: 10px;">Fly Dining</h1>
		</div>
		
		<div style="font-size:16px;padding:15px;margin:0px -15px 0px -15px;">
		<p>Dear '.$booking->getFullName().',</p>
		<p>Thank you for choosing FlyDining. We will do our best to make this experience phenomenal for you. We look forward to see you.
		<br>Bon Appétit.</p>
		<div style="text-align:center">
		<img src="https://ci4.googleusercontent.com/proxy/oZJFixdJqatJ4bPRlelACrUAiS7mmSp4OJja5qmREUBJVu47cIun1ciQ0hg1No-a2urGigmBjTwz7vi08Cs9arEdNLy3VuY916U=s0-d-e1-ft#http://www.skylounge.in/media/hero-image-receipt.png"
				width="125" height="120" style=";border:0px">
				<h1>Thank You For Your Order!</h1>
				<h2>Order ID :'. $booking->getSeq() .'</h2>
				<h3>Venue</h3>
				<p>'. $booking->getBookingDate()->format('d-m-Y') .' ('.$timeSlot->getTitle().')</p>
				<p>Fly Dining<br>
                        #24/10, House of Life,<br>
                        Kempapura Main Road,<br>
                        BENGALURU, KARNATAKA 560024<br>
                        India.
                </p>
				</div>
				</div>
		
				<div style="margin:10px;padding:10px;background-color:#f3f3f4">
				<h3>Order Confirmation</h3>
				</div>
				<div style="margin:10px;padding:10px">';
					$totalPerson = 0;
					foreach($menuPersonArr as $key=>$titleAndMembers){
						$html .='<div style="padding:20px 30px;margin:0px -15px 10px -15px">
							<div style="width:75%;float:left;position:relative;text-align:left">'.$titleAndMembers["title"].'</div>
							<div style="width:25%;float:left;position:relative;text-align:left">'.$titleAndMembers["members"].'</div>
						</div>';
						$totalPerson += $titleAndMembers["members"];
					}
					$html .='<div style="padding:20px 30px 40px 30px;margin-top:10px;background-color:#f3f3f4;font-weight:bold;font-size:14px;">
						<div style="width:75%;float:left;text-align:left">Total</div>
						<div style="width:25%;float:left;text-align:left">'.$totalPerson.'</div>
					</div>
				
					
				</div>
				<div style="padding:10px;margin:10px;text-align:center;">
					<h3>'.$booking->getFullName().'</h3>
					<h3>'.$booking->getEmailId().'</h3>
					<h3>'.$booking->getMobileNumber().'</h3>
				</div>
				<div style="padding:15px;margin:10px;text-align:center;background-color:#1ab394;color:white;margin:0px -15px 0px -15px;">
				<h1>Fly Dining</h1>
				<br>
				<h2>+91-844-8444-960</h2>
				<h2>hello@flydining.com</h2>
				</div>
                <div>
                <p>Please find session photographs in this <a href="https://photos.google.com/share/AF1QipNEwpOWZiYOV59xaV0_XPf0bbiukFcneeccG8yJ_uepL75c92w3TGsL-krGbQpWZw?key=THk0ZlZhR2NyclBiQWVyU013OFAzWGcwcS03SkNB">link</a>. We will upload pictures next day of the scheduled event.</p>
                </div>
				</div>
				</div>';
					
					
			$subject = "YOUR FLY DINING BOOKING CONFIRMATION.";
			$emails = array(0=>$booking->getEmailId());
			$attachments = self::getAttachments($booking,$menuPersonArr,$menuPriceArr,$timeSlot);
			MailUtil::sendSmtpMail($subject, $html, $emails,$attachments);
			$emails = StringConstants::EMAIL_IDS;
			if(!empty($emails)){
				$emails = explode(",", $emails);
				//MailUtil::sendSmtpMail($subject, $html, $emails);
			}
			
	}
	private static function getAttachments($booking,$menuPersonArr,$menuPriceArr,$timeSlot){
		$invoiceAttachment = self::getInvoiceAttachments($booking, $menuPersonArr, $menuPriceArr);
		$confimrationAttachment = self::getBookingConfirmationAttahment($booking, $menuPersonArr,$timeSlot);
		$attachemtns = array("Invoice"=>$invoiceAttachment,"BookingConfimration"=>$confimrationAttachment);
		return $attachemtns;
	}
	
	private static function getInvoiceAttachments($booking,$menuPersonArr,$menuPriceArr){
		$bookingDate = $booking->getBookedOn()->format('M d, Y, h:is a');
		$html = '<table style="width:100%;margin:auto;border:0px silver solid;padding:0px;font-family:arial;line-height:30px" >
			<tr>
				<td style="width:50%;padding:10px;border:1px silver solid">Fly Dining<br>
					#24/10, House of Life,,<br>
					Kempapura Main Road,<br>
					BENGALURU, KARNATAKA 560024<br>
					INDIA.<br>
					+91-844-8444-960<br>
					hello@flydining.com<br>
					GST No :<br><br>
					
					Bill No : '.$booking->getSeq().'<br><br>
					
					Date : '.$bookingDate .'
				</td>
				<td valign="top" style="width:50%;padding:10px;border:1px silver solid">
					Customer Name : '.$booking->getFullName().'<br><br>
					Contact No : '.$booking->getMobileNumber().'<br><br>
					Email : '.$booking->getEmailId().'<br><br>
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
					<td style="padding:10px;border:1px silver solid;">'.$value["title"].'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right">'.$rate.'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right">'.$value["members"].'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right">'.$amountWithoutTax.'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right">2.5%</td>
					<td style="padding:10px;border:1px silver solid;text-align:right">'.$tax.'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right">2.5%</td>
					<td style="padding:10px;border:1px silver solid;text-align:right">'.$tax.'</td>
					<td style="padding:10px;border:1px silver solid;text-align:right">'.$amount.'</td>
				</tr>';
				$totalAmount += $amount;
			}
			$totalAmount = number_format($totalAmount,2,'.','');
			$html .= '<tr style="font-size:13px">
				<td colspan=8 style="padding:10px;border:1px silver solid;font-weight:bold;text-align:right">TOTAL AMOUNT</td>
				<td style="padding:10px;border:1px silver solid;text-align:right;font-weight:bold;">'.$totalAmount.'</td>
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
	
	private static function getBookingConfirmationAttahment($booking,$menuPersonArr,$timeSlot){
		$html = '<table style="width:100%;margin:auto;border:0px silver solid;padding:10px;font-family:arial;line-height:30px;font-size:13px;color:#666666;" >
		 	<tr>
				<td style="width:100%;padding:10px;border:1px silver solid">
					<p>
						<b>BOOKING CONFIRMATION</b><br>
						Dear'. $booking->getFullName()
						.'Please find attached your booking <b>"'.$booking->getSeq().'"</b> Confirmation in the Flydining on date <b>"'.$booking->getBookingDate()->format('M d, Y').'"</b> at <b>"('.$timeSlot->getTitle().')"</b>.
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
							9. If you are lost or need direction, please call +91-844-8444-960. Kindly take note that we are unable to delay the experience for you if you call or message us on this number so please do not be late.<br>
					</p> 
					
				</td>
			</tr>
		</table>';
		$attachment1 = html2PdfUtil::html2Pdf($html);
		return  $attachment1;
	}
	
	public static function sendSmtpMail($subject,$body,$toEmails,$attachments = array()){
		$mail = new PHPMailer();
		//$body = file_get_contents('contents.html');
		$body = eregi_replace("[\]",'',$body);
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mail->Host       = "mail.virsacouture.com";      // sets GMAIL as the SMTP server
		$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
		$mail->Username   = "info@virsacouture.com";  // GMAIL username
		$mail->Password   = "Munish#314";            // GMAIL password
		
		$mail->SetFrom('bookings@flydining.com', 'FlyDining');
		$mail->AddReplyTo("bookings@flydining.com","FlyDining");
		$mail->Subject = $subject;
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
		foreach ($toEmails as $toEmail){
			$mail->AddAddress($toEmail);
		}
		//$mail->AddBCC("munishsethi777@gmail.com");
		foreach($attachments as $name=>$attachment){
			$name .= ".pdf";
			$mail->addStringAttachment($attachment, $name);
		}
		//$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
		
		if(!$mail->Send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
			echo "Message sent!";
		}
	}
	
	function mail_attachment($subject,$body,$toEmails,$attachments = array()) {
		$to = implode(",", $toEmails);
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	    $headers  = 'From: FlyDining<hello@flydining.com>' . "\r\n";
        $headers .= 'Bcc: hello@flydining.com,blr@flydining.com' . "\r\n";
	    // boundary 
	    $semi_rand = md5(time()); 
	    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	
	    // headers for attachment 
	    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
	
	    // multipart boundary 
	    $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
	    $message .= "--{$mime_boundary}\n";
	
	    // preparing attachments
	    foreach ($attachments as $key=>$attachment){
	        $data = chunk_split(base64_encode($attachment));
	        $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$key\"\n" . 
	        "Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . 
	        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
	        $message .= "--{$mime_boundary}\n";
	    }
	    $ok = mail($to, $subject, $body, $headers); 
	    if ($ok) { 
	        echo "<p>mail sent to $to!</p>"; 
	    } else { 
	        echo "<p>mail could not be sent!</p>"; 
	    } 
	}
}