<?php 
require_once('IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Utils/MailUtil.php");
//MailUtil::sendOrderEmailClient("munishsethi777@gmail.com");

?>
<html>
<head>
<title>Booking</title>
	<?include "ScriptsInclude.php"?>
	<style>
		.xdsoft_datetimepicker{
			width: 100%;
		}
		.xdsoft_datetimepicker .active{
			width:97%;
		}
	</style>
</head>
<body>
 <div id="wrapper">
	<div class="wrapper wrapper-content animated fadeIn">
       <div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins ">
					<div class="ibox-title">
						<h5>
							FLY DINING<small> select your bookings</small>
						</h5>
					</div>
					<div style="margin-top:10px">
                    	<div class="col-lg-9" id="dataDiv">
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
                       		
                       		
                       		
                       		
                    	</div>
                    	<div class="col-lg-3">
                       		<input type="text" onchange="javascript:loadData(this.value)" 
                       		name="bookingDate" id="bookingDate" class="form-control" style="width:100%"> 	
                    	</div>
                    </div>
                    
				</div>
			</div>
		</div>
   	</div>
 </div>	

	<div style="background-color:grey;width:100%">
		<div style="background-color:white;margin:auto;max-width:600px;padding:0px 15px 0px 15px">
		<div style="padding:15px;background-color:#1ab394;color:white;margin:0px -15px 0px -15px;">
		<h1 style="margin-top: 20px;margin-bottom: 10px;">Fly Dining</h1>
		</div>
		
		<div style="font-size:16px;padding:15px;margin:0px -15px 0px -15px;">
		<p>Dear Kishan,</p>
		<p>Thank you for choosing Sky Lounge. We will do our best to make this experience phenomenal for you. We look forward to see you.
		Bon Appétit.</p>
		<div style="text-align:center">
		<img src="https://ci4.googleusercontent.com/proxy/oZJFixdJqatJ4bPRlelACrUAiS7mmSp4OJja5qmREUBJVu47cIun1ciQ0hg1No-a2urGigmBjTwz7vi08Cs9arEdNLy3VuY916U=s0-d-e1-ft#http://www.skylounge.in/media/hero-image-receipt.png"
				width="125" height="120" style=";border:0px">
				<h1>Thank You For Your Order!</h1>
				<h2>Order ID :125C4E</h2>
				<h3>Venue</h3>
		
				<p>Oct. 2, 2018 (01:00PM - 02:00PM)</p>
				<p>Sky Lounge<br>
				Kempapura Main Road, Nr. Nagavara Lake,<br>
				Nagavara, Hebbal, Bengaluru,<br>
				Karnataka - 560024, India.</p>
				</div>
				</div>
		
				<div style="margin:30px;padding:10px;background-color:#f3f3f4">
				<h3>Order Confirmation</h3>
				</div>
				<div style="margin:30px;padding:10px">
					
					<div style="padding:0px 30px;margin:0px -15px 10px -15px">
						<div style="width:75%;float:left;position:relative;text-align:left">Vegetarian Menu</div>
						<div style="width:25%;float:left;position:relative;text-align:left">2</div>
					</div>
					
					<div style="padding:20px 30px;margin:0px -15px 0px -15px">
						<div style="width:75%;float:left;position:relative;text-align:left">Non Vegetarian Menu</div>
						<div style="width:25%;float:left;position:relative;text-align:left">1</div>
					</div>
					
					<div style="padding:20px 30px 40px 30px;margin:10px -15px 0px -15px;background-color:#f3f3f4;font-weight:bold;font-size:14px;">
						<div style="width:75%;float:left;text-align:left">Total</div>
						<div style="width:25%;float:left;text-align:left">3</div>
					</div>
				
					
				</div>
				<div style="padding:10px;margin:30px;text-align:center;">
					<h3>Munish Sethi</h3>
					<h3>munishsethi777@gmail.com</h3>
					<h3>9814600356</h3>
				</div>
				<div style="padding:15px;margin:30px;text-align:center;background-color:#1ab394;color:white;margin:0px -15px 0px -15px;">
				<h1>Fly Dining</h1>
				<br>
				<h2>+91 99889 99919</h2>
				<h2>info@flydining.com</h2>
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
	                                        	<div id="personCounts" class="row i-checks"></div>
												<div class="hr-line-dashed"></div>
												<div id="menuDiv"></div>	
										    </div>
	                                        <div id = "footerDiv" class="modal-footer"></div>
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
        format:'d-m-Y',
        //minDate:new Date()
    });
	currDate = getCurrentDate();
	loadData(currDate);
});
function loadData(selectedDate){
	var from = selectedDate.split("-")
	var d = new Date(from[2], from[1] - 1, from[0])
	
	var weekday=new Array(7);
	weekday[0]="Sunday";
	weekday[1]="Monday";
	weekday[2]="Tuesday";
	weekday[3]="Wednesday";
	weekday[4]="Thursday";
	weekday[5]="Friday";
	weekday[6]="Saturday";
	var n = weekday[d.getDay()];
	
	$.getJSON("Actions/TimeSlotAction.php?call=getTimeSlots&selectedDate="+selectedDate, function(data){
		  //var data = $.parseJSON(jsonString)
			var html = getHeaders();
		 $.each( data, function( key, val ) {
	 		html += '<div class="row ibox-content">';
			html += '<div class="col-lg-2">'+selectedDate+ '<br><small class="text-muted">'+n+'</small>' +'</div>';
			html += '<div class="col-lg-3">'+val.timeslot+ '</div>';
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
			html += '<div class="col-lg-2"><div class="progress progress-mini">';
			html += '<div style="width: '+val.availableInPercent+'%" class="progress-bar"></div></div>';
			html += '<small class="text-muted">'+ val.seatsAvailable  +' Seats</small></div>';
			if(val.seatsAvailable == 0){
				html += '<div class="col-lg-2"><button class="btn btn-danger btn-xs">Sold out</button></div>';	
			}else{
				html += '<div class="col-lg-2"><button class="btn btn-primary btn-xs" onclick="bookNow('+val.seq+ ',' + val.seatsAvailable+',\'' +  menuSeqs + '\',\'' +  menuArr + '\',\'' +  selectedDate + '\')">Book Now</button></div>';
			}
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
		    html += '<div class="col-sm-1">';
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
		str += '<input value="'+seq+'" type="radio" onchange="setValue()" name="menu" id="menuTitleRadio"><small> All '+ menuTitle+'</small>';
		str += '</label></div>';
		//str += '<div class="col-sm-2"><input type="text" class="menuCount" placeholder="'+menuTitle+' Person Count" id="'+seq+'_menuCountText" name="menuCountText"></div>';			
	});

	$.each( menuSeqArr, function( key, seq ) {
		var menuTitle = menuTitleArr[key];
		str += '<div class="col-sm-3">';
		str += '<input style="width:100%;font-size:10px" type="text" class="menuCount text-muted" placeholder="'+menuTitle+' Count" id="'+seq+'_menuCountText" name="menuCountText">';			
		str += '</div>';
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
        alert("Total Seats available for this slot are "+seats+" only.");
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
	today = dd + '-' + mm + '-' + yyyy;
	return today;
}
function getHeaders(){
	var html = '<div class="row ibox-content">'
		html += '<div class="col-lg-2">Date</div>';
		html += '<div class="col-lg-3">Slot Time</div>';
		html += '<div class="col-lg-3">Fare</div>';
		html += '<div class="col-lg-2">Seats Available</div>'
		html += '<div class="col-lg-2">Action</div>'
		html += '</div>';
		return html;
}

</script> 