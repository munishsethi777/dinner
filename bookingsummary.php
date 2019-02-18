<?php
require_once('IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuPricingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingAddOnMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/DiscountCouponMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/DateUtil.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/PackageMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/OccasionMgr.php");

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
$discountMaxAmount = 0;
$discoutOnMaxSeats = 0;
$couponSeq = 0;
$couponCode = "";
$cakeAmount = 0;
$inconvenienceCharges = 0;
$inconveniencePercent = 20;
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
	if($reschedulingAmount > 0){
		$inconvenienceCharges = ($inconveniencePercent / 100) * $reschedulingAmount;
	}
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

$packageCharge = number_format(0,2);
$packageName = "";
$selectedPackageSeq = $_POST["selectedPackage"];
//$selectedOccassionSeq = $_POST["selectedOccassion"];
if(!empty($selectedPackageSeq)){
	$packageMgr = PackageMgr::getInstance();
	$package = $packageMgr->findArrBySeq($selectedPackageSeq);
	$packageCharge = $package["price"];
	$packageName = $package["occasion"] . "-" .$package["title"];
}
//$occassionMgr = OccasionMgr::getInstance();
//$occassion = $occassionMgr->findBySeq($selectedOccassionSeq);


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
if($totalPerson >= 11 && !$isReschedule){
	if($totalPerson <= 15){
		$discountPercent = 10;
	}elseif($totalPerson > 15){
		$discountPercent = 20;
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
		$couponInfo = $discountCoupnMgr->applyCoupon($couponCode,$amount,$menusArr);
		if(empty($couponInfo)){
			$couponError = "Invalid coupon code!";
		}else{
			$discountPercent = $couponInfo["percent"];
			$discountedAmount = $couponInfo["amount"];
			$discountMaxAmount = $couponInfo["maxamount"];
			$discoutOnMaxSeats = $couponInfo["maxseats"];
			$isInvalid = false;
			if(!empty($discountPercent) && !empty($discoutOnMaxSeats)){
				if($totalPerson > $discoutOnMaxSeats){
					$couponError = "Invalid coupon code!";
					$isInvalid = true;
				}
			}
			if(!$isInvalid){
				if(!empty($discountPercent)){
					$discount = $amount - $discountedAmount;
				}else{
					$discount = $discountMaxAmount;
				}
				$couponSeq = $couponInfo["couponSeq"];
				//$discount = $amount - $discountedAmount;
				$totalAmount = $discountedAmount;
				$couponSuccess = "Coupon Applied Successfully!";
			}
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
$totalAmount += $packageCharge;
$amountInPaiseWithouAddOn = $totalAmount;
if(!empty($amount)){
	$amount = number_format($amount,2);
	$totalAmount +=  $handlingCharges;
	$totalAmount += $inconvenienceCharges;
	$amountWithouAddOn = $totalAmount;
	$formatedTotalAmount = number_format($totalAmount,2);
	$inconvenienceCharges = number_format($inconvenienceCharges,2);
	$totalAmountInPaise = $totalAmount * 100;
	$amountInPaiseWithouAddOn = $amountInPaiseWithouAddOn * 100;
}
$buttonLabel = "Make Payment of Rs " . $formatedTotalAmount;
if(!empty($discount)){
	$discount = number_format($discount,2);
}


//$totalAmountInPaise = 100;


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
				<?php //include 'header.php';?>
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
	                       			<?if(!empty($selectedPackageSeq)){ ?>
	                       			<div class="row m-b-sm">	
	                       				<div class="col-xs-8">
	                       					<small class="text-muted">
	                       						Package Charges (<?php echo $packageName?>)
	                       					</small>
	                       				</div>
	                       				<div class="col-xs-4 text-right">Rs <?php echo number_format($packageCharge,2);?></div>
	                       			</div>
	                       			<?php }?>
	                       			<?php if(!empty($isReschedule)){ ?>
	                       				<div class="row m-b-sm">	
		                       				<div class="col-xs-8">
		                       					<small class="text-muted">
		                       						Inconvenience Charges(20%)
		                       					</small>
		                       				</div>
		                       				<div style="color:red" class="col-xs-4 text-right"><?php echo "Rs " . $inconvenienceCharges?></div>
	                       				</div>
		                       			<div class="row m-b-sm">	
		                       				<div class="col-xs-8">
		                       					<small class="text-muted">
		                       						Earlier Paid Amount
		                       					</small>
		                       				</div>
		                       				<div style="color:green" class="col-xs-4 text-right">- Rs. <?php echo $reschedulingAmount?></div>
		                       			</div>
	                       			<?php }?>
	                       			<?php if(!empty($discount) && !$isReschedule){ ?>
		                       			<div class="row m-b-sm">	
		                       				<div class="col-xs-8">
		                       					<small class="text-muted">
		                       						Discount
		                       						<?php if(!empty($couponCode)){ 
		                       							if(!empty($discountPercent)){?>
		                       							 	(<?php echo $couponCode . " " . $discountPercent . "%"?>)
		                       							 <?php }else{?>
		                       							  	(<?php echo $couponCode . " Rs." . $discountMaxAmount?>)
		                       							  <?php }?>
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
	                       				<div class="form-group row" style="display:none">
		                                	<div class="col-lg-10" >
		                                       	<label> <input class="i-checks" <?php echo $isfillCake?> type="checkbox"  name="isAddCake" id="isAddCake" >  Add Cake <small>(Rs. 500/-)</small></label>
		                                       </div>
		                                 </div>
		                                 <div id="addOnDiv" style="display:none">
		                                	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Notes</label>
			                                    <div class="col-lg-10">
			                                    	<textarea maxLength="500" id="addonnotes" name="addonnotes" placeholder="Pls enter notes for the cake ordered" class="form-control" ><?php echo $notes?></textarea>
			                                    </div>
		                                	</div>
		                                </div>
		                                
		                                <div class="form-group row">
			                       			<label class="col-lg-2 col-form-label">Notes</label>
			                                   <div class="col-lg-10">
			                                   		<textarea maxLength="250" id="notes" name="notes" placeholder="Pls enter notes for booking" class="form-control" ><?php echo $notes?></textarea>
			                                   </div>
		                                </div>
		                                
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
	                       				<input type="hidden" id ="discountAmount" name="discountAmount" value='<?php echo $discountMaxAmount?>' />
	                       				<input type="hidden" id ="couponSeq" name="couponSeq" value='<?php echo $couponSeq?>' />
	                       				<input type="hidden" id ="selectedPackage" name="selectedPackage" value='<?php echo $selectedPackageSeq?>' />
	                       				<input type="hidden" id ="packagePrice" name="packagePrice" value='<?php echo $packageCharge?>' />
	                       				
	                       				
	                       				
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
			<?php include 'phoneInclude.php';?>
		</div>
   	</div>
 </div>
 	<div class="modal inmodal" id="myModal4" tabindex="-1" role="dialog"  aria-hidden="true">
		<div class="modal-dialog modal-lg">
        	<div class="modal-content animated fadeIn">
	        	<div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	                <h4 class="modal-title" style="color:#000;">Terms & Conditions</h4>
	            </div>
	            <div class="modal-body">
	            	<p>Visitors and users (hereafter referred to as the customer) of Flydining Online Reservation System on the Internet (hereafter referred to as System) agree to comply with the following terms of use (hereafter referred to as Terms) before any booking of seats.</p>
	            	<p>The Organizer shall have the right to amend the Terms of use of the System by publishing the relevant information/news in the www.flydining.com.The amendments enter into effect upon their publication on the www.flydining.com.</p>

	            	<h3 style="color:#000;">1. DEFINITIONS AND INTERPRETATION</h3>
	            	<p>“Seats” refers to the seats or other types of “Event” refers to the Flydining experience, which will be held at a venue in respect of which we have the right to sell you seats. Evidence (including electronic/online bookings) for our event sold by us to you. “Venue” means any facilities or locations of any nature where the event is being held. “We” means the organizer. “Us” and “Our” shall be ready accordingly. “You” means the seat who booked or anybody who in our reasonable opinion is acting with your authority or permission. “Your” shall be read accordingly.</p>

	            	<h3 style="color:#000;">2. SEATS</h3>
	            	<p>2.1  All prices quoted are in Indian Rupee (INR) and includes local taxes and GST.</p>
	            	<p>2.2  All seats are sold subject to availability and these terms and conditions. These terms and conditions should be read carefully prior to booking and any queries relating to them should be raised with us prior to booking, as booking of seats constitutes acceptance of these terms and conditions.</p>
	            	<p>2.3  You shall receive the confirmation of seat(s) from the System through the e-mail address provides by the Customer in the System upon paying for the seat(s).</p>
	            	<p>2.4  You shall not decide on the arrangement of seat(s) during the event.</p>
	            	<p>2.5  A valid seat(s) confirmation must be produced to get into the event. Seat is valid on the date and time of the event only. 1 (One) seat admits 1 (One) person only.</p>
	            	<p>2.6  Removing any part of, altering or defacing the seat confirmation may invalidate your seat. It is your responsibility to check your seat during the booking process on our System. Any mistake made during the booking cannot be rectified after the booking. Please check your seat confirmation carefully and contact us immediately if there is a mistake.</p>
	            	<p>2.7  The resale of seat(s) is prohibited. We reserve the right to cancel any seat(s) that have been resold.</p>
	            	<p>2.8  Any use of the Flydining System website, its contents and data for any unlawful activities, as well as for the purpose of sale or resale of seat(s), is prohibited without organizer prior written consent. We shall have the right to charge a contractual penalty for breach of contract in the amount of up to Rs.10,00,000/- from any customer who has violated this provision.</p>
	            	<p>2.9  Once seat(s) are sold, no refund, amendment or cancellations will be entertained.</p>
	            	<p>2.10  We will not be responsible for any seat(s) confirmation that is lost, stolen or destroyed.</p>
	            	<p>2.11  The Organizer only accepts responsibility for seat(s) bought from one of its own managed sales points.</p>
	            	<p>2.12  Ownership or possession of a seat(s) confirmation does not confer any rights (by implication or otherwise) on you to use, alter, copy or otherwise deal with any symbols, trademarks, logos and/or intellectual property appearing on the seat(s) confirmation.</p>
	            	<p>2.13  Any seat(s) confirmations obtained in breach of these terms and conditions shall be void and all rights conferred or evidenced by such seat(s) shall be void. Any person seeking to use such a void seat(s) in order to gain or provide entry to our event may be considered to be a trespasser and may be liable to be ejected and liable to legal action. Void seat(s) are non-refundable.</p>
	            	<p>2.14  You may transfer the seat(s) and the consideration for seat(s) transfer will however be given if you can provide evidence of double booking, illnesses or other related emergencies the Organizer 48 hours before the dinner through email: blr@flydining.com together with his/her replacement’s details such as Full Name, Identification Card Number/Passport Number, Contact Details etc.</p>
	            	<p>2.15  By performing any action on the System’s site, Customer confirms that:</p>
	            	<p>2.15.1  All prices quoted are in Indian Rupee (INR) and includes local taxes and GST.</p>
	            	<p>2.15.2  It is a legal entity whose representative has all and any powers to use the System services in the name of that person, and to assume any obligations for that legal entity.</p>
	            	<p>2.15.3  The person duly understands and complies with all the Terms of Use of the system.</p>
	            	<p>2.15.4  Pregnant Women are not allowed to experience Flydining.</p>
	            	<p>2.15.5  If the guest is under the age of 18, the presence and signature of parents/guardian that is at least 18 years old and above will be required for indemnity form.</p>

	            	<h3 style="color:#000;">3. PRICE AND PAYMENT</h3>
	            	<p>3.1  The price of the seat(s) shall be the price seat(s) at the time we accept your order.</p>
	            	<p>3.2  Please be advised that additional credit/debit card or banking fees may be charged per seat on certain performances.</p>
	            	<p>3.3  If you pay via credit/debit card, you can find the payment transaction on your credit card statement under the name “Sky Lounge”. The official payment of Sky Lounge is Razorpay.</p>
	            	<p>3.4  Discount/Promotion codes is not applicable in conjunction with other on-going promotions, discounts, vouchers or privilege cards. </p>

	            	<h3 style="color:#000;">4. CHANGES TO EVENT</h3>
	            	<p>The Organizer may postpone, delay, cancel, interrupt or stop the event due to adverse weather, dangerous situations or any other causes beyond reasonable control.</p>

	            	<h3 style="color:#000;">5. LIABILITY</h3>
	            	<p>In the event of the event being delayed, cancelled or postponed, the Organizer cannot be held responsible for any costs incurred by the customer for travel, accommodation or any other related service. Decisions to postpone or delay events are not under the Organizer’s control; therefore, we are not liable and will not offer compensation or refunds of any costs incurred. A full refund (on the fee of the seat only) will be given if the Organizer makes the cancellation.</p>

	            	<h3 style="color:#000;">6. CANCELLED OR RE-SCHEDULED OF EVENT</h3>
	            	<p>It is your responsibility to ascertain whether the Event has been cancelled or re-scheduled and the date and time of any re-scheduled event. Where an event is cancelled or re-scheduled, the organizer will use our reasonable and best endeavors to notify you. You can cancel the tickets upto 4 hours before the show, the inconvenience charge of 40% will be deducted for each resheduling or cancellation.</p>

	            	<h3 style="color:#000;">7. USE OF DETAILS AND DATA</h3>
	            	<p>Information is collected from those registering in the System in order to facilitate seat(s) booking or other services available.</p>

	            	<h3 style="color:#000;">8. CONDITIONS OF ADMISSION</h3>
	            	<p>8.1   You have to be at least 13 years old and above to be able to take part in the Flydining experience. If the guest(s) is under the age of 18, the presence and signature of parents/guardian that is at least 18 years old and above will be required for indemnity form.</p>
	            	<p>8.2  Punctuality is very important for the Flydining experience. You are advised to arrive at the exact scheduled time of your package. If there is a delay on your arrival and the platform has already been lifted, the Organizer will not lower the platform for you and your seat(s) will be cancelled automatically, with no refund will be given.</p>
	            	<p>8.3  You are advised to dress accordingly to the weather. Please be advised that at 50 Meters height the wind can be stronger and the temperature can be lower than usual. Once the platform has already been lifted, the Organizer will not lower the platform for you if you request to change your attire, shoes, etc.</p>
	            	<p>8.4  The Organizer cannot guarantee that participating chefs and restaurants will prepare dishes without common allergens, such as nuts, dairy, gluten etc. You are advised to go through the menu at our website before purchasing a seat(s). You shall be responsible for your own health and safety and shall observe any and all precautions regarding your medical condition inter alia diabetes, food allergies etc.</p>
	            	<p>8.5   The Organizer reserves the right to refuse or deny your admission to the venue in reasonable circumstances including health and safety matters, licensing reasons or where a seat(s) is void. The Organizer also reserves the right to request you to leave the venue at any point of time on any reasonable grounds and may take any appropriate action to enforces this right.</p>
	            	<p>8.6  By way of example, the Organizer may remove anybody who:</p>
	            	<p>8.6.1  behaved in a manner which in the reasonable opinion of the organizer has, or is likely to affect the enjoyment of other Customers</p>
	            	<p>8.6.2  uses threatening, abusive or insulting words or behavior or in any way provokes or behaves in a manner which may disturb the peace of the Organizer and other Customers</p>
	            	<p>8.6.3  in the reasonable opinion of the Organizer, you are acting under the influence of alcohol or drugs.</p>
	            	<p>8.7  You must comply with the instructions and directions given by the Organizer, the staff and stewards on duty. No refunds will be given who has denied entry due to their own behavior as suggested in, but not limited to, the examples above.</p>
	            	<p>8.8  You voluntarily assume all risk and danger incidental to the event whether occurring prior to, during or subsequent to the actual event, including any death, personal injury, loss, damage or liability.</p>
	            	<p>8.9  Flydining does not limit in any way the participation of any disabled person, but the said individual MUST provide sufficient information (in writing) regarding their medical conditions to the Organizer prior to purchasing a seat(s). The Organizer will then assess the situation and people trained in safety experience will decide whether the individual may or may not participate.</p>

	            	<h3 style="color:#000;">9. SAFETY RULES FOR THE EVENT</h3>
	            	<p>9.1  The Organizer has the discretion to decide on the height of the table once it is lifted, based on the recommendation from our safety officer at any particular day or time.</p>
	            	<p>9.2  In the case of adverse weather conditions, the Organizer has the rights to delay the dining session (at the Organizer’s discretion but with a maximum delay of 1.5 hours only). If the condition of the weather does not improve, the dining session will be moved to our indoor lounge. Your menu and serving style remain the same. Please note that no refund will be given in such cases.</p>
	            	<p>9.3  For safety reasons, you must be at least 145cms of height and a maximum weight of 150kgs of weight to enjoy the Flydining.</p>
	            	<p>9.4  We suggest you to avoid wearing open shoes or shoes without laces on the day of the event.</p>
	            	<p>9.5  Safety belts are for your protection. Leave them as they are and do not try to open them at any point during the dinning session.</p>
	            	<p>9.6  It is strictly forbidden to throw/release anything from the table at any time. Failure to adhere to this condition will lead to ejection and shall be asked to leave the venue at any point of time with no refund will be given.</p>
	            	<p>9.7  Smoking of conventional and or electronic cigarette/cigar is strictly prohibited during the Flydining experience. Failure to adhere to this condition will lead to seat(s) riders being asked to leave the venue at any point  with no refund will be given.</p>
	            	<p>9.8  Only small items such as cell phone or compact cameras    will be allowed to be brought together with you to the table during the dinning session. Strictly no bouquet of flowers or large handbags are allowed on board as lockers will be provided  for large items as mentioned above. It is your responsibility to keep your personal belongings like cell phones, cameras safe. The Organizer is not to be held responsible for the damage or loss of your items, if any.</p>
	            	<p>9.9  You are obliged to adhere and obey any instructions given by staff of Flydining at all times. Touching any operational device or machinery is strictly prohibited and may result to legal actions</p>
	            	<p>9.10  If you have questions or concerns about our Privacy Policy or your data, Terms and Conditions for using System, please feel free to contact us by email: blr@flydining.com.</p>

	            	<h3 style="color:#000;">10. RESTRICTIONS AND PROHIBITIONS</h3>
	            	<p>10.1  The use of equipment for recording or transmitting (by digital or other means) any audio, video or audio-visual material or any information or data inside the event venue is strictly forbidden, unless for personal use only. Unauthorized recordings, tapes, films or similar items may be confiscated and destroyed. Any recording made during the Event constitutes a breach of these conditions and shall automatically belong to the Organizer. The Organizer will not be held liable for any loss, theft or damage to any confiscated items.</p>
	            	<p>10.2  The Organizer reserves the right to use any photograph/video taken at any Flydining® event, without the expressed written permission of those included within the photograph/video. The Organizer may use the photograph/video in publications or other media material produced, used or contracted by the organizer including but not limited to: brochures, invitations, books, newspapers, magazines, television, websites, etc. </p>
	            	<p>10.3  Any person desiring not to have their photo taken or distributed must inform the Organizer in writing of his/her intentions and include his or her photograph. The Organizer will use the photo for identification purposes and will hold it in confidence.</p>
	            	<p>10.4  The following are not permitted within any venue:</p>
	            	<p>10.4.1  Pets, animals </p>
	            	<p>10.4.2  Your own food and drink (unless permitted by the Organizer) </p>
	            	<p>10.4.3  Bottles, cans or glass containers (unless permitted by the Organizer);</p>
	            	<p>10.4.4  Any item which may be interpreted as a potential weapon including sharp or pointed objects (e.g. knives)</p>
	            	<p>10.4.5  Illegal substances </p>
	            	<p>10.4.6  Professional/ commercial recording devices.</p>

	            	<h3 style="color:#000;">11. WEATHER CONDITIONS</h3>
	            	<p>The Organizer has the full rights to move the Flydining experience indoors at a ground level lounges, subject to conditions deemed unsafe as the following (with no refunds given):</p>
	            	<p>11.1  Heavy rain/thunderstorm</p>
	            	<p>11.2  Haze</p>
	            	<p>The Organizer has the full rights to completely cancel any dining session subject to conditions deemed unsafe such as the following (with full refunds given for seat booked only):</p>
	            	<p>11.3  Power failure by the venue provider</p>
	            	<p>11.4  Flooding, hurricane, earthquake or any force major at the venue</p>
	            	<p>11.5  Any politically motivated restriction/strike/curfew at venue or its surrounding</p>

	            	<h3 style="color:#000;">12. REFUNDS</h3>
	            	<p>12.1  Seat(s) booked cannot be refunded, exchanged or cancelled once issued except in the event of a cancellation by the Organizer themselves. Moving the dinner experience into the event lounge is NOT considered a cancelation and therefore will NOT be refunded.</p>
	            	<p>12.2  If an event is cancelled by the Organizer, the following situation will take place: For ONLINE credit or debit card bookings, we will work with your respective card’s banks to automatically debit the amount back into your This process takes approximately 14 working days after the Event.</p>
	            	<p>12.3  If the Event is postponed, booked seat(s) for the original date will be valid for the new date unless otherwise notified. You will have the option to seek a refund for the value of the booked seat(s) for a postponed event as below: a) a refund form will be  uploaded on our website. You will need to fill up this  form, attach proof of booking and send it back to us via Email (blr@flydining.com) or to our Office Address (Sky Lounge Sadguru Prerna Appt.-Block-A, Flat No.- 1002, B/h Rani towers Yogi Park Street No. 02, Kalawad Road, Rajkot-360005, Gujarat, India).</p>
	            	<p>12.4  Please be informed that the refund process will need at least 14 working days from the date of receiving your refund application form.</p>

	            	<h3 style="color:#000;">13. USE OF DETAILS AND DATA</h3>
	            	<p>13.1  This gift-card IS NOT THE ACTUAL TICKET.</p>
	            	<p>13.2  You will need to REDEEM this voucher to a ticket by going to our website: www.flydining.com or through phone call.</p>
	            	<p>13.3  Any additional cost exceeding the value of this voucher will be paid by the redeemer.</p>
	            	<p>13.4  This gift-card cannot be used for special event days.</p>
	            	<p>13.5  This gift-card cannot be refunded or exchanged for cash.</p>
	            	<p>13.6  The redemption is only possible/ valid, subject to availability. You may refer to our website (www.flydining.in) on the available dates.</p>
	            	<p>13.7  There is No Weather Guarantee included together with this voucher. You may choose to add-on the guarantee on our website.</p>
	            	<p>13.8  This gift voucher will not be replaced if its lost, damaged or stolen.</p>
	            	<p>13.9  Fly Dining reserves the right to amend these terms and conditions without prior notice.</p>
	            	<p>13.10  Kindly fix an appointment TWO day before collection via Email or Phone Call.</p>	
			    </div>
	            <div id = "footerDiv" class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
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
		   //$("#amount").val("<?php //echo $totalAmountInPaise?>");
		   //saveBooking();
		   //return;
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
			options.notes["RescheduleBookingId"] = $("#rescheduleBookingId").val();
			if($("#isAddCake").prop('checked') == true){
				options.notes["CakePrice"] = $("#cakePrice").val();	
				options.notes["Notes"] = $("#notes").val();	
			}
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
