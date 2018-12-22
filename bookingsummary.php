<?php
require_once('IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuPricingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingAddOnMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/DiscountCouponMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
require('razorconfig.php');
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
if(!isset($_POST["timeslotseq"])){
	echo ("Invalid Execution");
	die;
}
$rescheduleBooking = array();
$isReschedule = false;
$rescheduleBookingId = 0;
$reschedulingAmount = 0;
$name = "";
$email = "";
$dob = "";
$country = "91";
$mobile= "";
$isfillGst = "";
$isfillCake = "";
$notes = "";
$gstNo = "";
$companyName = "";
$companyMobile = "";
$companyState = "";
$coupon = "";
$couponError = "";
$couponSuccess = "";
$discount  = 0;
$discountPercent = 0;
$couponSeq = 0;
$couponCode = "";
$cakeAmount = 0;

if(isset($_POST["rescheduleBookingId"]) && !empty($_POST["rescheduleBookingId"])){
	$rescheduleBookingId = $_POST["rescheduleBookingId"];
	$bookingMgr = BookingMgr::getInstance();
	$rescheduleBooking = $bookingMgr->getBookingDetail($rescheduleBookingId);
	$bookingAddOnMgr = BookingAddOnMgr::getInstance();
	$bookingAddOn = $bookingAddOnMgr->findByBookingSeq($rescheduleBookingId);
	if(!empty($bookingAddOn)){
		$isfillCake = "checked";
		$notes = $bookingAddOn->getNotes();
		$cakeAmount = $bookingAddOn->getPrice();
	}
	$reschedulingAmount = $rescheduleBooking["amount"] + $cakeAmount;
	$name = $rescheduleBooking["fullname"];
	$email = $rescheduleBooking["emailid"];
	$dob = $rescheduleBooking["dateofbirth"];
	$dob = DateUtil::StringToDateByGivenFormat("Y-m-d", $dob);
	$dob = $dob->format("d-m-Y");
	$country = $rescheduleBooking["country"];
	$mobile = $rescheduleBooking["mobilenumber"];
	$gstNo = $rescheduleBooking["gstnumber"];
	$companyName = $rescheduleBooking["companyname"];
	$companyMobile = $rescheduleBooking["companymobile"];
	$companyState =  $rescheduleBooking["gststate"];
	if(!empty($gstNo)){
		$isfillGst = "checked";
	}
	$isReschedule = true;
}
$timeSlotSeq = $_POST["timeslotseq"];
$selectedDate = $_POST["selectedDate"];
$menus = $_POST["menuMembers"];
$timeSlotMgr = TimeSlotMgr::getInstance();
$timeSlot = $timeSlotMgr->findBySeq($timeSlotSeq);
$menuMgr = MenuMgr::getInstance();
$menuPricingMgr = MenuPricingMgr::getInstance();
$menuArr = json_decode($menus);
$menuHml = "";
$amount = 0;
$totalAmount = 0;
$handlingCharges = 0;
$formatedTotalAmount = 0;
$totalAmountInPaise = 0;
$menuBtnVisible = array(1=>"none",2=>"none",3=>"none");
$menuImgVisible = array(1=>"none",2=>"none",3=>"none");
$menusArr = array();
$menuPriceArr = array();
$menuPriceJson = "";
$totalPerson = 0;

foreach ($menuArr as $key=>$value){
	if(empty($value)){
		continue;
	}
	$menu = $menuMgr->findBySeq($key);
	$specialPrice = $menuPricingMgr->getPriceByMenuAndDate($key, $selectedDate);
	$rate = $menu->getRate();
	if(!empty($specialPrice)){
		$rate = $specialPrice;
	}
	$menuHml .= $value . " " . $menu->getTitle() . " - " . $value . " X " . $rate . "<br/>";
	$amount += $value * $rate;
	$totalPerson += $value;
	$totalAmount += $value * $rate;
	$menuBtnVisible[$key] = "inline-table";
	if(!in_array("inline-table", $menuImgVisible)){
		$menuImgVisible[$key] = "inline-table";
	}
	array_push($menusArr, $menu);
	$menuPriceArr[$key] = $rate;
}
if(!empty($menuPriceArr)){
	$menuPriceJson = json_encode($menuPriceArr);
}
if($totalPerson >= 10 && !$isReschedule){
	if($totalPerson == 10){
		$discountPercent = 10;
	}elseif($totalPerson > 10){
		$discountPercent = 15;
	}
	$discount = ($discountPercent / 100) * $amount;
	$totalAmount = $amount - $discount;
}

if(isset($_POST["call"]) && (isset($_POST["call"]) == "applyCoupon" || isset($_POST["call"]) == "addCake")){
	$name = $_POST["fullName"];
	$email = $_POST["email"];
	$dob = $_POST["dateofbirth"];
	$mobile = $_POST["mobile"];
	if(isset($_POST["companyInfo"])){
		$isfillGst = "checked";
	}
	if(isset($_POST["isAddCake"])){
		$isfillCake = "checked";
	}else{
		$isfillCake = "";
		$cakeAmount = 0;
	}
	$notes = $_POST["notes"];
	$gstNo = $_POST["gst"];
	$companyName = $_POST["companyName"];
	$companyMobile = $_POST["companyNumber"];
	$couponCode = ""; 
	if(isset($_POST["couponCode"])){
		$couponCode = $_POST["couponCode"];
	}
	$country = $_POST["country"];
	$companyState = $_POST["companyState"];
	$discountCoupnMgr = DiscountCouponMgr::getInstance();
	
	if(!empty($couponCode)){
		$couponInfo = $discountCoupnMgr->applyCoupon($couponCode,$amount);
		if(empty($couponInfo)){
			$couponError = "Invalid coupon code!";
		}else{
			$discountPercent = $couponInfo["percent"];
			$discountedAmount = $couponInfo["amount"];
			$couponSeq = $couponInfo["couponSeq"];
			$discount = $amount - $discountedAmount;
			$totalAmount = $discountedAmount;
			$couponSuccess = "Coupon Applied Successfully!";
		}
	}
	if(isset($_POST["isAddCake"])){
		$cakeAmount = 500;
	}
}
$totalAmount += $cakeAmount;
if(!empty($reschedulingAmount) && !empty($totalAmount)){
	if($totalAmount < $reschedulingAmount){
		$totalAmount = 0;
	}else{
		$totalAmount = $totalAmount - $reschedulingAmount;
	}
	
	$reschedulingAmount = number_format($reschedulingAmount,2);
}
$amountInPaiseWithouAddOn = $totalAmount;
if(!empty($amount)){
	$amount = number_format($amount,2);
	$totalAmount +=  $handlingCharges;
	$amountWithouAddOn = $totalAmount;
	$formatedTotalAmount = number_format($totalAmount,2);
	$totalAmountInPaise = $totalAmount * 100;
	$amountInPaiseWithouAddOn = $amountInPaiseWithouAddOn * 100;
}
$buttonLabel = "Make Payment of Rs " . $formatedTotalAmount;
if(!empty($discount)){
	$discount = number_format($discount,2);
}
//$totalAmountInPaise = 100;]
$razorpayOrderId = "";
if(!empty($totalAmountInPaise)){
	$api = new Api($keyId, $keySecret);
	$orderData = [
			//'receipt'         => 3456,
			'amount'          => $totalAmountInPaise,
			'currency'        => 'INR',
			'payment_capture' => 1
	];
	$razorpayOrder = $api->order->create($orderData);
	$razorpayOrderId = $razorpayOrder['id'];
}else{
	$buttonLabel = "Save Booking";
}
?>
<html>
<head>
<title>Booking</title>
<?include "ScriptsInclude.php"?>
<style>
	.file-box{
		width:33%;
	}
</style>
<meta name="viewport" content="width=device-width">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
 <div id="wrapper">
	<div class="wrapper wrapper-content animated fadeIn">
       <div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins ">
					<div style="margin-top:10px">
                    	<div class="col-lg-8">
                    		<div class="ibox-content">
	                       		<div class="row text-center" style="margin-bottom:10px">
	                       			<h1 style="padding-bottom:10px">
											MENU
									</h1>
									<?php foreach($menusArr as $menu){?>
										<button class="btn btn-primary btn-rounded menu<?php echo $menu->getSeq()?>btn" style="display:inline-table"><?php echo $menu->getTitle()?></button>
                                    <?php }?>
	                       		</div>
                                    <div class="row">
                                    	<?php foreach($menusArr as $menu){?>
											<img class="menu<?php echo $menu->getSeq()?>img"  style="height:auto;width:100%" src="images/menuImages/<?php echo $menu->getSeq().'.'.$menu->getImageName()?>">
                                    	<?php }?>
                                    </div>
                       			</div>
                    	</div>
                    	<div class="col-lg-4">
                    		<div class="ibox-content">
                    				<div class="row" style="margin-bottom:5px">
	                       				<div class="col-xs-8"><h3>BOOKING SUMMARY</h3></div>
	                       				<div class="col-xs-4 text-right"><button type="button" onclick="javascript:back()" class="btn btn-outline btn-info btn-xs">Change</button></div>
	                       			</div>
	                       			<div class="row" style="margin-bottom:5px">
	                       				<div class="col-xs-8">SLOT <?php echo $timeSlot->getTitle() . "<br>(".$selectedDate.")"?></div>
	                       				<div class="col-xs-4 text-right"><?php echo "Rs " . $amount?></div>
	                       			</div>
	                       			<div class="row">	
	                       				<div class="col-xs-8">
	                       					<small class="text-muted">
	                       						<?php echo $menuHml?>
	                       					</small>
	                       				</div>
	                       				<div class="col-xs-4 text-right"></div>
	                       			</div>
	                       			
	                       			<div class="row m-b-sm">	
	                       				<div class="col-xs-8">
	                       					<small class="text-muted">
	                       						Internet Handling Fees
	                       					</small>
	                       				</div>
	                       				<div class="col-xs-4 text-right">Rs 0.00</div>
	                       			</div>
	                       			<?php if(!empty($isReschedule)){ ?>
		                       			<div class="row m-b-sm">	
		                       				<div class="col-xs-8">
		                       					<small class="text-muted">
		                       						Earlier Paid Amount
		                       					</small>
		                       				</div>
		                       				<div style="color:red" class="col-xs-4 text-right">- Rs. <?php echo $reschedulingAmount?></div>
		                       			</div>
	                       			<?php }?>
	                       			<?php if(!empty($discount) && !$isReschedule){ ?>
		                       			<div class="row m-b-sm">	
		                       				<div class="col-xs-8">
		                       					<small class="text-muted">
		                       						Discount
		                       						<?php if(!empty($couponCode)){ ?>
		                       							 (<?php echo $couponCode?>)
		                       						<?php }?>
		                       						
		                       					</small>
		                       				</div>
		                       				<div style="color:red" class="col-xs-4 text-right">- Rs. <?php echo $discount?></div>
		                       			</div>
	                       			<?php }?>
	                       			<?php if(!empty($cakeAmount)){ ?>
		                       			<div class="row m-b-sm">	
		                       				<div class="col-xs-8">
		                       					<small class="text-muted">
		                       						Cake Charges
		                       					</small>
		                       				</div>
		                       				<div class="col-xs-4 text-right">Rs 500.00</div>
		                       			</div>
	                       			<?php }?>
	                       			<!-- <div class="row bg-muted p-h-sm">	
	                       				<div class="col-xs-8">
	                       					Sub Total
	                       				</div>
	                       				<div class="col-xs-4 text-right">Rs <?php //echo $formatedTotalAmount?></div>
	                       			</div> -->
	                       			<form id="userInfoForm" name="userInfoForm" method="post" action="Actions/BookingAction.php" class="m-t-xs">
	                       			<?php if(!$isReschedule){ ?>
		                       			<div class="row bg-muted p-h-sm">	
		                       				<div class="col-xs-4">
		                       					Discount Coupon
		                       				</div>
		                       				<div class="col-xs-6 text-right">
		                       					<input type="text" id="couponCode" value="<?php echo $couponCode?>" maxLength="100" name="couponCode" placeholder="Coupon Code" class="form-control">
		                       				</div>
		                       				<div class="col-xs-2 p-xxs">
		                       					<button type="button" onclick="javascript:applyCoupon()" class="btn btn-danger btn-xs">Apply</button>
		                       				</div>
		                       			</div>
		                       		<?php }?>
		                       			<?php if(!empty($couponError)){ ?>
			                       			<div class="row bg-danger p-h-sm m-b-xs">
			                       				<div class="col-xs-12 text-center">
			                       					<?php echo $couponError?>
			                       				</div>
			                       			</div>
		                       			<?php }?>
		                       			<?php if(!empty($couponSuccess)){ ?>
			                       			<div class="row bg-info p-h-sm m-b-xs">
			                       				<div class="col-xs-12 text-center">
			                       					<?php echo $couponSuccess?>
			                       				</div>
			                       			</div>
		                       			<?php }?>
		                       			<div class="row bg-success p-h-sm text-uppercase font-bold m-b-sm">	
		                       				<div class="col-xs-8">
		                       					AMOUNT PAYABLE
		                       				</div>
		                       				<div class="col-xs-4 text-right">Rs <?php echo $formatedTotalAmount?></div>
		                       			</div>
	                       				<input type="hidden" id ="call" name="call" value="saveBooking"/>
	                       				<input type="hidden" id ="transactionId" name="transactionId"/>
	                       				<input type="hidden" id ="amount" name="amount"/>
	                       				<input type="hidden" id ="cakePrice" name="cakePrice" value="<?php echo $cakeAmount?>"/>
	                       				<input type="hidden" id ="timeslotseq" name="timeslotseq" value="<?php echo $timeSlotSeq?>" />
	                       				<input type="hidden" id ="rescheduleBookingId" name="rescheduleBookingId" value="<?php echo $rescheduleBookingId?>" />
	                       				<input type="hidden" id ="selectedDate" name="selectedDate" value="<?php echo $selectedDate?>" />
	                       				<input type="hidden" id ="menupersons" name="menuMembers" value='<?php echo $menus?>' />
	                       				<input type="hidden" id ="menuPrice" name="menuPrice" value='<?php echo $menuPriceJson?>' />
	                       				<input type="hidden" id ="discountPercent" name="discountPercent" value='<?php echo $discountPercent?>' />
	                       				<input type="hidden" id ="couponSeq" name="couponSeq" value='<?php echo $couponSeq?>' />
		                       			<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Name</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="fullName" maxLength="100" value="<?php echo $name?>" name="fullName" required placeholder="FullName" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Email</label>
		                                    <div class="col-lg-10">
		                                    	<input type="email" id="email" maxLength="100" value="<?php echo $email?>" name="email" required email placeholder="Email" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">DOB</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="dateofbirth" value="<?php echo $dob?>" name="dateofbirth" required placeholder="Date of Birth" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Country</label>
			                                    <div class="col-lg-10">
			                                    	<?php include 'countryList.php';?>
			                                    </div>
		                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Mobile</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="mobile" value="<?php echo $mobile?>" maxLength="20" name="mobile" required placeholder="Mobile" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                                	<div class="col-lg-10" >
		                                       	<label> <input class="i-checks" <?php echo $isfillCake?> type="checkbox"  name="isAddCake" id="isAddCake" >  Add Cake <small>(Rs. 500/-)</small></label>
		                                       </div>
		                                 </div>
		                                 <div id="addOnDiv" style="display:none">
		                                	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Notes</label>
			                                    <div class="col-lg-10">
			                                    	<textarea maxLength="500" name="notes" placeholder="Pls enter notes for the cake ordered" class="form-control" ><?php echo $notes?></textarea>
			                                    </div>
		                                	</div>
		                                </div>
	                                	<div class="form-group row">
		                                	<div class="col-lg-10" >
		                                       	<label> <input class="i-checks" <?php echo $isfillGst?> type="checkbox"  name="companyInfo" id="companyInfo" >  Fill GST Information</label>
		                                       </div>
		                                 </div>
		                                 <div id="companyDiv" style="display:none">
		                                	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">GST NO.</label>
			                                    <div class="col-lg-10">
			                                    	<input type="text" id="gst" value="<?php echo $gstNo?>" maxLength="50" name="gst" placeholder="GST No." class="form-control">
			                                    </div>
		                                	</div>
		                                	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Company Name</label>
			                                    <div class="col-lg-10">
			                                    	<input type="text" id="companyName" value="<?php echo $companyName?>" maxLength="100"  name="companyName" placeholder="Company Name" class="form-control">
			                                    </div>
		                                	</div>
		                                	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Company Mobile</label>
			                                    <div class="col-lg-10">
			                                    	<input type="text" id="companyNumber" value="<?php echo $companyMobile?>" maxLength="25" name="companyNumber" placeholder="Company Mobile" class="form-control">
			                                    </div>
		                                	</div>
		                                	
		                                	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Company State</label>
			                                    <div class="col-lg-10">
			                                    	<?php include 'stateList.php';?>
			                                    </div>
		                                	</div>
	                                	</div>
	                                	<div class="form-group row">
	                                		<div class="col-lg-10" id="termDiv">
	                                        	<label> <input required class="i-checks" type="checkbox" name="termsAndConditions" id="termsAndConditions" ><a data-toggle="modal" data-target="#myModal4" href="#" >   I agree to accept the terms & condition</a></label>
	                                        </div>
	                                    </div>
	                                	<div class="form-group row">
	                                		<div class="col-lg-12">
		                                		<button class="btn btn-primary" type="button" id="rzp-button" style="width:100%">
			                                		<?php echo $buttonLabel?>
			                                	</button>
		                                	</div>
		                            	</div>
	                       			</form>
	                       			<div class="row m-t-sm text-center">	
	                       				<small class="text-muted">
		                       				You can cancel the tickets upto 4 hours before the show. <br>
		                       				Refunds will be done according to <a href="#">Cancellation Policy</a>
	                       				</small>
	                       			</div>
	                       	</div>
                       	</div>
                    </div>
                    
				</div>
			</div>
		</div>
   	</div>
 </div>
 	<div class="modal inmodal" id="myModal4" tabindex="-1" role="dialog"  aria-hidden="true">
		<div class="modal-dialog">
        	<div class="modal-content animated fadeIn">
	        	<div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	                <h4 class="modal-title">Terms & Conditions</h4>
	            </div>
	            <div class="modal-body">
	            	<ul>
	             	   <li>Outside Food and beverages is not allowed inside our premises.</li>
					   <li>Photography and videography is not allowed.</li>
					   <li>Ticket once purchased cannot be exchanged or adjusted/transferred for any other slot.</li>
					   <li>Handbags, Laptops/Tabs , Cameras and all other electronic itens are not allowed on Flydining.</li>
					   <li>Smoking is strictly not permitted on Flydining.</li> 
					   <li>People under Influence of Alcohal/Drugs will not be allowed in Flydining.</li> 
					   <li>We reserve the Right of Admission.</li>
					 </ul>
			    </div>
	            <div id = "footerDiv" class="modal-footer"></div>
	        </div>
	    </div>
	  </div>
</body>
</html>
<script src="scripts/FormValidators/FormValidators.js"></script>
<script>
$( document ).ready(function() {
	 menuArr = [];
	 $("#country").val("<?php echo $country?>");	
	 $("#companyState").val("<?php echo $companyState?>");
	 $('#dateofbirth').datetimepicker({
         timepicker:false,
         format:'d-m-Y',
         maxDate:new Date()
     });
     currDate = getCurrentDate();
	<?php foreach($menusArr as $menu){?>
		menuArr.push("<?php echo $menu->getSeq()?>");//populate js array
		$(".menu<?php echo $menu->getSeq()?>img").hide();//hide all the photos


		$( ".menu<?php echo $menu->getSeq()?>btn" ).click(function() {//click of button 
			$.each(menuArr, function(key,value){
				$(".menu"+ value +"img").hide();//hide all images
			});
			$(".menu<?php echo $menu->getSeq()?>img").show();//but show the clicked one
		});
	<?php }?>
	$(".menu"+ menuArr[0] +"img").show();//hide all images

	$( ".vegbtn" ).click(function() {
		$(".nvegimg").hide();
		$(".mockimg").hide();
		$(".vegimg").show();
	});
	$( ".nvegbtn" ).click(function() {
		$(".nvegimg").show();
		$(".vegimg").hide();
		$(".mockimg").hide();
	});
	$('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
	$('#termDiv').on('ifChanged', function(event){
		var flag  = $("#termsAndConditions").is(':checked');
		if(flag){
			$('#myModal4').modal('show');
		}
	});
	<?php if(!empty($isfillGst)){?>
		$('#companyDiv').show();
	<?php }?>
	<?php if(!empty($isfillCake)){?>
		$('#addOnDiv').show();
	<?php }?>
	$('#companyInfo').on('ifChanged', function(event){
		var flag  = $("#companyInfo").is(':checked');
		if(flag){
			$('#companyDiv').show();
		}else{
			$('#companyDiv').hide();
		}
	});
	$('#isAddCake').on('ifChanged', function(event){
		var flag  = $("#isAddCake").is(':checked');
		if(flag){
			addCake();
			$('#addOnDiv').show();
		}else{
		   $('#call').val("addCake");
       	   $('#userInfoForm')[0].action = "bookingsummary.php";
       	   $('#userInfoForm')[0].submit();	
		   $('#addOnDiv').hide();
		}
	});
});
function addCake(){
	 bootbox.confirm("Do you realy want to add cake to your booking?", function(result) {
         if(result){
        	 $('#call').val("addCake");
        	 $('#userInfoForm')[0].action = "bookingsummary.php";
        	 $('#userInfoForm')[0].submit();	    
         }else{
        	 $("#isAddCake").iCheck('uncheck');
        	 $("#isAddCake").prop("checked", false);
        	 $('#addOnDiv').hide();
         }
	 });
}
function applyCoupon(){
	$('#call').val("applyCoupon");
	$('#userInfoForm')[0].action = "bookingsummary.php";
	$('#userInfoForm')[0].submit();		
}
document.getElementById('rzp-button').onclick = function(e){
	if($("#userInfoForm")[0].checkValidity()) {
		var amount = "<?php echo $totalAmountInPaise?>";
		if(amount != "0" && amount != 0){ 
			var dateofbirth = $("#dateofbirth").val();
			if(getAge(dateofbirth) <= 12) {
			    alert("You have to be more than 12 years old!");
			    return;
			}
		   $("#amount").val("<?php echo $totalAmountInPaise?>");
		   saveBooking();
		   return;
			var fullName = $("#fullName").val();
			var email = $("#email").val();
			var mobile = $("#mobile").val();
			var options = {
				    "key":"rzp_live_zZ6x7CvsASE4M3",
				    "amount": "<?php echo $totalAmountInPaise?>", // 2000 paise = INR 20
				    "name": "Flydining",
				    "description": "Purchase Description",
				    "image": "https://www.flydining.com/booking/images/logo.png",
				    "prefill": {
					    "contact" : mobile,
				        "name": fullName,
				        "email": email
				    },
				    "notes": {
				    	"BookingDate": "<?php echo $selectedDate; ?>",
				    	"BookingSlot": "<?php echo $timeSlot->getTitle()?>",
				    	"BookingDetails": "<?php echo $menuHml?>",
				    },
				    "theme": {
				        "color": "#1ab394"
				    },
				    "order_id": "<?php echo $razorpayOrderId?>"
		
			};
			options.notes["Country"] = $("#country").val();
			options.notes["DOB"] = $("#dateofbirth").val();
			options.notes["DiscountCoupon"] = $("#couponCode").val();
			if($("#companyInfo").prop('checked') == true){
				options.notes["GSTNo"] = $("#gst").val();
				options.notes["CompanyName"] = $("#companyName").val();
				options.notes["CompanyMobile"] = $("#companyNumber").val();
				options.notes["CompanyState"] = $("#companyState").val();
			} 
			options.handler = function (response){
				$("#transactionId").val(response.razorpay_payment_id);
				    $("#amount").val("<?php echo $amountInPaiseWithouAddOn?>");
				     document.userInfoForm.submit();
				};
	
				// Boolean whether to show image inside a white frame. (default: true)
	
				options.modal = {
				    ondismiss: function() {
				        console.log("This code runs when the popup is closed");
				    },
				    escape: true,
				    backdropclose: false
				};
			var rzp1 = new Razorpay(options);
		    rzp1.open();
		    e.preventDefault();
		}else{
			saveBooking();
		}
    }
    else {
        $("#userInfoForm")[0].reportValidity(); 
    }
    
	
}
function getAge(birthDateString) {
    var today = new Date();
    var parts = birthDateString.split('-');
    var month = parts[1] - 1;
    var birthDate = new Date(parts[2],month,parts[0]);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}


function back(){
	var isRescheudle = "<?php echo $isReschedule?>"
	if(isRescheudle == "1"){
		location.href = "reschedule.php";		
	}else{
		location.href = "index.php";
	}
}
function saveBooking(){
	$("#amount").val("<?php echo $amountInPaiseWithouAddOn?>");
    document.userInfoForm.submit();
} 
</script> 
