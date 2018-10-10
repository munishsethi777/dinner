<?php
class Admin{
	
	private $seq,$username,$name,$password,$isenable,$createdon;
	public static $className = "Admin";
	public static $tableName = "admins";
	public function setSeq($seq_){
		$this->seq = $seq_;
	}
	public function getSeq(){
		return $this->seq;
	}
	
	public function setUserName($userName_){
		$this->username = $userName_;
	}
	public function getUserName(){
		return $this->username;
	}
	
	public function setName($name_){
		$this->name = $name_;
	}
	public function getName(){
		return $this->name;
	}
	
	public function setPassword($password_){
		$this->password = $password_;
	}
	public function getPassword(){
		return $this->password;
	}
	
	public function setIsEnable($isEnable){
		$this->isenable = $isEnable;
	}
	public function getIsEnable(){
		return $this->isenable;
	}
	
	public function setCreatedOn($createdOn_){
		$this->createdon = $createdOn_;
	}
	public function getCreatedOn(){
		return $this->createdon;
	}
	
}