<?php
class Occasion{
	private $seq,$title,$description,$createdon,$lastmodifiedon,$isenabled;
	public static $tableName = "occasions";
	public static $className = "Occasion";
	public function setSeq($seq_){
		$this->seq = $seq_;	
	}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setTitle($title_){
		$this->title = $title_;
	}
	public function getTitle(){
		return $this->title;
	}
	
	public function setDescription($description_){
		$this->description = $description_;
	}
	public function getDescription(){
		return $this->description;
	}
	
	public function setCreatedOn($createdOn_){
		$this->createdon = $createdOn_;
	}
	public function getCreatedOn(){
		return $this->createdon;
	}
	
	public function setLastModifiedOn($lastModifiedOn){
		$this->lastmodifiedon = $lastModifiedOn;
	}
	public function getLastModifiedOn(){
		return $this->lastmodifiedon;
	}
	
	public function setIsEnabled($isEnabled){
		$this->isenabled = $isEnabled;
	}
	public function getIsEnabled(){
		return $this->isenabled;
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