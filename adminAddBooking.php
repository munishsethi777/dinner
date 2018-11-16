<?include("SessionCheck.php");
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
	                       			<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Select Date</label>
	                                    <div class="col-lg-2">
	                                    	<input type="text" onchange="javascript:loadData(this.value)" id="bookingDate" name="bookingDate" required placeholder="Select Date" class="form-control">
	                                    </div>
	                               </div>
	                               		<div class="form-group row">
	                               			<label class="col-lg-2 col-form-label">FullName</label>
	                                		<div class="col-lg-4">
	                               				<input type="text" id="fullName" name="fullName" required 
			                                    placeholder="FullName" class="form-control">
		                                    </div>
		                                </div>
		                                <div class="form-group row">
		                                	<label class="col-lg-2 col-form-label">Mobile</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" id="mobile" name="mobile" required 
			                                    placeholder="Mobile" class="form-control">
		                                    </div>
		                                </div>
		                                 <div class="form-group row">    
		                                 	<label class="col-lg-2 col-form-label">Email</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" id="email" name="email" required 
			                                    placeholder="Email" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                                	<label class="col-lg-2 col-form-label">Payment Id</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" id="paymentid" name="paymentid"
			                                    placeholder="Payment Id" class="form-control">
		                                    </div>
		                                </div>
		                                <div class="form-group row">
		                                	<label class="col-lg-2 col-form-label">GST No.</label>
		                                    <div class="col-lg-4">
			                       				<input type="text" id="gstno" name="gstno" 
			                                    placeholder="GST No." class="form-control">
		                                    </div>
		                                </div>
	                               		<div class="form-group row">
	                                		<div class="col-lg-3">
			                       				<label class="col-form-label">Time Slot</label>
			                                </div>
		                                    <div class="col-lg-3">
			                       				<label class="col-form-label">Menu</label>
			                       	        </div>
		                                    <div class="col-lg-1">
			                       				<label class="col-form-label">Seats</label>
			                                </div>
		                                    <div class="col-lg-2">
			                       				<label class="col-form-label">Amount</label>
			                                </div>
	                                	</div>
                                	
                                		
                                	<div id="dataDiv">
	                              	</div>
                                	<hr>
                                 	<div class="form-group row">
                                		<div class="col-lg-12">
	                                		<button class="btn btn-primary" onclick="submitBookingForm()" type="button"  id="rzp-button" style="width:100%">
	                                			Save Booking
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
       	   loadData(currDate);
       	   $('#bookingDate').val(currDate);
	       $('#bookingForm').jqxValidator({
	       	    hintType: 'label',
	       	    animationDuration: 0,
	       	    rules: [
	       	        { input: '.menuCount', message: 'field is required!', action: 'keyup, blur', rule: 'required'
	       	        }
	       	    ]
	       	});
        });
        
        function loadData(selectedDate){
        	$.getJSON("Actions/TimeSlotAction.php?call=getTimeSlots&selectedDate="+selectedDate, function(data){
      		  //var data = $.parseJSON(jsonString)
      		 var html = "";
      		 var totalAmount = 0;
      		 $.each( data, function( key, val ) {
          		var timeSlotSeq = val.seq;
          		html += '<input type="hidden" value="'+val.seq+'" id ="timeslotseq" name="timeslotseq[]" />'; 
      	 		html += '<div class="form-group row">';
      			html += '<div class="col-lg-3">';
       			html +=  '<input type="text" id="fullName" name="timeSlot" value="'+val.timeslot+'" class="form-control" disabled>';
                html += '</div>';
                html += '<div class="col-lg-3">';
           		var menuList = val.menu
           		$.each( menuList, function( k, menu ) {
           			html +=  '<input type="text" id="menutitle" name="menuTitle" value="'+menu.menutitle+ ' Rs.' + menu.rate + '" class="form-control" disabled><br>';
                });
           		html += '</div>';
           		html += '<div class="col-lg-1">';
           	
           		$.each( menuList, function( k, menu ) {
           			html += '<select id="'+menu.menuseq +'_selectedSeats" onchange="calculateAmount('+ menu.menuseq + ','+timeSlotSeq+','+ menu.rate +')" name="'+timeSlotSeq+'_selectedSeats[]" required class="form-control">';
	           		html += '<option id="0">0</option>';
	                var seats = val.seatsAvailable;
	           		for(var i = 1; i <= seats; i++){
	           			html += '<option value="'+menu.menuseq+'_' +i+'">'+i+'</option>';	
	           		}
	                html += '</select><br>';
                });
                html += '</div>';
				html += '<div class="col-lg-2">';
                $.each( menuList, function( k, menu ) {
                	html += '<input type="text" id="'+menu.menuseq+'_amount" value="0" name="'+timeSlotSeq+'_amount[]" required  class="form-control">';
                	html += '<br>';
                });
           		html += '</div>'
      			html += '</div>';
      			html += '<hr>';
      		});
       		$("#dataDiv").html(html);
      	});	 
        }
		function calculateAmount(menuSeq,timeSlotSeq,menuRate){
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
	        	$('input[name="timeslotseq[]"]').each(function() {
	            	var timeSlotSeq = this.value;
	            	var hasSeatSelected = false;
	        		$('select[name="'+timeSlotSeq+'_selectedSeats[]"]').each(function() {
	            		var selectedSeats = this.value;
	            		if(selectedSeats != "0"){
	            			hasSeatSelected = true;	
	            			flag = true;				
	            		}	
	        		});
	        		i++;
	            });  
	            if(!flag){
	            	alert("No seat selected to save booking");
	            	return;    
	            }
	            $('#bookingForm').ajaxSubmit(function( data ){
		       		 var obj = $.parseJSON(data);
		       		 if(obj.success == 1){
		           		 location.href = "dashboard.php";
		       		 }else{
		           		 alert("Error" + obj.message);
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
</script>