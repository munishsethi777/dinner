<?php
//require_once('../IConstants.inc');
require_once ($ConstantsArray ['dbServerUrl'] . "StringConstants.php");
require_once ($ConstantsArray ['dbServerUrl'] . "log4php/Logger.php");
Logger::configure ( $ConstantsArray ['dbServerUrl'] . "log4php/log4php.xml" );
class SMSUtil{
	private static $smsUtil;
	private static $logger;
	
	public static function getInstance(){
		if (!self::$smsUtil){
			self::$smsUtil = new SMSUtil();
			self::$logger = Logger::getLogger ( "logger" );
			return self::$smsUtil;
		}
		return self::$smsUtil;
	}
	

	public function sendSMS($receipientno, $msg){
		$msg = urlencode($msg);
		$ch = curl_init();
		$senderID = StringConstants::sms_senderID;
		$authKey = StringConstants::sms_authKey;
		$apiUrl = StringConstants::sms_apiUrl;
		$apiUrl .= "?APIKey=".$authKey."&number=".$receipientno."&text=".$msg."&senderid=".$senderID."&route=13&flashsms=0&channel=2&DCS=0";
		curl_setopt($ch,CURLOPT_URL,  $apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$buffer = curl_exec($ch);
		$errorMessage = null;
		if(empty($buffer)){
			$response = json_decode($buffer,true);
			$errorCode = $response["ErrorCode"];
			if($errorCode != "000"){
				$errorMessage = $response["ErrorMessage"];
			}
		}
		curl_close($ch);
		self::$logger->info($buffer."<br>".$receipientno . "<br> " . $msg);
		return $errorMessage;
	}
}
?>
