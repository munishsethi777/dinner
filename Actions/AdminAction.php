<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/AdminMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/ConfigurationMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/SessionUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Admin.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Configuration.php");
$success = 1;
$message = "";
$call = "";
$response = new ArrayObject();
if(isset($_GET["call"])){
	$call = $_GET["call"];
}else{
	$call = $_POST["call"];
	
}
if($call == "loginAdmin"){
	$username = $_GET["username"];
	$password = $_GET["password"];
	$adminMgr = AdminMgr::getInstance();
	$admin = $adminMgr->logInAdmin($username,$password);
	if(!empty($admin) && $admin->getPassword() == $password){
		$sessionUtil = SessionUtil::getInstance();
		$sessionUtil->createAdminSession($admin);
		$response["admin"] = $adminMgr->toArray($admin);
		$message = "Login successfully";
	}else{
		$success = 0;
		$message = "Incorrect Username or Password";
	}
}
if($call == "changePassword"){
	$password = $_GET["newPassword"];
	$earlierPassword = $_GET["earlierPassword"];
	try{
		$adminMgr = AdminMgr::getInstance();
		$isPasswordExists = $adminMgr->isPasswordExist($earlierPassword);
		if($isPasswordExists){
			$adminMgr->ChangePassword($password);
			$message = "Password Updated Successfully";
		}else{
			$message = "Incorrect Current Password!";
			$success = 0;
		}

	}catch(Exception $e){
		$success = 0;
		$message  = $e->getMessage();
	}
}
if($call == "saveSettings"){
	$cakeVendorEmail = $_GET["cakeVendorEmail"];
	$cakeVendorMobile = $_GET["cakeVendorMobile"];
	$cakeVendorMessage = $_GET["cakeVendorMessage"];
	try{
		$configurationMgr = ConfigurationMgr::getInstance();
		$configurationMgr->saveConfiguration(Configuration::$CAKE_VENDOR_EMAIL, $cakeVendorEmail);
		$configurationMgr->saveConfiguration(Configuration::$CAKE_VENDOR_MOBILE, $cakeVendorMobile);
		$configurationMgr->saveConfiguration(Configuration::$CAKE_VENDOR_MESSAGE, $cakeVendorMessage);
		$message = "Admin Settings Saved Successfully";
	}catch(Exception $e){
		$success = 0;
		$message  = $e->getMessage();
	}
}


$response["success"] = $success;
$response["message"] = $message;
echo json_encode($response);
return;