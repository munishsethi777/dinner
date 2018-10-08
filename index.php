<?php?>
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
	                       			<button class="btn btn-primary" >BOOK NOW</button>
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
						<form role="form" id="bookingForm" method="post" action="bookingsummary.php" class="form-inline">
							<input type="hidden" id ="timeslotseq" name="timeslotseq" />
							<input type="hidden" id ="selectedDate" name="selectedDate" />
							<input type="hidden" id ="menuMembers" name="menuMembers" />
							<div class="modal inmodal" id="myModal4" tabindex="-1" role="dialog"  aria-hidden="true">
							    <div class="modal-dialog">
                                    <div class="modal-content animated fadeIn">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title">How Many Persons?</h4>
                                        </div>
                                        <div class="modal-body">
                                        	
                           					<div id="personCounts" class="row i-checks">
											</div>
											<div class="hr-line-dashed"></div>
												<div id="menuDiv">
												</div>	
									    </div>
                                        <div id = "footerDiv" class="modal-footer">
                                           
	                                     </div>
	                                    </div>
	                                </div>
	                            </div>
                            </form>
</body>
</html>
 <script src="scripts/FormValidators/BookingFormValidations.js"></script> 
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
// 	$("#saveBtn").click(function(e){
// 	    var btn = this;
// 	    var validationResult = function (isValid) {
// 	        if (isValid) {
// 	        	submitBookingForm(e,btn);
// 	        }
// 	    }
// 	    $('#bookingForm').jqxValidator('validate', validationResult);   
// 	})
});
function loadData(selectedDate){
	$.get("Actions/TimeSlotAction.php?call=getTimeSlots&selectedDate="+selectedDate, function(jsonString){
		 var data = $.parseJSON(jsonString)
		 var html = getHeaders();
	 	 $.each( data, function( key, val ) {
		 	html += '<div class="row ibox-content">';
			html += '<div class="col-lg-2">'+selectedDate+'</div>';
			html += '<div class="col-lg-2">'+val.timeslot+'</div>';
			var fair = "";
			var menuList = val.menu; 
			var menuArr = [];
			var menuSeqs = [];
			$.each( menuList, function( k, menu ) {
				fair += "Rs. " + menu.rate +"("+menu.menutitle+")<br>";	
				menuArr[k] = menu.menutitle;
				menuSeqs[k] = menu.menuseq
	 		});
			html += '<div class="col-lg-3">' + fair + '</div>';
			html += '<div class="col-lg-3"><div class="progress progress-mini">';
			html += '<div style="width: '+val.availableInPercent+'%" class="progress-bar"></div></div>';
			html += '<small class="text-muted">'+ val.seatsAvailable  +' Seats</small></div>';
			html += '<div class="col-lg-2"><button class="btn btn-primary btn-xs" onclick="bookNow('+val.seq+ ',' + val.seatsAvailable+',\'' +  menuSeqs + '\',\'' +  menuArr + '\',\'' +  selectedDate + '\')">Book Now</button></div>';
			html += '</div>';
		});
	 	$("#dataDiv").html(html);
	});	 	
}

function bookNow(timeSlotSeq,seats,menuSeqs,menuTitles,selectedDate){
	$("#timeslotseq").val(timeSlotSeq);
	$("#selectedDate").val(selectedDate);
	var menuSeqArr = menuSeqs.split(",");
	var menuTitleArr = menuTitles.split(",");
	$("#personCounts").html("");
	$("#footerDiv").html("");
	var html = "";
	for(var i = 1; i <= seats; i++) {
		    html += '<div class="col-sm-1 ">';
		 	html += '<label class="checkbox-inline">';
			html += '<input value="'+i+'" type="radio" onchange="setValue()" name="personCount" id="personCount">'+i;
			html += '</label></div>';
	}
	$("#personCounts").html(html);
	var str = "";
	$("#menuDiv").html("");
	$.each( menuSeqArr, function( key, seq ) {
		var menuTitle = menuTitleArr[key];
		str += '<div class="col-sm-3">';
		str += '<label class="checkbox-inline">';
		str += '<input value="'+seq+'" type="radio" onchange="setValue()" checked="checked" name="menu" id="menuTitleRadio"> All '+ menuTitle;
		str += '</label></div>';
		str += '<input type="text" class="menuCount" placeholder="'+menuTitle+' Person Count" id="'+seq+'_menuCountText" name="menuCountText">';			
	});
	var footerButtons = '<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>';
    	footerButtons += '<button type="button" id="saveBtn" onClick="javascript:submitBookingForm('+seats+ ',\'' +  menuSeqs + '\')" class="btn btn-primary">Continue</button>';
    $("#footerDiv").html(footerButtons);
	$("#menuDiv").html(str);
	$('#myModal4').modal('show');
}

function setValue(){
	$("input.menuCount:text").val("");
	var personCount = $('#personCount:checked').val();
	var selectedMenuTitleSeq = $('#menuTitleRadio:checked').val();
	$("#"+selectedMenuTitleSeq+"_menuCountText").val(personCount);
}

function submitBookingForm(seats,menuSeqs){
	var text = "";
	$('#menuDiv input[type=text]').each(function (){
		var val = $(this).val();
		if(val != null && val != "" ){
			if(!$.isNumeric(val)){
				text = "";
				return false;
			}
		}
		text += $(this).val();
    });
    if(text == ""){
        alert("Invalid person count");
        return;
    }	
    var personCounts = {};
    var menuSeqArr = menuSeqs.split(",");
    var totalPersons = 0;
	$.each( menuSeqArr, function( key, seq ) {
		personMenuCount = $("#"+seq+"_menuCountText").val();
		personCounts[seq]= personMenuCount;
		totalPersons += parseInt(personMenuCount);
	}) 
	if(totalPersons > seats){
        alert("Totals persons applied are not available.");
        return;
    }
   var jsonS = JSON.stringify(personCounts);
   $('#menuMembers').val(jsonS)
   $("#bookingForm").submit();
}
function IsNumeric(val) {
    return Number(parseFloat(val)) === val;
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