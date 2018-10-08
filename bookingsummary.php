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
                    		<div class="ibox-content">
	                       		<div class="row text-center" style="margin-bottom:10px">
	                       			<h1 style="padding-bottom:10px">
											MENU
									</h1>
									<button class="btn btn-primary btn-rounded">VEG</button>
									<button class="btn btn-primary btn-rounded">NON VEG</button>
	                       		</div>
		                       		<div class="row">
		                       			<div class="file-box">
			                                <div class="file">
		                                        <div class="icon">
		                                            <i class="fa fa-file"></i>
		                                        </div>
		                                        <div class="file-name">
		                                            <p class="text-success">ITEM NAME</p>
		                                            <small>Lorem ipsum dolor sit amet, 
		                                            consectetur adipiscing elit, sed do eiusmod tempor 
		                                            incididunt ut labore et 
		                                            dolore magna aliqua.</small>
		                                        </div>
			                                </div>
		                            	</div>
		                            	
		                            	<div class="file-box">
			                                <div class="file">
		                                        <div class="icon">
		                                            <i class="fa fa-file"></i>
		                                        </div>
		                                        <div class="file-name">
		                                            <p class="text-success">ITEM NAME</p>
		                                            <small>Lorem ipsum dolor sit amet, 
		                                            consectetur adipiscing elit, sed do eiusmod tempor 
		                                            incididunt ut labore et 
		                                            dolore magna aliqua.</small>
		                                        </div>
			                                </div>
		                            	</div>
		                            	
		                            	<div class="file-box">
			                                <div class="file">
		                                        <div class="icon">
		                                            <i class="fa fa-file"></i>
		                                        </div>
		                                        <div class="file-name">
		                                            <p class="text-success">ITEM NAME</p>
		                                            <small>Lorem ipsum dolor sit amet, 
		                                            consectetur adipiscing elit, sed do eiusmod tempor 
		                                            incididunt ut labore et 
		                                            dolore magna aliqua.</small>
		                                        </div>
			                                </div>
		                            	</div>
		                            	
		                            	<div class="file-box">
			                                <div class="file">
		                                        <div class="icon">
		                                            <i class="fa fa-file"></i>
		                                        </div>
		                                        <div class="file-name">
		                                            <p class="text-success">ITEM NAME</p>
		                                            <small>Lorem ipsum dolor sit amet, 
		                                            consectetur adipiscing elit, sed do eiusmod tempor 
		                                            incididunt ut labore et 
		                                            dolore magna aliqua.</small>
		                                        </div>
			                                </div>
		                            	</div>
		                            	
		                            	<div class="file-box">
			                                <div class="file">
		                                        <div class="icon">
		                                            <i class="fa fa-file"></i>
		                                        </div>
		                                        <div class="file-name">
		                                            <p class="text-success">ITEM NAME</p>
		                                            <small>Lorem ipsum dolor sit amet, 
		                                            consectetur adipiscing elit, sed do eiusmod tempor 
		                                            incididunt ut labore et 
		                                            dolore magna aliqua.</small>
		                                        </div>
			                                </div>
		                            	</div>
		                            	
		                            	<div class="file-box">
		                                <div class="file">
	                                        <div class="icon">
	                                            <i class="fa fa-file"></i>
	                                        </div>
	                                        <div class="file-name">
	                                            <p class="text-success">ITEM NAME</p>	
	                                            <small>Lorem ipsum dolor sit amet, 
	                                            consectetur adipiscing elit, sed do eiusmod tempor 
	                                            incididunt ut labore et 
	                                            dolore magna aliqua.</small>
	                                        </div>
		                                </div>
	                            	</div>
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
	                       			
	                       			
	                       			<form class="m-t-lg">
		                       			<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Name</label>
		                                    <div class="col-lg-10">
		                                    	<input type="text" id="fullName" placeholder="FullName" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Email</label>
		                                    <div class="col-lg-10">
		                                    	<input type="email" id="email" placeholder="Email" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                       				<label class="col-lg-2 col-form-label">Mobile</label>
		                                    <div class="col-lg-10">
		                                    	<input type="mobile" id="mobile" placeholder="mobile" class="form-control">
		                                    </div>
	                                	</div>
	                                	<div class="form-group row">
		                                		<button class="btn btn-primary col-lg-12" id="rzp-button">
			                                		Make Payment of Rs 10,750.00
			                                	</button>
		                                	
	                                	</div>
	                       			</form>
	                       			
	                       			
	                       			
	                       			<div class="row m-t-sm text-center">	
	                       				<small class="text-muted">
		                       				You can cancel the tickets upto 4 hours before the show. <br>
		                       				Refunds will be done according to <a href="#">Cancellation Policy</a>
	                       				</small>
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



document.getElementById('rzp-button').onclick = function(e){
	var fullName = $("#fullName").val();
	var email = $("#email").val();
	var mobile = $("#mobile").val();
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
		        "name": fullName,
		        "email": email
		    },
		    "notes": {
		        "address": "Hello World"
		    },
		    "theme": {
		        "color": "#1ab394"
		    }
		};
	var rzp1 = new Razorpay(options);
    rzp1.open();
    e.preventDefault();
}
</script> 