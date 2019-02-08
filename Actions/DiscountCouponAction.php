<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/DiscountCouponMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/DiscountCoupon.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
$success = 1;
$call = "";
$message = "";
$response = new ArrayObject();
if(isset($_GET["call"])){
	$call = $_GET["call"];
}else{
	$call = $_POST["call"];
}
$discountCouponMgr = DiscountCouponMgr::getInstance();
if($call == "saveCoupon"){
	try{
		$discountCoupon = new DiscountCoupon();
		$discountCoupon->createFromRequest($_REQUEST);
		$discountCoupon->setCreatedOn(new DateTime());
		$isEnabled = 0;
		if(isset($_REQUEST["isenabled"]) && !empty($_REQUEST["isenabled"])){
			$isEnabled = 1;
		}
		$discountTypeOption = $_POST["discountTypeOption"];
		$percent = 0;
		$maxAmount = 0;
		$maxSeats = 0;
		if($discountTypeOption == "percent"){
			$percent = $_POST["percent"];
			$maxSeats = $_POST["maxseats"];
		}else{
			$maxAmount = $_POST["maxamount"];
		}
		$discountCoupon->setPercent($percent);
		$discountCoupon->setMaxAmount($maxAmount);
		$discountCoupon->setMaxSeats($maxSeats);
		$discountCoupon->setIsEnabled($isEnabled);
		$validTillDate = $_REQUEST["validtilldate"];
		$validTillDate = DateUtil::StringToDateByGivenFormat("d-m-Y", $validTillDate);
		$discountCoupon->setValidTillDate($validTillDate);
		$discountCouponMgr->saveDiscountCoupon($discountCoupon);
		$message = "Discount Coupon Saved Successfully";
	}catch (Exception $e){
		$success = 0;
		$message  = $e->getMessage();
	}
}
if($call == "getAllCoupons"){
	$couponJson = $discountCouponMgr->getAllForGrid();
	echo $couponJson;
	return;
}
if($call == "deleteCoupons"){
	$ids = $_GET["ids"];
	try{
		$discountCouponMgr->deleteBySeqs($ids);
		$message = "Discount coupon(s) Deleted successfully";
	}catch(Exception $e){
		$success = 0;
		$message = $e->getMessage();
		//$message = ErrorUtil::checkReferenceError(LearningPlan::$className,$e);
	}
}
$response = new ArrayObject();
$response["success"]  = $success;
$response["message"]  = $message;
echo json_encode($response);