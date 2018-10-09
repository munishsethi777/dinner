<?php
require_once('IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
$timeSlotSeq = $_POST["timeslotseq"];
$selectedDate = $_POST["selectedDate"];
$menus = $_POST["menuMembers"];
$timeSlotMgr = TimeSlotMgr::getInstance();
$timeSlot = $timeSlotMgr->findBySeq($timeSlotSeq);
$menuMgr = MenuMgr::getInstance();
$menuArr = json_decode($menus);
$menuHml = "";
$amount = 0;
$totalAmount = 0;
$handlingCharges = 750;
$formatedTotalAmount = 0;
$totalAmountInPaise = 0;
foreach ($menuArr as $key=>$value){
	if(empty($value)){
		continue;
	}
	$menu = $menuMgr->findBySeq($key);	
	$rate = $menu->getRate();
	$menuHml .= $value . " " . $menu->getTitle() . " - " . $value . " X " . $rate . "<br/>";
	$amount += $value * $rate;
	$totalAmount += $value * $rate;
}
if(!empty($amount)){
	$amount = number_format($amount,2);
	$totalAmount +=  $handlingCharges;
	$formatedTotalAmount = number_format($totalAmount,2);
	$totalAmountInPaise = $totalAmount * 100;
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
									<button class="btn btn-primary btn-rounded vegbtn">VEG</button>
									<button class="btn btn-primary btn-rounded nvegbtn">NON VEG</button>
	                       		</div>
		                       		<div class="row">
		                       			<img class="vegimg" style="height:auto;width:100%" src="images/veg.jpeg">
		                       			<img class="nvegimg" style="height:auto;width:100%;display:none" src="images/nveg.jpeg">
	                       			</div>
                       			</div>
                    	</div>
                    	<div class="col-lg-4">
                    		<div class="ibox-content">
	                       			<h3>BOOKING SUMMARY</h3>
	                       			<div class="row" style="margin-bottom:5px">
	                       				<div class="col-md-8">SLOT <?php echo $timeSlot->getTitle()?></div>
	                       				<div class="col-md-4 text-right"><?php echo "Rs " . $amount?></div>
	                       			</div>
	                       			<div class="row">	
	                       				<div class="col-lg-8">
	                       					<small class="text-muted">
	                       						<?php echo $menuHml?>
	                       					</small>
	                       				</div>
	                       				<div class="col-lg-4 text-right"></div>
	                       			</div>
	                       			
	                       			<div class="row m-b-sm">	
	                       				<div class="col-lg-8">
	                       					<small class="text-muted">
	                       						Internet Handling Fees
	                       					</small>
	                       				</div>
	                       				<div class="col-lg-4 text-right">Rs 750.00</div>
	                       			</div>
	                       			<div class="row bg-muted p-h-sm">	
	                       				<div class="col-lg-8">
	                       					Sub Total
	                       				</div>
	                       				<div class="col-lg-4 text-right">Rs <?php echo $formatedTotalAmount?></div>
	                       			</div>
	                       			
	                       			<div class="row bg-success p-h-sm text-uppercase font-bold">	
	                       				<div class="col-lg-8">
	                       					AMOUNT PAYABLE
	                       				</div>
	                       				<div class="col-lg-4 text-right">Rs <?php echo $formatedTotalAmount?></div>
	                       			</div>
	                       			<form id="userInfoForm" method="post" action="Actions/BookingAction.php" class="m-t-lg">
	                       				<input type="hidden" id ="call" name="call" value="saveBooking"/>
	                       				<input type="hidden" id ="timeslotseq" name="timeslotseq" value="<?php echo $timeSlotSeq?>" />
	                       				<input type="hidden" id ="selectedDate" name="selectedDate" value="<?php echo $selectedDate?>" />
		                       			<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Name</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="fullName" required placeholder="FullName" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Email</label>
		                                    <div class="col-lg-10">
		                                    	<input type="email" id="email" required email placeholder="Email" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Mobile</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="mobile" required placeholder="mobile" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                                		<button class="btn btn-primary col-lg-12" id="rzp-button">
			                                		Make Payment of Rs <?php echo $formatedTotalAmount?>
			                                	</button>
		                                	
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
</body>
</html>
<script src="scripts/FormValidators/FormValidators.js"></script>
<script>
$( document ).ready(function() {
	$( ".vegbtn" ).click(function() {
		$(".nvegimg").hide();
		$(".vegimg").show();
	});
	$( ".nvegbtn" ).click(function() {
		$(".nvegimg").show();
		$(".vegimg").hide();
	});
});


document.getElementById('rzp-button').onclick = function(e){
	if($("#userInfoForm")[0].checkValidity()) {
		var fullName = $("#fullName").val();
		var email = $("#email").val();
		var mobile = $("#mobile").val();
		var options = {
			    "key": "rzp_live_KpbxYUeCTzMhDO",
			    "amount": "<?php echo $totalAmountInPaise?>", // 2000 paise = INR 20
			    "name": "Flydining",
			    "description": "Purchase Description",
			    "image": "/your_logo.png",
			    "handler": function (response){
			        alert(response.razorpay_payment_id);
			    },
			    "prefill": {
				    "contact" : mobile,
			        "name": fullName,
			        "email": email
			    },
			    "notes": {
			        "address": "Hello World"
			    },
			    "theme": {
			        "color": "#1ab394"
			    }
			};
		var rzp1 = new Razorpay(options);
	    rzp1.open();
	    e.preventDefault();
    }
    else {
        $("#userInfoForm")[0].reportValidity(); 
    }
	
}
function saveBooking(){
    $('#userInfoForm').ajaxSubmit(function( data ){
        alert("success");
    })
} 
</script> 