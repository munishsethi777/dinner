<?php
class Configuration{
	private $configkey,$configvalue;
	
	public static $CAKE_VENDOR_EMAIL = "cakevendoremail";
	public static $CAKE_VENDOR_MOBILE = "cakevendormobile";
	public static $CAKE_VENDOR_MESSAGE = "cakevendormessage";
	
	public static $BOOKING_CLOSUR_EMAIL = "bookingclosuremail";
	public static $BOOKING_CLOSUR_MOBILE = "bookingclosurmobile";
	
	public static $tableName = "configurations";
	public static $className = "configuration";

	
	public function setSeq($seq_){
		$this->seq = $seq_;
	}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setConfigKey($configKey){
		$this->configkey = $configKey;
	}
	public function getConfigKey(){
		return $this->configkey;
	}

	public function setConfigValue($configValue){
		$this->configvalue = $configValue;
	}
	public function getConfigValue(){
		return $this->configvalue;
	}
}