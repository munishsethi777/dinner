<?php ?>

<html>
<head>
<title>Booking</title>
<?include "ScriptsInclude.php"?>
</head>
<body>
 <div id="wrapper">
	<div class="wrapper wrapper-content animated fadeIn">
       <div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins ">
					<div class="ibox-title">
						<h5>
							SKY DINING<small> select your bookings</small>
						</h5>
					</div>
					<div style="margin-top:10px">
                    	<div class="col-lg-8" id="dataDiv">
                       		<div class="row ibox-content" style="font-weight: bold">
	                       		<div class="col-lg-2">
	                       			Date
	                       		</div>
	                       		<div class="col-lg-2">
	                       			Slot Time
	                       		</div>
	                       		<div class="col-lg-3">
	                       			Fare
	                       		</div>
	                       		<div class="col-lg-3">
	                       			Seats Available
	                       		</div>
	                       		<div class="col-lg-2">
	                       			Action
	                       		</div>
	                       	</div>
                       		
                       		
                       		<div class="row ibox-content">
	                       		<div class="col-lg-2">
	                       			17-09-2018<br>
	                       			<small class="text-muted">Monday</small>
	                       		</div>
	                       		<div class="col-lg-2">
	                       			7:30 PM<br>
	                       			<small class="text-muted">Session1</small>
	                       		</div>
	                       		<div class="col-lg-3">
	                       			Rs. 2000 (Veg) <br>
	                       			Rs. 2800 (N.Veg)
	                       		</div>
	                       		<div class="col-lg-3">
	                       			<div class="progress progress-mini">
                                    	<div style="width: 100%;" class="progress-bar"></div>
                                    </div>
	                       			<small class="text-muted">8 Seats</small>
	                       		</div>
	                       		<div class="col-lg-2">
	                       			<button class="btn btn-primary">BOOK NOW</button>
	                       		</div>
                       		</div>
                       		<div class="row ibox-content">
	                       		<div class="col-lg-2">
	                       			17-09-2018<br>
	                       			<small class="text-muted">Monday</small>
	                       		</div>
	                       		<div class="col-lg-2">
	                       			7:30 PM<br>
	                       			<small class="text-muted">Session1</small>
	                       		</div>
	                       		<div class="col-lg-3">
	                       			Rs. 2000 (Veg) <br>
	                       			Rs. 2800 (N.Veg)
	                       		</div>
	                       		<div class="col-lg-3">
	                       			<div class="progress progress-mini">
                                    	<div style="width: 50%;" class="progress-bar"></div>
                                    </div>
	                       			<small class="text-muted">4 Seats</small>
	                       		</div>
	                       		<div class="col-lg-2">
	                       			<button class="btn btn-primary">BOOK NOW</button>
	                       		</div>
                       		</div>
                       		
                    	</div>
                    	<div class="col-lg-4">
                       		<input type="text" onchange="javascript:loadData(this.value)" name="bookingDate" id="bookingDate" class="form-control"> 	
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
<script type="text/javascript">
$(document).ready(function(){ 
	$('#bookingDate').datetimepicker({
        timepicker:false,
        inline: true,
        sideBySide: true,
        format:'m-d-Y'
    });
    currDate = getCurrentDate();
	loadData(currDate);
});
function loadData(selectedDate){
	$.getJSON("Actions/TimeSlotAction.php?call=getTimeSlots&selectedDate="+selectedDate, function(data){
		 var html = getHeaders();
	 	 $.each( data, function( key, val ) {
		 	html += '<div class="row ibox-content">';
			html += '<div class="col-lg-2">'+selectedDate+'</div>';
			html += '<div class="col-lg-2">'+val.timeslot+'</div>';
			var fair = "";
			var menuList = val.menu; 
			$.each( menuList, function( k, menu ) {
				fair += "Rs. " + menu.rate +"("+menu.menutitle+")<br>";	
	 		});
			html += '<div class="col-lg-3">' + fair + '</div>';
			html += '<div class="col-lg-3"><div class="progress progress-mini">';
			html += '<div style="width: '+val.availableInPercent+'%" class="progress-bar"></div></div>';
			html += '<small class="text-muted">'+ val.seatsAvailable  +' Seats</small></div>';
			html += '<div class="col-lg-2"><button class="btn btn-primary">BOOK NOW</button></div>';
			html += '</div>';
		});
	 	$("#dataDiv").html(html);
	});	 	
}

function getCurrentDate(){
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();

	if(dd<10) {
	    dd = '0'+dd
	} 

	if(mm<10) {
	    mm = '0'+mm
	} 

	today = mm + '-' + dd + '-' + yyyy;
	return today;
}
function getHeaders(){
	var html = '<div class="row ibox-content">'
		html += '<div class="col-lg-2">Date</div>';
		html += '<div class="col-lg-2">Slot Time</div>';
		html += '<div class="col-lg-3">Fare</div>';
		html += '<div class="col-lg-3">Seats Available</div>'
		html += '<div class="col-lg-2">Action</div>'
		html += '</div>';
		return html;
}

</script> 