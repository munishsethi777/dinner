<?php
require_once($ConstantsArray['dbServerUrl'] ."DataStores/BeanDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/DiscountCoupon.php");
class DiscountCouponMgr{
	private static $DiscountCouponMgr;
	private static $dataStore;
	private static $sessionUtil;
	
	public static function getInstance()
	{
		if (!self::$DiscountCouponMgr)
		{
			self::$DiscountCouponMgr = new DiscountCouponMgr();
			self::$dataStore = new BeanDataStore(DiscountCoupon::$className, DiscountCoupon::$tableName);
		}
		return self::$DiscountCouponMgr;
	}
	
	public function saveDiscountCoupon($discountCoupon){
		$id = self::$dataStore->save($discountCoupon);
		return $id;
	}
	
	public function getAllForGrid(){
		$coupons = self::$dataStore->findAllArr(true);
		$mainArr["Rows"] = $coupons;
		$mainArr["TotalRows"] = $this->getAllCount();
		return json_encode($mainArr);
	}
	
	public function getAllCount(){
		$query = "select count(*) from discountcoupons";
		$count = self::$dataStore->executeCountQueryWithSql($query,true);
		return $count;
	}
	
	public function findBySeq($seq){
		$discountCoupon = self::$dataStore->findBySeq($seq);
		return $discountCoupon;
	}
	
	public function deleteBySeqs($ids){
		$flag = self::$dataStore->deleteInList($ids);
		return $flag;
	}
	public function findByCode($code){
		$colval["code"] = $code;
		$coupon = self::$dataStore->executeConditionQuery($colval);
		if(!empty($coupon)){
			return $coupon[0];
		}
		return null;
	}
	public function applyCoupon($code,$amount){
		$coupon = $this->findByCode($code);
		if(empty($coupon)){
			return null;
		}
		$isEnabled = $coupon->getIsEnabled();
		if(empty($isEnabled)){
			return null;
		}
		$validTill = $coupon->getValidTillDate();
		$validTill = DateUtil::StringToDateByGivenFormat("Y-m-d", $validTill);
		$validTill->setTime(0,0);
		$now = new DateTime();
		$now->setTime(0, 0);
		$inValidCoupon = false;
		if($validTill <  new DateTime()){
			return null;
		}
		$bookingManager = BookingMgr::getInstance();
		$usagesCount = $bookingManager->getCouponUsageCount($coupon->getSeq());
		$usagesTimes = $coupon->getUsageTimes();
		if($usagesCount >= $usagesTimes){
			return null;
		}
		$percent = $coupon->getPercent();
		$discount = ($percent / 100) * $amount;
		$amount = $amount - $discount; 
		$mainArr["percent"] = $percent;
		$mainArr["amount"] = $amount;
		$mainArr["couponSeq"] = $coupon->getSeq();
		return $mainArr;
	}
	
	public function getAll(){
		$coupons = self::$dataStore->findAll();
		return $coupons;
	}
}