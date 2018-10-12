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
	                    	
	                        <form id="userInfoForm" method="post" action="Actions/BookingAction.php" class="m-t-lg">
	                       			<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Select Date</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" id="fullName" name="fullName" required placeholder="fullname" class="form-control">
	                                    </div>
	                                    
	                                    
                                	</div>
                                	
                                	<div class="form-group row">
                                		<div class="col-lg-2">
		                       				<label class="col-form-label">Time Slot</label>
		                                    	<input type="text" id="fullName" name="fullName" 
		                                    value="1PM to 2PM" class="form-control" disabled>
	                                    </div>
	                                    <div class="col-lg-2">
		                       				<label class="col-form-label">Menu</label>
		                       				<select id="fullName" name="fullName" required 
		                                    	placeholder="fullname" class="form-control">
		                                    		<option>Veg</option>
		                                    		<option>Non Veg</option>
		                                    	</select>
		                                    	
	                                    </div>
	                                    <div class="col-lg-1">
		                       				<label class="col-form-label">Seats</label>
		                                    	<select id="fullName" name="fullName" required 
		                                    	placeholder="fullname" class="form-control">
		                                    		<option>0</option>
		                                    	</select>
	                                    </div>
	                                    <div class="col-lg-2">
		                       				<label class="col-form-label">FullName</label>
		                                    <input type="text" id="fullName" name="fullName" required 
		                                    placeholder="fullname" class="form-control">
	                                    </div>
	                                    <div class="col-lg-2">
		                       				<label class="col-form-label">Mobile</label>
		                                    <input type="text" id="fullName" name="fullName" required 
		                                    placeholder="fullname" class="form-control">
	                                    </div>
	                                    <div class="col-lg-2">
		                       				<label class="col-form-label">Email</label>
		                                    <input type="text" id="fullName" name="fullName" required 
		                                    placeholder="fullname" class="form-control">
	                                    </div>
                                	</div>
                                	<hr>
                                	                                	
                                	
                                	<div class="form-group row">
                                		<div class="col-lg-12">
	                                		<button class="btn btn-primary" id="rzp-button" style="width:100%">
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
           //loadGrid()
           $('.i-checks').iCheck({
	        	checkboxClass: 'icheckbox_square-green',
	        	radioClass: 'iradio_square-green',
	    	});
           
        });
        
        
</script>