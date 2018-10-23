<?include("SessionCheck.php");
require_once('IConstants.inc');
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/TimeSlotMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/BookingMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/BookingDetailMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Utils/DateUtil.php");
$timeSlotMgr = TimeSlotMgr::getInstance();
$timeSlots = $timeSlotMgr->findAll();
$booking = New Booking();
$bookingDetailJson = "";
$bookedOn = "";
$disabled = "";
if(isset($_POST["isView"])){
	$isView = $_POST["isView"];
	if(!empty($isView)){
		$disabled = "disabled";
	}
}
if(isset($_POST["seq"])){
	$bookingSeq = $_POST["seq"];
	$bookingManager = BookingMgr::getInstance();
	$booking = $bookingManager->findBySeq($bookingSeq);
	$bookingDate = $booking->getBookingDate();
	$bookedOn = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$bookingDate);
	$bookedOn = $bookedOn->format("d-m-Y");
	$bookingDetailMgr = BookingDetailMgr::getInstance();
	$bookingDetail = $bookingDetailMgr->getDetailByBookingSeqAndTimeSlot($bookingSeq, $booking->getTimeSlot());
	$bookingDetailJson = json_encode($bookingDetail);
}
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
        $(document).ready(function(){
          
           $('#bookingDate').datetimepicker({
               timepicker:false,
               format:'d-m-Y',
               minDate:new Date()
           });
           currDate = getCurrentDate();
           selectedDate = "<?php echo $bookedOn ?>";
           if(selectedDate == ""){
        	   selectedDate = currDate; 
           }
       	   $('#bookingDate').val(selectedDate);
	       loadData();
        });
        
        function loadData(){
            var selectedBookingDetail = '<?php echo $bookingDetailJson?>';
            var bookingDetailJsonObject;
            if(selectedBookingDetail != ""){
            	bookingDetailJsonObject = $.parseJSON(selectedBookingDetail);
            }
            var selectedDate = $("#bookingDate").val();
            var timeSlotSeq =  $("#timeSlot").val();
            var html = "";
        	$.getJSON("Actions/MenuAction.php?call=getMenusByTimeSlot&timeSlotSeq="+timeSlotSeq + "&selectedDate="+selectedDate, function(data){
            	menus = data.menus;
            	seats = data.seats;
            	totalSeats = data.totalSeats;
            	selectedSeats = data.selectedSeats;
            	if(bookingDetailJsonObject != null){
            		seats += parseInt(selectedSeats)
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
               			selectedSeat = getMembers(k,bookingDetailJsonObject);
               			menuAmount[k] = menu.rate * selectedSeat;
               			//if(selectedSeat > 0){
               				//seats += parseInt(selectedSeat);
               			//}
               		}
               		html += '<select id="'+k +'_selectedSeats" <?php echo $disabled?> onchange="calculateAmount('+ k + ',' + menu.rate +')" name="selectedSeats[]" required class="form-control">';
	           		html += '<option id="0">0</option>';
	                for(var i = 1; i <= seats; i++){
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
				$("#availableSeats").val(seats);
                $.each( menus, function( k, menu ) {
                    var selectAmount = 0;
                    if(menuAmount.length > 0){
                    	selectAmount = menuAmount[k];
                    }
                	html += '<input type="text" <?php echo $disabled?> id="'+k+'_amount" value="'+selectAmount+'" name="amount[]" required  class="form-control">';
                	html += '<br>';
                });
           		html += '</div>'
        		$("#dataDiv").html(html);
      		});
       		
        }
        function getMembers(value,jsonObject){
            var members = 0;
        	$.each( jsonObject, function( index, val ) {
            	if(val.menuseq == value && members == 0){
            		members =  val.members;	
            	}
        	});
        	return members;
        }
        
		function calculateAmount(menuSeq,menuRate){
			var selectedSeats = $("#"+menuSeq+"_selectedSeats option:selected").text();
			selectesSeats = parseInt(selectedSeats);
			var amount = selectedSeats * menuRate;
			$("#"+menuSeq+"_amount").val(amount);
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
        function cancel(){
           	location.href = "dashboard.php";
        }
</script>