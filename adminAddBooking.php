<?//include("SessionCheck.php");
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
                                	<div>
	                                	<div class="form-group row">
	                                		<div class="col-lg-2">
			                       				<label class="col-form-label">Time Slot</label>
			                                </div>
		                                    <div class="col-lg-2">
			                       				<label class="col-form-label">Menu</label>
			                       	        </div>
		                                    <div class="col-lg-1">
			                       				<label class="col-form-label">Seats</label>
			                                </div>
		                                    <div class="col-lg-2">
			                       				<label class="col-form-label">FullName</label>
			                                </div>
		                                    <div class="col-lg-2">
			                       				<label class="col-form-label">Mobile</label>
			                                </div>
		                                    <div class="col-lg-2">
			                       				<label class="col-form-label">Email</label>
			                                </div>
			                                <div class="col-lg-1">
			                       				<label class="col-form-label">Amount</label>
			                                </div>
	                                	</div>
                                	</div>
                                	<div id="dataDiv">
	                                	<div class="form-group row">
	                                		<div class="col-lg-2">
			                       				<input type="text" id="fullName" name="fullName" 
			                                    value="1PM to 2PM" class="form-control" disabled>
		                                    </div>
		                                    <div class="col-lg-2">
			                       				<select id="fullName" name="fullName" required 
			                                    	placeholder="fullname" class="form-control">
			                                    		<option>Veg</option>
			                                    		<option>Non Veg</option>
			                                    	</select>
			                                </div>
		                                    <div class="col-lg-1">
			                       				<select id="fullName" name="fullName" required 
			                                    	placeholder="fullname" class="form-control">
			                                    		<option>0</option>
			                                    	</select>
		                                    </div>
		                                    <div class="col-lg-2">
			                       				<input type="text" id="fullName" name="fullName" required 
			                                    placeholder="FullName" class="form-control">
		                                    </div>
		                                    <div class="col-lg-2">
			                       				<input type="text" id="fullName" name="fullName" required 
			                                    placeholder="Mobile" class="form-control">
		                                    </div>
		                                    <div class="col-lg-2">
			                       				<input type="text" id="fullName" name="fullName" required 
			                                    placeholder="Email" class="form-control">
		                                    </div>
	                                	</div>
	                                	
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
      			html += '<div class="col-lg-2">';
       			html +=  '<input type="text" id="fullName" name="timeSlot" value="'+val.timeslot+'" class="form-control" disabled>';
                html += '</div>';
                html += '<div class="col-lg-2">';
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
				html += '<input type="text" id="'+timeSlotSeq+'_fullName" name="'+timeSlotSeq+'_fullName" required placeholder="FullName" class="form-control">';
				html += '</div>';
                html += '<div class="col-lg-2">';
           	 	html += '<input type="text" id="'+timeSlotSeq+'_mobile" name="'+timeSlotSeq+'_mobile" required placeholder="Mobile" class="form-control">';
                html += '</div><div class="col-lg-2">';
           		html += '<input type="text" id="'+timeSlotSeq+'_email" name="'+timeSlotSeq+'_email" required placeholder="Email" class="form-control">';
                html += '</div>'
                html += '<div class="col-lg-1">';
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
        		if(hasSeatSelected){
            		var fullName = $("#"+ timeSlotSeq + "_fullName").val();
            		var email = $("#"+ timeSlotSeq + "_email").val();
            		var mobile = $("#"+ timeSlotSeq + "_mobile").val();
            		if(fullName == null || fullName == ""){
                		alert("Full Name is required for row no " + i);
                		return;
            		}
            		if(email == null || email == ""){
                		alert("Email is required for row no " + i);
                		return
            		}
            		if(mobile == null || mobile == ""){
                		alert("Mobile is required for row no " + i);
                		return;
            		}
        		}
        		i++;
            });  
            if(!flag){
            	alert("No value selected to save booking");
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
        } 
        function requiredFullName(input,timeSlotSeq){
            $id = "";
        	$('input[id$="txtVal1"]').each(function(index) { 
        	    // do something here
        	    $(this).addClass( "myClass" );
        	})   
        }
</script>