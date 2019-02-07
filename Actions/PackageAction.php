<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/PackageMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Package.php");
$success = 1;
$call = "";
$message = "";
$response = new ArrayObject();
if(isset($_GET["call"])){
	$call = $_GET["call"];
}else{
	$call = $_POST["call"];
}
$packageMgr = PackageMgr::getInstance();
if($call == "savePackage"){
	try{
		$package = new Package();
		$package->createFromRequest($_REQUEST);
		$isEnabled = 0;
		if(isset($_REQUEST["isenabled"]) && !empty($_REQUEST["isenabled"])){
			$isEnabled = 1;
		}
		$package->setIsEnabled($isEnabled);
		$package->setCreatedOn(new DateTime());
		$package->setLastModifiedOn(new DateTime());
		$packageMgr->savePackage($package);
		$message = "Package Saved Successfully";
	}catch (Exception $e){
		$success = 0;
		$message  = $e->getMessage();
	}
}
if($call == "getAllPackages"){
	$packages = $packageMgr->getAllForGrid();
	echo $packages;
	return;
}
if($call == "deletePackages"){
	$ids = $_GET["ids"];
	try{
		$packageMgr->deleteBySeqs($ids);
		$message = "Package(s) Deleted successfully";
	}catch(Exception $e){
		$success = 0;
		$message = $e->getMessage();
	}
}
if($call == "getPackagePrice"){
	$ids = $_GET["id"];
	$response = new ArrayObject();
	try{
		$price = $packageMgr->getPackagePrice($ids);
		$response["price"] = $price;
		$message = "Package(s) Deleted successfully";
	}catch(Exception $e){
		$success = 0;
		$message = $e->getMessage();
	}
	$response["success"]  = $success;
	$response["message"]  = $message;
	echo json_encode($response);
	return;
}
if($call == "getPackagesByOccasionSeq"){
	$occassionSeq = $_REQUEST["selectedOccasion"];
	$response = new ArrayObject();
	try{
		$packages = null;
		if(!empty($occassionSeq)){
			$packages = $packageMgr->findByOccasionSeq($occassionSeq);
		}
		$response["packages"] = $packages;
	}catch(Exception $e){
		$success = 0;
		$message = $e->getMessage();
	}
	$response["success"]  = $success;
	$response["message"]  = $message;
	echo json_encode($response);
	return;
}


$response = new ArrayObject();
$response["success"]  = $success;
$response["message"]  = $message;
echo json_encode($response);

