<?php
class Menu{
	public static $tableName = "menus";
	public static $className = "Menu";
	private $seq,$title,$description,$rate;
	
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
	
	public function setRate($rate_){
		$this->rate = $rate_;
	}
	
	public function getRate(){
		return  $this->rate;
	}
}