<?php ?>

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
</head>
<body>
 <div id="wrapper">
	<div class="wrapper wrapper-content animated fadeIn">
       <div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins ">
					<div style="margin-top:10px">
                    	<div class="col-lg-8">
                    		<div class="ibox border-bottom">
			                    <div class="ibox-title bg-success">
			                        <a class="collapse-link" style="float:left;margin-right:6px;color:white">
		                                <i class="fa fa-chevron-up"></i>
		                            </a>
			                        <h5>Share Your Contact Details</h5> 
			                    </div>
			                    <div class="ibox-content">
									<form role="form" class="form-inline">
		                                <div class="form-group">
		                                    <input type="text" placeholder="Enter Full Name" id="fullName" class="form-control">
		                                </div>
		                                <div class="form-group">
		                                    <input type="text" placeholder="Enter Mobile no" id="mobile" class="form-control">
		                                </div>
		                                <div class="form-group">
		                                    <input type="email" placeholder="Enter Email id" id="email" class="form-control">
		                                </div>
		                                <div class="form-group">
		                                    <button id="rzp-button" class="btn btn-primary" type="submit" style="margin-top:5px">
		                                    Make Payment
		                                    </button>
		                                </div>
		                            </form>			                        
                				</div>
                    		</div>
                    		
                    		
                    		
                    	</div>
                    	
                    	
                    	
                    	
                    	
                    	<div class="col-lg-4">
                    		<div class="ibox-content">
	                       			<h3>BOOKING SUMMARY</h3>
	                       			<div class="row" style="margin-bottom:5px">
	                       				<div class="col-md-8">SLOT 7:30PM - 9:30PM</div>
	                       				<div class="col-md-4 text-right">Rs 10,500.00</div>
	                       			</div>
	                       			<div class="row">	
	                       				<div class="col-lg-8">
	                       					<small class="text-muted">
	                       						2 VEG - 2 X 4500 <br>
	                       						2 N.VEG - 2 X 4500 M<br>
	                       					</small>
	                       				</div>
	                       				<div class="col-lg-4 text-right"></div>
	                       			</div>
	                       			
	                       			<div class="row m-b-sm">	
	                       				<div class="col-lg-8">
	                       					<small class="text-muted">
	                       						Internet Handling Fees
	                       					</small>
	                       				</div>
	                       				<div class="col-lg-4 text-right">Rs 750.00</div>
	                       			</div>
	                       			<div class="row bg-muted p-h-sm">	
	                       				<div class="col-lg-8">
	                       					Sub Total
	                       				</div>
	                       				<div class="col-lg-4 text-right">Rs 10,750.00</div>
	                       			</div>
	                       			
	                       			<div class="row bg-success p-h-sm text-uppercase font-bold">	
	                       				<div class="col-lg-8">
	                       					AMOUNT PAYABLE
	                       				</div>
	                       				<div class="col-lg-4 text-right">Rs 10,750.00</div>
	                       			</div>
	                       			
	                       	</div>
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
<script>
var options = {
    "key": "rzp_live_KpbxYUeCTzMhDO",
    "amount": "100", // 2000 paise = INR 20
    "name": "Flydining",
    "description": "Purchase Description",
    "image": "/your_logo.png",
    "handler": function (response){
        alert(response.razorpay_payment_id);
    },
    "prefill": {
        "name": "Munish Sethi",
        "email": "munishsethi777@gmail.com"
    },
    "notes": {
        "address": "Hello World"
    },
    "theme": {
        "color": "#1ab394"
    }
};
var rzp1 = new Razorpay(options);

document.getElementById('rzp-button').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script> 