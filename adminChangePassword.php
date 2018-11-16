<?include("SessionCheck.php");?>
<html>
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
	                <div class="ibox mainDiv">
	                    <div class="ibox-title">
	                    	 <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
								<a class="navbar-minimalize minimalize-styl-2 btn btn-primary "
									href="#"><i class="fa fa-bars"></i> </a>
							</nav>
	                        <h5>Change Password</h5>
	                    </div>
	                     <div class="ibox-content">
	                        <form id="changePasswordForm" action="Actions/AdminAction.php" class="m-t-lg">
	                        	<input type="hidden" id ="call" name="call"   value="changePassword"/>
	                        		<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Current Password</label>
	                                  	<div class="col-lg-4">
	                                  		<input type="password" required placeholder="Current Password" id="earlierPassword" name="earlierPassword" class="form-control">
	                            		</div>
	                            	</div>	
	                            	<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">New Password</label>
	                                  	<div class="col-lg-4">
	                                  		 <input type="password" required id="newPassword" placeholder="New Password" name="newPassword" class="form-control">
	                            		</div>
	                            	</div>
	                            	<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Confirm Password</label>
	                                  	<div class="col-lg-4">
	                                  		<input type="password" required id="confirmPassword" placeholder="Confirm Password" name="confirmPassword" class="form-control">
	                            		</div>
	                            	</div>
	                            	<div>
	                                     <button class="btn btn-primary ladda-button" data-style="expand-right" id="saveBtn" type="button">
	                                        <span class="ladda-label">Change Password</span>
	                                    </button>
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
$(document).ready(function(){ 
    $("#saveBtn").click(function(e){
    	if($("#changePasswordForm")[0].checkValidity()) {
        	var confirmPassword = $("#confirmPassword").val();
        	var newPassword = $("#newPassword").val();
        	if(confirmPassword != newPassword){
        		alert("Confirm Password should match with New Password");
            	return;
        	}
    		changePassword();
    	}else{
    		$("#changePasswordForm")[0].reportValidity(); 
    	}
    })
});
function changePassword(){
    $('#changePasswordForm').ajaxSubmit(function( data ){
        showResponseToastr(data,null,"changePasswordForm","mainDiv");
    })
} 
</script>

