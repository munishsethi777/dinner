<?php
require_once('class.phpmailer.php');
class MailUtil{
	
	public static function sendOrderEmailClient($to){
		$html = '<div style="background-color:grey;width:100%">
		<div style="align:auto;background-color:white;margin-left:25%;width:50%;padding:0px 15px 0px 15px">
		<div style="padding:15px;background-color:#1ab394;color:white;margin:0px -15px 0px -15px;">
		<h1>Fly Dining</h1>
		</div>
		
		<div style="font-size:16px;padding:15px;margin:0px -15px 0px -15px;">
		<p>Dear Kishan,</p>
		<p>Thank you for choosing Sky Lounge. We will do our best to make this experience phenomenal for you. We look forward to see you.
		Bon Appétit.</p>
		<div style="text-align:center">
		<img src="https://ci4.googleusercontent.com/proxy/oZJFixdJqatJ4bPRlelACrUAiS7mmSp4OJja5qmREUBJVu47cIun1ciQ0hg1No-a2urGigmBjTwz7vi08Cs9arEdNLy3VuY916U=s0-d-e1-ft#http://www.skylounge.in/media/hero-image-receipt.png"
				width="125" height="120" style=";border:0px">
				<h1>Thank You For Your Order!</h1>
				<h2>Order ID :125C4E</h2>
				<h3>Venue</h3>
		
				<p>Oct. 2, 2018 (01:00PM - 02:00PM)</p>
				<p>Sky Lounge<br>
				Kempapura Main Road, Nr. Nagavara Lake,<br>
				Nagavara, Hebbal, Bengaluru,<br>
				Karnataka - 560024, India.</p>
				</div>
				</div>
		
				<div style="margin:30px;padding:10px;background-color:#f3f3f4">
				<h3>Order Confirmation</h3>
				</div>
				<div style="margin:30px;padding:10px">
					
					<div style="padding:0px 30px;margin:0px -15px 10px -15px">
						<div style="width:75%;float:left;position:relative;text-align:left">Vegetarian Menu</div>
						<div style="width:25%;float:left;position:relative;text-align:left">2</div>
					</div>
					
					<div style="padding:20px 30px;margin:0px -15px 0px -15px">
						<div style="width:75%;float:left;position:relative;text-align:left">Non Vegetarian Menu</div>
						<div style="width:25%;float:left;position:relative;text-align:left">1</div>
					</div>
					
					<div style="padding:20px 30px 40px 30px;margin:10px -15px 0px -15px;background-color:#f3f3f4;font-weight:bold;font-size:14px;">
						<div style="width:75%;float:left;text-align:left">Total</div>
						<div style="width:25%;float:left;text-align:left">3</div>
					</div>
				
					
				</div>
				<div style="padding:10px;margin:30px;text-align:center;">
					<h3>Munish Sethi</h3>
					<h3>munishsethi777@gmail.com</h3>
					<h3>9814600356</h3>
				</div>
				<div style="padding:15px;margin:30px;text-align:center;background-color:#1ab394;color:white;margin:0px -15px 0px -15px;">
				<h1>Fly Dining</h1>
				<br>
				<h2>+91 99889 99919</h2>
				<h2>info@flydining.com</h2>
				</div>
		
		
				</div>
				</div>';
			$subject = "YOUR FLY DINING BOOKING CONFIRMATION.";
			MailUtil::sendSmtpMail($subject, $html, $to);
		
	}
	
	private static function sendSmtpMail($subject,$body,$toEmail){
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
		
		$mail->SetFrom('bookings@flydining.com', 'First Last');
		$mail->AddReplyTo("bookings@flydining.com","First Last");
		$mail->Subject = $subject;
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
		$mail->AddAddress($toEmail);
		//$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
		
		if(!$mail->Send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
			echo "Message sent!";
		}
	}
}