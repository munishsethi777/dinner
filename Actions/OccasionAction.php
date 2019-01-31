<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/OccasionMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Occasion.php");
$success = 1;
$call = "";
$message = "";
$response = new ArrayObject();
if(isset($_GET["call"])){
	$call = $_GET["call"];
}else{
	$call = $_POST["call"];
}
$occasionMgr = OccasionMgr::getInstance();
if($call == "saveOccasion"){
	try{
		$occasion = new Occasion();
		$occasion->createFromRequest($_REQUEST);
		$isEnabled = 0;
		if(isset($_REQUEST["isenabled"]) && !empty($_REQUEST["isenabled"])){
			$isEnabled = 1;
		}
		$occasion->setIsEnabled($isEnabled);
		$occasion->setCreatedOn(new DateTime());
		$occasion->setLastModifiedOn(new DateTime());
		$occasionMgr->saveOccasion($occasion);
		$message = "Package Saved Successfully";
	}catch (Exception $e){
		$success = 0;
		$message  = $e->getMessage();
	}
}
if($call == "getAllOccasions"){
	$occasions = $occasionMgr->getAllForGrid();
	echo $occasions;
	return;
}
if($call == "deleteOccasions"){
	$ids = $_GET["ids"];
	try{
		$occasionMgr->deleteBySeqs($ids);
		$message = "Occasion(s) Deleted successfully";
	}catch(Exception $e){
		$success = 0;
		$message = $e->getMessage();
	}
}
$response = new ArrayObject();
$response["success"]  = $success;
$response["message"]  = $message;
echo json_encode($response);
