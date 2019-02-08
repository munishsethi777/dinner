<?php
class DiscountCoupon{
	private $seq, $description,$code,$isenabled,$createdon,$validtilldate,$usagetimes,$percent;
	private $maxamount,$maxseats;
	public static $className = "DiscountCoupon";
	public static $tableName = "discountcoupons";
	public function setSeq($seq_){
		$this->seq = $seq_;
	}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setDescription($description_){
		$this->description = $description_;
	}
	public function getDescription(){
		return $this->description;
	}
	
	public function setCode($code_){
		$this->code = $code_;
	}
	public function getCode(){
		return $this->code;
	}
	
	public function setIsEnabled($isEnabled_){
		$this->isenabled = $isEnabled_;
	}
	public function getIsEnabled(){
		return $this->isenabled;
	}
	
	public function setCreatedOn($createdOn_){
		$this->createdon = $createdOn_;
	}
	public function getCreatedOn(){
		return $this->createdon;
	}
	
	public function setValidTillDate($validTillDate_){
		$this->validtilldate = $validTillDate_;
	}
	public function getValidTillDate(){
		return $this->validtilldate;
	}
	
	public function setUsageTimes($usageTimes_){
		$this->usagetimes = $usageTimes_;
	}
	public function getUsageTimes(){
		return $this->usagetimes;
	}
	
	public function setPercent($percent_){
		$this->percent  = $percent_;
	}
	public function getPercent(){
		return $this->percent;
	}
	
	public function setMaxAmount($maxamount_){
		$this->maxamount = $maxamount_;
	}
	public function getMaxAmount(){
		return $this->maxamount;
	}

	public function setMaxSeats($seats_){
		$this->maxseats = $seats_;
	}
	public function getMaxSeats(){
		return $this->maxseats;
	}
	
	function createFromRequest($request){
		if (is_array($request)){
			$this->from_array($request);
		}
		return $this;
	}
	
	public function from_array($array)
	{
		foreach(get_object_vars($this) as $attrName => $attrValue){
			$flag = property_exists(self::$className, $attrName);
			$isExists = array_key_exists($attrName, $array);
			if($flag && $isExists){
				$this->{$attrName} = $array[$attrName];
			}
		}
	}
}