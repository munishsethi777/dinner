<?php 
require_once('IConstants.inc');
//require_once($ConstantsArray['dbServerUrl'] ."Utils/MailUtil.php");
//MailUtil::sendSmtpMail("test subject","body","munishsethi777@gmail.com");

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
		
		.datediv1{
			display:none;
		}
		.progressCol{
			width: 16.66%;
		}
		.xdsoft_datetimepicker .xdsoft_label{
			z-index:999 !important;
		}
		.buttonDivMob{
			display:none;
		}
		select{
			font-size:12px !important;
		}
		
		@media all  
			and (max-width: 768px) {
		  	.datediv2, .tableHeaders, .dateCol{
			    display: none;
			 }
		  	.datediv1 {
		    	display: block;
		  	}
		  	.progressCol{
		  		width: 30%;
		  	}
		  	.inmodal .modal-header {
				padding: 20px 15px;
				text-align: center;
			}
			.modal-body {
				padding: 10px 30px 10px 20px;
			}
			.inmodal .modal-title {
				font-size: 20px;
			}
			.buttonDivMob{
				display:block;
			}
			.buttonDiv{
				display:none;
			}
			.timeslotBox{
				padding-bottom:50px;
			}
		}
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">


<!-- conversion tracking code -->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-22899782-19"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-22899782-19');
</script>

<!-- Global site tag (gtag.js) - Google Ads: 1052641146 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-1052641146"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-1052641146');
</script>



<!-- end of conversion tracking code -->


</head>
<body>
 <div id="wrapper">
	<div class="wrapper wrapper-content animated fadeIn">
       <div class="row">
			<div class="col-lg-12">
				<?php //include 'header.php';?>
				<div class="ibox float-e-margins ">
					<div class="ibox-title">
						<h5>
							FLY DINING<small> select your bookings</small>
						</h5>
						<!-- <div class="col-xs-2"><button onclick="rescheduleBooking()" class="btn btn-primary btn-xs">Reschedule Booking</button></div> -->
					</div>
					
						<div style="margin-top:10px">
							
							<div class="col-sm-3 datediv1">
	                       		<input type="text" onchange="javascript:loadData(this.value)" 
                       		name="bookingDate" id="bookingDate" class="form-control bookingDate" style="width:100%"> 	
                    	
	                    	</div>
							
	                    	<div class="col-sm-9 p-xxs" id="dataDiv">
	                       		
	                    	</div>
	
	                    	<div class="col-sm-3 datediv2" >
	                       		<input type="text" onchange="javascript:loadData(this.value)" 
	                       		name="bookingDate" id="bookingDate" class="form-control bookingDate" style="width:100%"> 	
	                    	</div>
							
                   
				</div>
					
			</div>
			
		</div>
		<?php include 'phoneInclude.php';?>
   	</div>
 </div>	

	
						<form role="form" id="bookingForm" method="post" action="bookingsummary.php" class="form-inline">
							<input type="hidden" id ="timeslotseq" name="timeslotseq" />
							<input type="hidden" id ="selectedDate" name="selectedDate" />
							<input type="hidden" id ="menuMembers" name="menuMembers" />
							<input type="hidden" id ="isTestMode" name="isTestMode" value="1"/>
							<input type="hidden" id ="selectedOccassion" name="selectedOccassion"/>
							<input type="hidden" id ="selectedPackage" name="selectedPackage"/>
							
							<div class="modal inmodal" id="myModal4" tabindex="-1" role="dialog"  aria-hidden="true">
							    <div class="modal-dialog">
                                    <div class="modal-content animated fadeIn">
	                                        <div class="modal-header">
	                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	                                            <h4 class="modal-title">How Many Persons?</h4>
	                                        </div>
	                                        <div class="modal-body">
	                                        	<div id="personCounts" class="row i-checks text-center"></div>
	                                        		
										    </div>
	                                        <div id = "footerDiv" class="modal-footer"></div>
	                                    </div>
	                                </div>
	                            </div>
                            </form>
                            
                            <div class="modal inmodal" id="myModal5" tabindex="-1" role="dialog"  aria-hidden="true">
							    <div class="modal-dialog modal-lg">
                                   	<div class="modal-content animated fadeIn">
	                                   
	                                       	<div class="modal-body">
	                                    		<div align="center" id="menuImageDiv"><img src="images/dummy.jpg"></img></div>
	                        	    	</div>
	                                 </div>
	                             </div>
	                       </div>
</body>
</html>
 <script src="scripts/FormValidators/BookingFormValidations.js"></script> 
<script type="text/javascript">
$(document).ready(function(){ 
	currDate = new Date();
	minDate = new Date();
	//if(currDate.getHours() >= 13){
		//currDate.setDate(currDate.getDate() + 1);
		//minDate.setDate(minDate.getDate() + 1);
	//}
	var bookingEndDate = new Date();
	bookingEndDate.setMonth(bookingEndDate.getMonth() + 2);
	$('.bookingDate').datetimepicker({
        timepicker:false,
        inline: true,
        sideBySide: true,
        format:'d-m-Y',
        useCurrent:false,
        defaultDate:currDate,
        minDate:minDate,
        maxDate:bookingEndDate
    });
	currDate = getCurrentDate(currDate);
	loadData(currDate);
});
function getHeaders(){
	var html = '<div class="row ibox-content tableheaders p-xs">'
	html += '<div class="col-xs-1 p-xs">Date</div>';
	html += '<div class="col-xs-2 p-xs">Slot Time</div>';
	html += '<div class="col-xs-3 p-xs">Fare</div>';
	html += '<div class="col-xs-2 p-xs">Occassion</div>'
	html += '<div class="col-xs-2 p-xs">Package</div>'
	html += '<div class="col-lg-2 col-xs-1 p-xs text-center">Action</div>'
	html += '</div>';
	return html;
}
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
		   var html = getHeaders();
		   if(data.length == 0){
				html += "<center style='margin-top:10px;'>No Timeslots available for booking, please select some other date</center>";
		   }
		 $.each( data, function( key, val ) {
	 		html += '<div class="row ibox-content p-xs timeslotBox">';
	 		html += '<div class="col-lg-1 col-sm-1 col-xs-0 dateCol p-xs">'+selectedDate+ '<br><small class="text-muted">'+n+'</small>' +'</div>';
			html += '<div class="col-lg-2 col-sm-2 col-xs-5 timeslotCol p-xs">'+val.timeslot;
			html += '<br/><small class="text-muted">'+ val.description  +'</small></div>';
			var fair = "";
			var menuList = val.menu; 
			var isBookingPast = val.isbookingpast
			var menuArr = [];
			var menuSeqs = [];
			$.each( menuList, function( k, menu ) {
				if(menu.discountedRate == null){
					fair += "Rs. " + menu.rate;
				}else{	
					fair += "<label style='text-decoration: line-through;font-weight:normal'>Rs. " + menu.rate + "</label>";
					fair += " <label class='text-danger' style='font-weight:normal;font-size:15px;'> Rs. "+menu.discountedRate+"</label>";
				}	
				var menuImage = menu.menuimage;
				if(menuImage != null && menuImage != ""){
					var imagePath = "images/menuImages/"+menu.menuseq + "." + menuImage
					fair +='<p><a href="#" onClick="showMenuImage(\'' +  imagePath + '\')"><small class="text-muted"> ('+menu.menutitle+')</small></a></p>';
				}else{
					fair +="<p><small class='text-muted'> ("+menu.menutitle+")</small></p>";
				}	
				menuArr[k] = menu.menutitle;
				menuSeqs[k] = menu.menuseq
	 		});
			html += '<div class="col-lg-3 col-sm-3 col-xs-4 fairCol p-xs">' + fair + '</div>';
			//Mobile View Only starts
			html += '<div class="buttonDivMob col-lg-2 col-sm-2 col-xs-3 p-xs text-center">';
			if(val.seatsAvailable == 0){
				html += '<button class="btn btn-muted btn-xs">Sold out</button>';	
			}else{
				if(isBookingPast){
					html += '<button class="btn btn-muted btn-xs">Booking Closed</button>';
					html += '<h4><small class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Booking Closed For Today </small></h4>'		
				}else{
					html += '<button class="btn btn-danger btn-xs" onclick="bookNow('+val.seq+ ',' + val.seatsAvailable+',\'' +  menuSeqs + '\',\'' +  menuArr + '\',\'' +  selectedDate + '\')">Book Now</button>';
					html += val.msg;	
				}
			}
			html += '</div>';
			//Mobile view only ends
			
			//Occassions
			html += '<div class="col-lg-2 col-sm-2 col-xs-6 p-xs"><select class="form-control occasionSelect" id="occassion'+ val.seq +'">';
			html += '<option value="">Choose Occassion</option>';
			$.each(val.occasions, function(key,occasion){
				html += '<option value="'+occasion[0]+'">'+ occasion[1] +'</option>';
			});
			html += "/<select></div>";
			//packages
			html += '<div class="col-lg-2 col-sm-2 col-xs-6 p-xs"><select class="form-control packageSelect" id="package'+ val.seq +'">';
			html += '<option value="">Choose Package</option>';
			//$.each(val.packages, function(key,package_){
				//html += '<option value="'+package_[0]+'">'+ package_[1] +' Rs.'+ package_[3] +'</option>';
			//});
			html += "/<select></div>";
			
			if(val.seatsAvailable == 0){
				val.availableInPercent = 0;
			}

			html += '<div class="buttonDiv col-lg-2 col-sm-2 col-xs-3 p-xs text-center">';
				if(val.seatsAvailable == 0){
					html += '<button class="btn btn-muted btn-xs">Sold out</button>';	
				}else{
					if(isBookingPast){
						html += '<button class="btn btn-muted btn-xs">Booking Closed</button>';
						html += '<h4><small class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Booking Closed For Today </small></h4>'		
					}else{
						html += '<button class="btn btn-danger btn-xs" onclick="bookNow('+val.seq+ ',' + val.seatsAvailable+',\'' +  menuSeqs + '\',\'' +  menuArr + '\',\'' +  selectedDate + '\')">Book Now</button>';
						html += val.msg;	
					}
					
				}
			html += '</div>';

			
			
		html += '</div>';
		});
	 	$("#dataDiv").html(html);
	 	$(".occasionSelect").change(function() {
		 	id = this.id.substr(9);
		 	$.getJSON("Actions/PackageAction.php?call=getPackagesByOccasionSeq&selectedOccasion="+this.value, function(data){
	 			$('#package'+id).empty();
	 			$.each(data.packages, function(key, value) {   
	 			     $('#package'+id)
	 			         .append($("<option></option>")
	 			                    .attr("value",value[0])
	 			                    .text(value[2]+'- Rs.'+value[4])); 
	 			});
	 		});
		    
		});
	});	 	
}

function setPersonCount(menuSeq,count){
	$("."+menuSeq+"personButton").removeClass("btn-primary");
	if($(".hiddenMenuSeq"+ menuSeq).val() != count){
		buttonClassName = "#personCount"+ menuSeq +"-"+count;
		$(buttonClassName).addClass("btn-primary");//set colored button
		$(".hiddenMenuSeq"+ menuSeq).val(count);//set hidden prop count
		selectButton(menuSeq,count)
	}else{
		$(".hiddenMenuSeq"+ menuSeq).val(0);
	}
}

function selectButton(menuSeq,count){
	for($i=1;$i<=count;$i++){
		buttonClassName = "#personCount"+ menuSeq +"-"+$i;
		$(buttonClassName).addClass("btn-primary")	;
	}
}
function bookNow(timeSlotSeq,seats,menuSeqs,menuTitles,selectedDate){
	$("#timeslotseq").val(timeSlotSeq);
	$("#selectedDate").val(selectedDate);
	var menuSeqArr = menuSeqs.split(",");
	var menuTitleArr = menuTitles.split(",");
	$("#personCounts").html("");
	$("#footerDiv").html("");

	selectedOccassion = $("#occassion"+timeSlotSeq).val();
	selectedPackage = $("#package"+timeSlotSeq).val();
	$("#selectedOccassion").val(selectedOccassion);
	$("#selectedPackage").val(selectedPackage);
	
	
	var html = "";

	$.each( menuSeqArr, function( key, seq ) {
		html += '<input type="hidden" name="hiddenMenuSeq'+seq+'" class="hiddenMenuSeq'+seq+'" value="0"/>';
		html += '<div class="row m-sm">';
			html += '<div class="row p-xs text-muted"><h3>'+menuTitleArr[key]+'</h3></div>';
			html += '<div class="row">';
			for(var i = 1; i <= seats; i++) {
				html += '<div class="col-xs-1" style="margin-bottom:16px;">';
			 	html += '<button class="btn btn-xs btn-muted '+seq+'personButton" id="personCount'+ seq +'-'+i+'" onClick="setPersonCount('+ seq +','+i+')">';
			 	html += i;
				html += '</button></div>';
			}
			html += '</div>';
		html += '</div>';
	});

	$("#personCounts").html(html);
	var str = "";
	$("#menuDiv").html("");
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
    var personCounts = {};
    var menuSeqArr = menuSeqs.split(",");
    var totalPersons = 0;
	$.each( menuSeqArr, function( key, seq ) {
		personMenuCount = $(".hiddenMenuSeq"+seq).val();
		//personMenuCount = $("#"+seq+"_menuCountText").val();
		if(personMenuCount != ""){
			personCounts[seq]= personMenuCount;
			totalPersons += parseInt(personMenuCount);
		}
	}) 
	if(totalPersons == 0){
		alert("Select some seats");
		return;
	}
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
function getCurrentDate(dateObj){
	//var dateObj = new Date();
	var dd = dateObj.getDate();
	var mm = dateObj.getMonth()+1; //January is 0!
	var yyyy = dateObj.getFullYear();
	if(dd<10) {
	    dd = '0'+dd
	} 
	if(mm<10) {
	    mm = '0'+mm
	} 
	today = dd + '-' + mm + '-' + yyyy;
	return today;
}

function rescheduleBooking(){
	location.href = "reschedule.php"
}
function showMenuImage(imagePath){
	var imgHtml = "<img width='100%' height ='auto' src='"+imagePath+"'></img>"
	$("#menuImageDiv").html(imgHtml);
	$('#myModal5').modal('show');
}
</script> 
<script>
  fbq('track', 'ViewContent', {
    content_ids: 'Booking_first_page',
  });
</script>