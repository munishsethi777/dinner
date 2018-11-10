<?php
class MenuPricing{
	private $seq,$menuseq,$date,$price,$description;
	public static $tableName = "menupricing";
	public static $className = "MenuPricing";
	public function setSeq($seq_){
		$this->seq = $seq_;
	}
	public function getSeq(){
		return $this->seq;
	} 
	
	public function setMenuSeq($menuSeq_){
		$this->menuseq = $menuSeq_;
	}
	public function getMenuSeq(){
		return $this->menuseq;
	}
	
	public function setDate($date_){
		$this->date = $date_;
	}
	public function getDate(){
		return $this->date;
	}
	
	public function setPrice($price_){
		$this->price = $price_;
	}
	public function getPrice(){
		return $this->price;
	}
	public function setDescription($description_){
		$this->description = $description_;
	}
	public function getDescription(){
		return $this->description;
	}
}