<?include("SessionCheck.php");
require_once('IConstants.inc');
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/TimeSlotMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/PackageMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/OccasionMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/BookingMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/BookingAddOnMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/BookingDetailMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/DiscountCouponMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Utils/DateUtil.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Enums/BookingStatus.php");

$timeSlotMgr = TimeSlotMgr::getInstance();
$timeSlots = $timeSlotMgr->findAll();
$packagesMgr = PackageMgr::getInstance();
$packages = $packagesMgr->getAllWithOccasions();

$ocassionMgr = OccasionMgr::getInstance();
$occasions = $ocassionMgr->findAll();
$booking = New Booking();
$relatedBooking = null;
$isRecheduled = false;
$bookingDetailJson = "";
$bookedOn = "";
$disabled = "";
$bithDate = "";
$discountCoupons = array();
$isfillCake = "";
$notes = "";
$cakePrice = "500";
if(isset($_POST["isView"])){
	$isView = $_POST["isView"];
	if(!empty($isView)){
		$disabled = "disabled";
	}
}
$bookingStatus = "";
$parentBookingSeq = 0;
$bookingAddOn = new BookingAddOn();
$allBookingStatus = BookingStatus::getAll();
if(isset($_POST["seq"])){
	$bookingSeq = $_POST["seq"];
	$bookingManager = BookingMgr::getInstance();
	$booking = $bookingManager->findBySeq($bookingSeq);
	$bookingStatus = $booking->getStatus();
	if($bookingStatus == BookingStatus::rescheduled){
		$relatedBooking = $bookingManager->getBookingByParentId($bookingSeq);
		$isRecheduled = true;
	}
	$parentBookingSeq = $booking->getParentBookingSeq();
	if(!empty($parentBookingSeq)){
		$relatedBooking = $bookingManager->findBySeq($parentBookingSeq);
	}else{
		$parentBookingSeq = 0;
	}
	$bookingDate = $booking->getBookingDate();
	$bookedOn = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$bookingDate);
	$bookedOn = $bookedOn->format("d-m-Y");
	$bithDate = $booking->getDateOfBirth();
	if(!empty($bithDate)){
		$bithDate = DateUtil::StringToDateByGivenFormat("Y-m-d",$bithDate);
		$bithDate = $bithDate->format("d-m-Y");
	}
	$bookingDetailMgr = BookingDetailMgr::getInstance();
	$bookingDetail = $bookingDetailMgr->getDetailByBookingSeqAndTimeSlot($bookingSeq, $booking->getTimeSlot());
	$bookingDetailJson = json_encode($bookingDetail);
	$bookingAddOnMgr = BookingAddOnMgr::getInstance();
	$bookingAddOn = $bookingAddOnMgr->findByBookingSeq($bookingSeq);
	if(!empty($bookingAddOn)){
		$isfillCake = "checked";
		$notes = $bookingAddOn->getNotes();
		$cakePrice = $bookingAddOn->getPrice();
	}
}
$discountCouponMgr = DiscountCouponMgr::getInstance();
$discountCoupons = $discountCouponMgr->getAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Bookings</title>
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
	                        <h5>Create Bookings</h5>
	                    </div>
	                    <div class="ibox-content">
	                    	
	                        <form id="bookingForm" method="post" action="Actions/BookingAction.php" class="m-t-lg">
	                        		<input type="hidden" id ="call" name="call"  value="saveBookingsFromAdmins"/>
	                        		<input type="hidden" id ="seq" name="seq"  value="<?php echo $booking->getSeq() ?>"/>
	                        		<input type="hidden" id ="cakePrice" name="cakePrice"  value="<?php echo $cakePrice?>"/>
	                        		<input type="hidden" id ="bookingid" name="bookingid"  value="<?php echo $booking->getBookingId() ?>"/>
	                        		<input type="hidden" id ="discountCouponType" name="discountcoupontype"/>
	                        		<input type="hidden" id ="parentbookingseq" name="parentbookingseq"  value="<?php echo $parentBookingSeq ?>"/>
	                        		<input type="hidden" id ="availableSeats" name="availableSeats"/>
	                       			<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Booking Date</label>
	                                    <div class="col-lg-2">
	                                    	<input type="text" id="bookingDate" <?php echo $disabled?>  value="<?php echo $bookedOn?>" onchange="loadData()" name="bookingDate" required placeholder="Select Date" class="form-control">
	                                    </div>
	                                </div>
	                               		<div class="form-group row">
	                               			<label class="col-lg-2 col-form-label">FullName</label>
	                                		<div class="col-lg-4">
	                               				<input type="text" <?php echo $disabled?> value="<?php echo $booking->getFullName()?>" id="fullName" name="fullName" required 
			                                    placeholder="FullName" class="form-control">
		                                    </div>
		                                </div>
		                                <div class="form-group row">
		                                	<label class="col-lg-2 col-form-label">Mobile</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" <?php echo $disabled?> value="<?php echo $booking->getMobileNumber()?>" id="mobile" name="mobile" required 
			                                    placeholder="Mobile" class="form-control">
		                                    </div>
		                                </div>
		                                 <div class="form-group row">    
		                                 	<label class="col-lg-2 col-form-label">Email</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" <?php echo $disabled?> value="<?php echo $booking->getEmailId()?>" id="email" name="email" required 
			                                    placeholder="Email" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">DOB</label>
		                                    <div class="col-lg-4">
		                                    	<input type="text" id="dateofbirth" name="dateofbirth" value="<?php echo $bithDate?>" required placeholder="Date of Birth" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
			                       			<label class="col-lg-2 col-form-label">Country</label>
			                                   <div class="col-lg-4">
			                                   	<?php include 'countryList.php';?>
			                                   </div>
		                                </div>
	                                	<div class="form-group row">
		                                	<label class="col-lg-2 col-form-label">Payment Id</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" <?php echo $disabled?> value="<?php echo $booking->getTransactionId()?>" id="paymentid" name="paymentid"
			                                    placeholder="Payment Id" class="form-control">
		                                    </div>
		                                </div>
		                                <div class="form-group row">
		                                	<label class="col-lg-2 col-form-label">GST No.</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" <?php echo $disabled?> value="<?php echo $booking->getGSTNumber()?>" id="gstno" name="gstno" 
			                                    placeholder="GST No." class="form-control">
		                                    </div>
		                                </div>
		                                <div class="form-group row">
		                                	<label class="col-lg-2 col-form-label">Company Name</label>
		                                    <div class="col-lg-4"> 
			                       				<input type="text" <?php echo $disabled?> value="<?php echo $booking->getCompanyName()?>" id="companyName" name="companyname" 
			                                    placeholder="Company Name" class="form-control">
		                                    </div>
		                                </div>
		                                <div class="form-group row">
		                                	<label class="col-lg-2 col-form-label">Company Number</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" <?php echo $disabled?> value="<?php echo $booking->getCompanyMobile()?>" id="companyMobile" name="companymobile" 
			                                    placeholder="Company Number" class="form-control">
		                                    </div>
		                                </div>
		                                <div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Company State</label>
			                                    <div class="col-lg-4">
			                                    	<?php include 'stateList.php';?>
			                                    </div>
		                                	</div>
		                                 <div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Time Slot</label>
		                                    <div class="col-lg-4">
		                                    	<select class="form-control chosen-select" <?php echo $disabled?> onchange="loadData()" required id="timeSlot" name="timeSlot">
													<?php foreach ($timeSlots as $timeSlot){
														$seq = $timeSlot->getSeq();
														$selected = "";
														if($seq == $booking->getTimeSlot()){
															$selected = "selected";
														}
														?>
														<option <?php echo $selected ?> value="<?php echo $timeSlot->getSeq()?>"><?php echo $timeSlot->getTitle()?></option>
														
													<?php }?>
												</select> <label class="jqx-validator-error-label" id="lpError"></label>
								    		</div>
                               			</div>
                               			<div id="dataDiv">
	                              		</div>
	                               		<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Discount Coupon</label>
			                                    <div class="col-lg-4">
			                                    	<select class="form-control chosen-select" <?php echo $disabled?> required id="couponSeq" onchange="applyDiscount()" name="couponSeq">
														<option value="0">Select Coupon</option>
														<?php foreach ($discountCoupons as $discountCoupon){
															$seq = $discountCoupon->getSeq();
															$percent = $discountCoupon->getPercent();
															$selected = "";
															if($seq == $booking->getCouponSeq()){
																$selected = "selected";
															}
														    $text = $discountCoupon->getPercent() . "%";
														    $id = $seq . "_percent";
														    if(!empty($discountCoupon->getMaxAmount())){
														    	$text = "Rs." . $discountCoupon->getMaxAmount();
														    	$id = $seq . "_amount";
														    	$percent = $discountCoupon->getMaxAmount();
														    }
															?>
															<option <?php echo $selected ?> id="<?php echo $id?>" value="<?php echo $seq . "_" .$percent?>"><?php echo $discountCoupon->getCode()?> (<?php echo $text?>)</option>
														<?php }?>
													</select>
									    		</div>
									    		<?php if(!empty($booking->getSeq())){
									    			$discountText = $booking->getDiscountPercent() . "%";
									    			if(!empty($booking->getDiscountAmount())){
									    				$discountText = "Rs." . $booking->getDiscountAmount();
									    			}
									    		?>
										    		<div class="col-sm-2">
										    			<input type="text" id="discountPercent" disabled value="<?php echo $discountText?>" class="form-control">
										    		</div>
										    		
									    		<?php }?>
                               			</div>	
                               			
                               			<div class="form-group row" style="display:none">
                               			    <label class="col-lg-2 col-form-label">Add On</label>
		                                	<div class="col-lg-4" >
		                                	   	<input class="i-checks" <?php echo $isfillCake?> type="checkbox"  name="isAddCake" id="isAddCake" >  Add Cake <small>(Rs. 500/-)</small>
		                                       </div>
		                                 </div>
		                                 <div id="addOnDiv" style="display:none">
		                                	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Notes</label>
			                                    <div class="col-lg-4">
			                                    	<textarea maxLength="500" name="notes" placeholder="Add Notes" class="form-control" ><?php echo $notes?></textarea>
			                                    </div>
		                                	</div>
		                                </div>
		                                <div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Package</label>
		                                    <div class="col-lg-4">
		                                    	<select class="form-control" <?php echo $disabled?> onchange="addPackage(this.value)"  required id="packageSeq" name="packageSeq">
		                                    		<option value="0">Select Package</option>
													<?php 
														foreach ($packages as $package){
															$seq = $package[1];
															$selected = "";
															if($seq == $booking->getPackageSeq()){
																$selected = "selected";
														}
													?>
														<option <?php echo $selected ?> value="<?php echo $package[1]?>">
																<?php echo $package['occasion'] ." - ".$package['title']?>
														</option>
														
													<?php }?>
												</select> <label class="jqx-validator-error-label" id="lpError"></label>
								    		</div>
								    		 <div class="col-lg-2">
								    		 	<input type="text" name="packagePrice" id="packageprice" onchange="applyDiscount()" placeholder="Package Price" value="<?php echo $booking->getPackagePrice()?>" class="form-control">	
								    		 </div>
                               			</div>
                               			<!-- <div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Occasion</label>
		                                    <div class="col-lg-4">
		                                    	<select class="form-control chosen-select" <?php //echo $disabled?> required id="occasionseq" name="occasionseq">
		                                    		<option value="0">Select Occasion</option>
														<?php //foreach ($occasions as $occasion){
															//$seq = $occasion->getSeq();
															//$selected = "";
															//if($seq == $booking->getOccasionSeq()){
																//$selected = "selected";
															//}
														  ?>
														<option <?php //echo $selected ?> value="<?php //echo $seq?>"><?php //echo $occasion->getTitle()?></option>
														
													<?php //}?>
												</select> <label class="jqx-validator-error-label" id="lpError"></label>
								    		</div>
                               			</div> -->
                               			<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Final Amount</label>
			                                    <div class="col-lg-4 finalAmount"></div>
									    		
                               			</div>	
	                                <?php if(!empty($booking->getSeq())){?>
                               			<div class="form-group row">
                               			    <label class="col-lg-2 col-form-label">Booking Id</label>
		                                	<span class="col-lg-4"><?php echo $booking->getBookingId()?></span>
		                                       
		                                 </div>
		                             	<div class="form-group row">
			                       				<label class="col-lg-2 col-form-label">Status</label>
			                                    <div class="col-lg-4">
			                                    	<select class="form-control chosen-select" <?php echo $disabled?> id="status" name="status">
														<option value="">Select Status</option>
														<?php foreach ($allBookingStatus as $key=>$bookingStatus){
															$selected = "";
															if($booking->getStatus() == $bookingStatus){
																$selected = "selected";
															}
															?>
															<option <?php echo $selected ?> value="<?php echo $bookingStatus?>"><?php echo $bookingStatus?></option>
														<?php }?>
													</select>
									    		</div>
									   </div>	
                               	     <?php }else{?>
                               	     		<input type="hidden" id ="status" name="status"  value="<?php echo $booking->getStatus() ?>"/>
                               	     <?php }?>
	                                <?php if(!empty($relatedBooking)){ 
	                                		$status = "";
	                                		if($isRecheduled){
	                                			 $status = "Rescheduled to booking id  - " . $relatedBooking->getSeq();
	                                		}else{
	                                			$status = "Rescheduled from booking id  - " .$relatedBooking->getSeq();
	                                		}
	                                	    
		                                	if(!empty($booking->getSeq())){?>
	                               				<div class="form-group row">
				                        		<label class="col-lg-2 col-form-label">Status</label>
				                                <div class="col-lg-4"><?php echo $status?></div>
									  		</div>	
	                               		<?php }
	                                }?>
                                	<hr>
                                 	<div class="form-group row">
                                		<div class="col-lg-2">
                                			<?php if(empty($disabled)){?>
	                                		<button class="btn btn-primary" onclick="submitBookingForm()" type="button" id="rzp-button">
	                                			Save Booking
		                                	</button>
		                                	<?php }?>
	                                	</div>
	                                	<div class="col-lg-2">
                                			<button class="btn btn-default" onclick="cancel()" type="button">
	                                			Cancel
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
	 	isSelectAll = false;
	 	var totalAmount = 0;
		var discountPercent = "<?php echo $booking->getDiscountPercent()?>";
		var discountAmount = "<?php echo $booking->getDiscountAmount()?>";
        $(document).ready(function(){
        	$('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
           $('#bookingDate').datetimepicker({
               timepicker:false,
               format:'d-m-Y',
               minDate:new Date()
           });
           $('#dateofbirth').datetimepicker({
               timepicker:false,
               format:'d-m-Y',
           });
           currDate = getCurrentDate();
           selectedDate = "<?php echo $bookedOn ?>";
           if(selectedDate == ""){
        	   selectedDate = currDate; 
           }
       	   $('#bookingDate').val(selectedDate);
	       loadData();
	       $('#companyState').val("<?php echo $booking->getGstState()?>");
	       $('#country').val("<?php echo $booking->getCountry()?>");
	       $('#isAddCake').on('ifChanged', function(event){
		   		var flag  = $("#isAddCake").is(':checked');
		   		if(flag){
		   			$('#addOnDiv').show();
		   		}else{
		   		   $('#addOnDiv').hide();
		   		}
		   		applyDiscount()
	   		});
        });
        function addPackage(packageSeq){
        	$.getJSON("Actions/PackageAction.php?call=getPackagePrice&id="+packageSeq, function(data){
            	var success = data.success;
            	if(success == 1){
                	$("#packageprice").val(data.price);	
                	applyDiscount();
            	}else{
                	alert(data.message);
            	}		 
        	});  
        }
        function loadData(){
            var selectedBookingDetail = '<?php echo $bookingDetailJson?>';
            var bookingDetailJsonObject;
            if(selectedBookingDetail != ""){
            	bookingDetailJsonObject = $.parseJSON(selectedBookingDetail);
            }
            var selectedDate = $("#bookingDate").val();
            var timeSlotSeq =  $("#timeSlot").val();
            var bookingSeq = $("#seq").val()
            var html = "";
        	$.getJSON("Actions/MenuAction.php?call=getMenusByTimeSlot&timeSlotSeq="+timeSlotSeq + "&selectedDate="+selectedDate + "&bookingSeq="+bookingSeq, function(data){
            	menus = data.menus;
            	availableSeats = data.availableSeats;
            	totalSeats = data.totalSeats;
            	totalSelectedSeats = data.totalSelectedSeats;
            	selectedSeats = data.selectedSeats;
            	if(bookingDetailJsonObject != null){
            		availableSeats += parseInt(selectedSeats)
            	}
            	html = '<div class="form-group row">';
            	html += '<label class="col-lg-2 col-form-label">Menu</label>';
            	html += '<div class="col-lg-2">';
            	$.each(menus, function(index , menu){
                	html += '<input type="text" disabled id="menu" value="'+menu.title+'" name="menu" class="form-control"><br>';					
        		});
        		html += '</div>';
        		html += '<div class="col-lg-1">';
        		menuAmount = [];
               	$.each( menus, function( k, menu ) {
               		var selectedSeat = 0;
               		if(bookingDetailJsonObject != null){
               			bookingDetail = getMembers(k,bookingDetailJsonObject);
               			selectedSeat = bookingDetail["members"];
               			menuPrice = bookingDetail["menuprice"];
               			var rate = menu.rate;
               			if(menuPrice != null && menuPrice != "" && menuPrice != "0" && menuPrice > 0){
               				rate = menuPrice;
               			}
               			menuAmount[k] = 0;
               			if(selectedSeat > 0){
               				menuAmount[k] = rate * selectedSeat;
               				totalAmount += rate * selectedSeat;	
               			}
               			
               			//if(selectedSeat > 0){
               			//	seats += parseInt(selectedSeat);
               			//}
               		}
               		html += '<select id="'+k +'_selectedSeats" <?php echo $disabled?> onchange="calculateAmount('+ k + ',' + menu.rate +')" name="selectedSeats[]" required class="form-control">';
	           		html += '<option id="0">0</option>';
	                for(var i = 1; i <= availableSeats; i++){
		                if(i == selectedSeat){
		                	html += '<option selected value="'+k+'_' +i+'">'+i+'</option>';	  
		                }else{
		                	html += '<option value="'+k+'_' +i+'">'+i+'</option>'
		                }	
	           		}
	                html += '</select><br>';
                });
                html += '</div>';
				html += '<div class="col-lg-2">';
				$("#availableSeats").val(availableSeats);
                $.each( menus, function( k, menu ) {
                    var selectAmount = 0;
                    if(menuAmount.length > 0){
                    	selectAmount = menuAmount[k];
                    }
                	html += '<input type="text"onchange="applyDiscount()" <?php echo $disabled?> id="'+k+'_amount" value="'+selectAmount+'" name="amount[]" required  class="form-control menuPrices">';
                	html += '<br>';
                });
           		html += '</div>'
        		$("#dataDiv").html(html);
        		if(discountPercent != 0){
        			var discount = (discountPercent / 100) * totalAmount
					totalAmount = totalAmount - discount;
					$("#discountCouponType").val("percent");
            	}
        		if(discountAmount != 0){
            		discountAmount = parseInt(discountAmount)
            		if(discountAmount >totalAmount){
            			totalAmount = 0;
            		} else{
            			totalAmount = totalAmount - discount;
            		}
            		$("#discountCouponType").val("amount");	
        		}
        		var isAddCake = $('#isAddCake').is(":checked")
     	        if(isAddCake){
            		var cakePrice = $("#cakePrice").val()
            		totalAmount += parseInt(cakePrice);
     	        }
        		totalAmount = addPackageAmount(totalAmount);
            	$(".finalAmount").html("Rs. "+ totalAmount);
      		});
      		
        }
        
        function getMembers(value,jsonObject){
            var members = 0;
            var bookingDetail = [];
        	$.each( jsonObject, function( index, val ) {
            	if(val.menuseq == value && members == 0){
            		members =  val.members;	
            		bookingDetail["members"] = val.members;
            		bookingDetail["menuprice"] = val.menuprice;
            	}
        	});
        	return bookingDetail;
        }
        
		function calculateAmount(menuSeq,menuRate){
			var selectedSeats = $("#"+menuSeq+"_selectedSeats option:selected").text();
			selectesSeats = parseInt(selectedSeats);
			var amount = selectedSeats * menuRate;
			$("#"+menuSeq+"_amount").val(amount);
			var sum = 0;
		    $(".menuPrices").each(function(){
		        sum += +$(this).val();
		    });
		    var coupon = $("#couponSeq").val();
		    if(coupon != "0" && coupon != 0){
		    	var seqAndPercent = coupon.split("_");
		    	var percent = parseInt(seqAndPercent[1]);
		    	var discount = (percent / 100) * sum;
		    	sum = sum - discount;
		    }
			$(".finalAmount").html("Rs. "+ sum);
			applyDiscount();
		}
		
        function getHeaders(){
        	var html = '<div class="row ibox-content">'
        		html += '<div class="col-xs-2">Time Slot</div>';
        		html += '<div class="col-xs-3">Menu</div>';
        		html += '<div class="col-xs-3">Seats</div>';
        		html += '<div class="col-xs-2">FullName</div>'
        		html += '<div class="col-xs-2">Mobile</div>'
        		html += '<div class="col-xs-2">Email</div>'
        		html += '</div>';
        		return html;
        }

        function submitBookingForm(){
        	if($("#bookingForm")[0].checkValidity()) {
        		var dateofbirth = $("#dateofbirth").val();
        		if(getAge(dateofbirth) <= 12) {
        		    alert("You have to be more than 12 years old!");
        		    return;
        		}
	            i = 1;
	            var flag = false;
	           	var timeSlotSeq = this.value;
	            	var hasSeatSelected = false;
	            	var totalSelectedSeats = 0;
	        		$('select[name="selectedSeats[]"]').each(function(){
	            		var selectedSeats = this.value;
	            		totalSelectedSeats += parseInt(this.selectedOptions[0].text);
	            		if(selectedSeats != "0"){
	            			hasSeatSelected = true;	
	            			flag = true;				
	            		}	
	        		});
	        		i++;
	            if(!flag){
	            	alert("No seat selected to save booking");
	            	return;    
	            }
	            var availableSeats = $("#availableSeats").val();
	            if(availableSeats != null){
	            	availableSeats = parseInt(availableSeats);
	            	if(totalSelectedSeats > availableSeats){
	            		alert("Total " + availableSeats + " seat(s) available for selected booking date.");
	            		return false;
	            	}
	            }
	            $('#bookingForm').ajaxSubmit(function( data ){
		       		 var obj = $.parseJSON(data);
		       		 if(obj.success == 1){
		           		 location.href = "dashboard.php";
		       		 }else{
		           		 alert("Error - " + obj.message);
		       		 }	 
	       	 	});
	       	 	
        	}else{
        		$("#bookingForm")[0].reportValidity();
        	}
        } 
        function requiredFullName(input,timeSlotSeq){
            $id = "";
        	$('input[id$="txtVal1"]').each(function(index) { 
        	    // do something here
        	    $(this).addClass( "myClass" );
        	})   
        }
        
        function applyDiscount(){
            var totalAmount = 0;
            var coupon = $("#couponSeq").val();
            var id = $("#couponSeq").find('option:selected').attr('id');
           	$('select[name="selectedSeats[]"]').each(function() {
				var selectedOptions = $(this).val();
				var id = this.id
				id = id.split("_");
				id = id[0];
				var amount = parseInt($("#"+id+"_amount").val());
				totalAmount += amount;  						
			}); 
	        if(coupon != "0" && coupon != 0){  
		        var seqAnddiscountType = id.split("_");
		        var discountType = seqAnddiscountType[1];
		       	var seqAndPercent = coupon.split("_");
			    var percent = parseInt(seqAndPercent[1]);
			    var discount = 0;
			    if(discountType == "percent"){
			    	discount = (percent / 100) * totalAmount;  
			    	$("#discountPercent").val(percent + "%");  
			    	$("#discountCouponType").val("percent");
			    }else{
			    	discount = percent;
			    	$("#discountPercent").val("Rs." + percent);
			    	$("#discountCouponType").val("amount");
			    }
			    if(discount > totalAmount){
			    	totalAmount = 0;
			    }else{
			    	totalAmount = totalAmount - discount;  
			    }
			    
	        }
	        var isAddCake = $('#isAddCake').is(":checked")
	        if(isAddCake){
	        	totalAmount += 500;    
	        }
	        totalAmount = addPackageAmount(totalAmount);
	        $(".finalAmount").html("Rs. "+ totalAmount);
        }

		function addPackageAmount(totalAmount){
			var selectedPackageSeq = $("#packageSeq").val();
	        if(selectedPackageSeq > 0){
		        var packagePrice = $("#packageprice").val();
		        if(packagePrice !=null && packagePrice != ""){
			        packagePrice = parseInt(packagePrice);
			        totalAmount += packagePrice;
		        }
	        }
	        return totalAmount;
		}
        
        function cancel(){
           	location.href = "dashboard.php";
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
</script>
