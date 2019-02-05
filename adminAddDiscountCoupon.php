<?php
include("SessionCheck.php");
require_once('IConstants.inc');
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/DiscountCouponMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "BusinessObjects/DiscountCoupon.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Utils/DateUtil.php");
$discountCoupon = new DiscountCoupon();
$isEnableChecked = "";
$validTillDateStr = "";
if(isset($_POST["seq"])){
	$seq = $_POST["seq"];
	$discountCouponMgr = DiscountCouponMgr::getInstance();
	$discountCoupon = $discountCouponMgr->findBySeq($seq);
	$validTill = $discountCoupon->getValidTillDate();
	$validTill = DateUtil::StringToDateByGivenFormat("Y-m-d", $validTill);
	$validTillDateStr = $validTill->format("d-m-Y");
	if(!empty($discountCoupon->getIsEnabled())){
		$isEnableChecked = "checked";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Discount Coupon</title>
<?include "ScriptsInclude.php"?>
</head>
<body>
    <div id="wrapper">
    	<?php include("adminmenuInclude.php")?>
    	<div id="page-wrapper" class="gray-bg">
	        <div class="row border-bottom">
	        </div>
        	<div class="row">
	        	<div class="col-lg-12">
	                <div class="ibox">
	                    <div class="ibox-title">
	                    	 <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
								<a class="navbar-minimalize minimalize-styl-2 btn btn-primary "
									href="#"><i class="fa fa-bars"></i> </a>
							</nav>
	                        <h5>Create Discount Coupon</h5>
	                    </div>
	                    <div class="ibox-content">
		                	<form id="couponForm" method="post" enctype="multipart/form-data" action="Actions/DiscountCouponAction.php" class="m-t-lg">
	                        		<input type="hidden" id ="call" name="call"  value="saveCoupon"/>
	                        		<input type="hidden" id ="seq" name="seq"  value="<?php echo $discountCoupon->getSeq()?>"/>
	                        		<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Coupon Code</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" maxLength="25" value="<?php echo $discountCoupon->getCode()?>"  id="code" name="code" required placeholder="Coupon Code" class="form-control">
	                                    </div>
	                               </div>
	                               <div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Description</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" maxLength="250" value="<?php echo $discountCoupon->getDescription()?>"  id="description" name="description" required placeholder="Description" class="form-control">
	                                    </div>
	                               </div>
	                               <div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Percent</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" value="<?php echo $discountCoupon->getPercent()?>"  id="percent" name="percent" required placeholder="Percent" class="form-control">
	                                    </div>
	                               </div>
	                               <div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Valid Till</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" value="<?php echo $validTillDateStr?>"  id="validtilldate" name="validtilldate" placeholder="Select Date" required class="form-control date">
	                                    </div>
	                               </div>
	                               <div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Usage Times</label>
	                                    <div class="col-lg-4">
	                                       	<input class="form-control touchspin3" value="<?php echo $discountCoupon->getUsageTimes()?>" id="usagetimes" required type="text" name="usagetimes">
	                                    </div>
	                               </div>
	                               <div class="form-group row i-checks">
	                       				<label class="col-lg-2 col-form-label">Enabled</label>
	                                    <div class="col-lg-4">
	                                    	<input type="checkbox" <?php echo $isEnableChecked?>  id="isenabled" name="isenabled">
	                                    </div>
	                                </div>
	                               <div class="form-group row">
                               		<div class="col-lg-6">
	                               		<button class="btn btn-primary" type="button" onclick="javascript:submitCouponForm()" id="rzp-button" style="float:right">
	                               			Save Coupon
		                               	</button>
	                              	</div>
	                           </div>
	                        </form>
                        </div>
	                </div>
		        </div>
        	</div>
        </div>
    </div>
 </body>
 </html>
 <script type="text/javascript">
 $(document).ready(function(){
	    $('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
		   	radioClass: 'iradio_square-green',
		});
	    $('.date').datetimepicker({
	 	  	timepicker:false,
	 		format: 'd-m-Y',
		 	minDate:new Date()
 		});
	    $(".touchspin3").TouchSpin({
            verticalbuttons: true,
            buttondown_class: 'btn btn-white',
            buttonup_class: 'btn btn-white',
            max: 500
        });
 });
 function submitCouponForm(){
 	if($("#couponForm")[0].checkValidity()) {
     	 $('#couponForm').ajaxSubmit(function( data ){
			 var obj = $.parseJSON(data);
	    	 if(obj.success == 1){
	        	 location.href = "adminShowDiscountCoupons.php";
	    	 }else{
	        	 alert("Error" + obj.message);
	  		 }	 
		  });
 	}else{
 		$("#couponForm")[0].reportValidity();
 	}
 } 
 </script>