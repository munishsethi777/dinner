<?php
require_once('IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
require('razorconfig.php');
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
if(!isset($_POST["timeslotseq"])){
	echo ("Invalid Execution");
	die;
}
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
$handlingCharges = 0;
$formatedTotalAmount = 0;
$totalAmountInPaise = 0;
$menuBtnVisible = array(1=>"none",2=>"none",3=>"none");
$menuImgVisible = array(1=>"none",2=>"none",3=>"none");

$menusArr = array();
foreach ($menuArr as $key=>$value){
	if(empty($value)){
		continue;
	}
	$menu = $menuMgr->findBySeq($key);	
	$rate = $menu->getRate();
	$menuHml .= $value . " " . $menu->getTitle() . " - " . $value . " X " . $rate . "<br/>";
	$amount += $value * $rate;
	$totalAmount += $value * $rate;
	$menuBtnVisible[$key] = "inline-table";
	if(!in_array("inline-table", $menuImgVisible)){
		$menuImgVisible[$key] = "inline-table";
	}
	array_push($menusArr, $menu);
}
if(!empty($amount)){
	$amount = number_format($amount,2);
	$totalAmount +=  $handlingCharges;
	$formatedTotalAmount = number_format($totalAmount,2);
	$totalAmountInPaise = $totalAmount * 100;
}
//$totalAmountInPaise = 100;

$api = new Api($keyId, $keySecret);
$orderData = [
		//'receipt'         => 3456,
		'amount'          => $totalAmountInPaise,
		'currency'        => 'INR',
		'payment_capture' => 1
];
$razorpayOrder = $api->order->create($orderData);
$razorpayOrderId = $razorpayOrder['id'];

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
	                       			<div class="row bg-muted p-h-sm">	
	                       				<div class="col-xs-8">
	                       					Sub Total
	                       				</div>
	                       				<div class="col-xs-4 text-right">Rs <?php echo $formatedTotalAmount?></div>
	                       			</div>
	                       			
	                       			<div class="row bg-success p-h-sm text-uppercase font-bold">	
	                       				<div class="col-xs-8">
	                       					AMOUNT PAYABLE
	                       				</div>
	                       				<div class="col-xs-4 text-right">Rs <?php echo $formatedTotalAmount?></div>
	                       			</div>
	                       			<form id="userInfoForm" method="post" action="Actions/BookingAction.php" class="m-t-lg">
	                       				<input type="hidden" id ="call" name="call" value="saveBooking"/>
	                       				<input type="hidden" id ="transactionId" name="transactionId"/>
	                       				<input type="hidden" id ="amount" name="amount"/>
	                       				<input type="hidden" id ="timeslotseq" name="timeslotSeq" value="<?php echo $timeSlotSeq?>" />
	                       				<input type="hidden" id ="selectedDate" name="selectedDate" value="<?php echo $selectedDate?>" />
	                       				<input type="hidden" id ="menupersons" name="menuPersons" value='<?php echo $menus?>' />
		                       			<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Name</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="fullName" name="fullName" required placeholder="FullName" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Email</label>
		                                    <div class="col-lg-10">
		                                    	<input type="email" id="email" name="email" required email placeholder="Email" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Mobile</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="mobile" name="mobile" required placeholder="Mobile" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">GST NO.</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="gst" name="gst" placeholder="GST No." class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
	                                		<div class="col-lg-10" id="termDiv">
	                                        	<label> <input required class="i-checks" type="checkbox" name="termsAndConditions" id="termsAndConditions" ><a data-toggle="modal" data-target="#myModal4" href="#" >   I agree to accept the terms & condition</a></label>
	                                        </div>
	                                    </div>
	                                	<div class="form-group row">
	                                		<div class="col-lg-12">
		                                		<button class="btn btn-primary" id="rzp-button" style="width:100%">
			                                		Make Payment of Rs <?php echo $formatedTotalAmount?>
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
// 	$( ".mockbtn" ).click(function() {
// 		$(".nvegimg").hide();
// 		$(".vegimg").hide();
// 		$(".mockimg").show();
// 	});
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
});


document.getElementById('rzp-button').onclick = function(e){
    //$("#transactionId").val("testid");
    //$("#amount").val("100");
    //saveBooking();
    //return;
	if($("#userInfoForm")[0].checkValidity()) {
		var fullName = $("#fullName").val();
		var email = $("#email").val();
		var mobile = $("#mobile").val();
		var options = {
			    //"key": "rzp_live_KpbxYUeCTzMhDO",
			    "key":"rzp_live_zZ6x7CvsASE4M3",
			    "amount": "<?php echo $totalAmountInPaise?>", // 2000 paise = INR 20
			    "name": "Flydining",
			    "description": "Purchase Description",
			    "image": "https://www.flydining.com/booking/images/logo.png",
			    "handler": function (response){
				    $("#transactionId").val(response.razorpay_payment_id);
				    $("#amount").val("<?php echo $totalAmountInPaise?>");
			        //alert(response.razorpay_payment_id);
			        saveBooking();
			    },
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
		var rzp1 = new Razorpay(options);
	    rzp1.open();
	    e.preventDefault();
    }
    else {
        $("#userInfoForm")[0].reportValidity(); 
    }
	
}
function back(){
	location.href = "index.php";
}
function saveBooking(){
    $('#userInfoForm').ajaxSubmit(function( data ){
        alert("Transaction completed successfuly");
    })
} 
</script> 
