<?php 
require_once('IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/BookingDetailMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/TimeSlotMgr.php");
$bookingId = 0;
session_start();
if(isset($_SESSION["bookingid"])){
	$bookingId = $_SESSION["bookingid"];
}
$booking = new Booking();
$bookingDetails = array();
$timeSlot = new TimeSlot();
$bookingDate = "";
if(!empty($bookingId)){
	$bookingMgr = BookingMgr::getInstance();
	$bookingDetailMgr = BookingDetailMgr::getInstance();
	$booking = $bookingMgr->findBySeq($bookingId);
	$bookingDate = $booking->getBookingDate();
	$bookingDate = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s", $bookingDate);
	$bookingDate = $bookingDate->format("jS F Y");
	$bookingDetails = $bookingDetailMgr->getBookingDetailAndMenu($bookingId);
	$timeSlotMgr = TimeSlotMgr::getInstance();
	$timeSlot = $timeSlotMgr->findBySeq($booking->getTimeSlot());
}
?>
<html>
<head>
<title>Thankyou for your Booking</title>
	<?include "ScriptsInclude.php"?>
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

<!-- Event snippet for Flydining_Conversion_tracking conversion page -->
<script>
  gtag('event', 'conversion', {
      'send_to': 'AW-1052641146/e6HlCLecxJIBEPqO-PUD',
      'transaction_id': ''
  });
</script>

<!-- end of conversion tracking code -->

<!--  Seller ratings on google code -->
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>

<script>
  window.renderOptIn = function() {
    window.gapi.load('surveyoptin', function() {
      window.gapi.surveyoptin.render(
        {
          // REQUIRED FIELDS
          "merchant_id": 130266907,
          "order_id": "<?php echo $booking->getBookingId()?>",
          "email": "<?php echo $booking->getEmailId()?>",
          "delivery_country": "IN",
          "estimated_delivery_date": "<?php echo date("Y-m-d")?>",

          // OPTIONAL FIELDS
          //"products": [{"gtin":"GTIN1"}, {"gtin":"GTIN2"}]
        });
    });
  }
</script>

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
							FLY DINING<small> thanks for your booking</small>
						</h5>
					</div>
					<div class="ibox-content text-center">
						<div class="row">
							<div class="col-lg-8 col-lg-offset-2">
							<?php if(empty($bookingId)){?>
									<h3 class="lbl text-danger">Oops.. Something went wrong. Pls contact us at nivedika@flydining.com or call us at 
									+91-76981-81000,+91-81305-40906 regarding your attempt to booking.</h3>
							<?php }else {?>
									<p style="font-size:14px">
										Thank you for choosing FlyDining. We will do our best to make this experience phenomenal for you.<br> 
										We look forward to see you.</p>
										<img src="http://www.flydining.com/booking/images/icontick.png" 
										width="125" height="120" style="border:0px">
									<p class="m-t-lg">	
										Your Booking id is  <h1 class="text-info"><?php echo $booking->getBookingId()?></h1>
									</p>
									<p class="m-t-lg">Booking Details</p>
									<div class="row">
										<div class="col-lg-6 col-xs-6 text-right">Payment id :</div>
										<div class="col-lg-6 col-xs-6 text-left"><?php echo $booking->getTransactionId()?></div>
									</div>
									<div class="row">
										<div class="col-lg-6 col-xs-6 text-right">Booking Date :</div>
										<div class="col-lg-6 col-xs-6 text-left"><?php echo $bookingDate?></div>
									</div>
									<div class="row">
										<div class="col-lg-6 col-xs-6 text-right">Booking TimeSlot :</div>
										<div class="col-lg-6 col-xs-6 text-left"><?php echo $timeSlot->getTitle()?></div>
									</div>
									
									<div class="row">
										<div class="col-lg-6 col-xs-6 text-right">Booking Details :</div>
											<div class="col-lg-6 col-xs-6 text-left">
											<?php foreach ($bookingDetails as $bookingDetail){
												echo $bookingDetail["members"]?> seats x <?php echo $bookingDetail["title"]?><br>
											<?php }?>
											</div>
									</div>
								<?php }?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php include 'phoneInclude.php';?>
		</div>
   	</div>
 </div>	
</body>
</html>
