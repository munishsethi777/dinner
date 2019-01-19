<?include("SessionCheck.php");
require_once('IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Managers/ConfigurationMgr.php");
$configurationMgr = ConfigurationMgr::getInstance();
$cakeVendorEmail = $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_EMAIL);
$cakeVendorMobile = $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_MOBILE);
$cakeVendorMessage = $configurationMgr->getConfiguration(Configuration::$CAKE_VENDOR_MESSAGE);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
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
	                        <h5>Settings</h5>
	                    </div>
	                     <div class="ibox-content">
	                     		<h5>Cake Vendor Settings</h5>
	                        	<form id="settingForm" action="Actions/AdminAction.php" class="m-t-lg">
	                        		<input type="hidden" id ="call" name="call"   value="saveSettings"/>
		                        		<div id="cakeSettingDiv">
			                        		<div class="form-group row">
			                       				<label class="col-lg-1 col-form-label">Email</label>
			                                  	<div class="col-lg-8">
			                                  		<input type="text" required placeholder="Email" value="<?php echo $cakeVendorEmail?>" name="cakeVendorEmail" class="form-control">
			                            		</div>
			                            	</div>
			                            	<div class="form-group row">
			                       				<label class="col-lg-1 col-form-label">Mobile</label>
			                                  	<div class="col-lg-8">
			                                  		 <input type="text" required placeholder="Mobile" value="<?php echo $cakeVendorMobile?>" name="cakeVendorMobile" class="form-control">
			                            		</div>
			                            	</div>		
			                            	<div class="form-group row">
			                       				<label class="col-lg-1 col-form-label">Email Text</label>
			                                  	<div class="col-lg-8">
			                                  		<textarea rows="3" id="cakeVendorMessage" name="cakeVendorMessage" placeholder=" Content" cols="81"><?php echo $cakeVendorMessage?></textarea>
			                            		</div>
			                            	</div>
			                         	</div>
		                            	<div>
		                                     <button class="btn btn-primary ladda-button" data-style="expand-right" id="saveBtn" type="button">
		                                        <span class="ladda-label">Save</span>
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
    	if($("#settingForm")[0].checkValidity()) {
        	saveSettings();
    	}else{
    		$("#changePasswordForm")[0].reportValidity(); 
    	}
    })
});
function saveSettings(){
    $('#settingForm').ajaxSubmit(function( data ){
        showResponseToastr(data,null,null,"mainDiv");
    })
} 
</script>


