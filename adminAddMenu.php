<?php
$imagePath = "images/dummy.jpg";
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
	                        <h5>Create Menu</h5>
	                    </div>
	                </div>
	                <div class="ibox-content">
	                	<form id="menuForm" method="post" action="Actions/BookingAction.php" class="m-t-lg">
                        		<input type="hidden" id ="call" name="call"  value="saveBookingsFromAdmins"/>
                       			<div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Title</label>
                                    <div class="col-lg-4">
                                    	<input type="text"  id="menuTitle" name=""menuTitle"" required placeholder="Title" class="form-control">
                                    </div>
                               </div>
                               <div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Description</label>
                                    <div class="col-lg-4">
                                    	<input type="text" id="description" name="description" required placeholder="Description" class="form-control">
                                    </div>
                               </div>
                               <div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Rate</label>
                                    <div class="col-lg-4">
                                    	<input type="text"  id="rate" name="rate" required placeholder="Rate" class="form-control">
                                    </div>
                               </div>
                               <div class="form-group row">
									<label class="col-sm-2 control-label">Image</label>
									<div class="col-sm-5">
										<input type="file" id="badgeImage" name="badgeImage"
											class="form-control hidden" /> <label for="badgeImage"><a><img
												alt="image" id="badgeImg" class="img" width="92px;"
												src="<?echo $imagePath."?".time() ?>"></a></label> <label
											class="jqx-validator-error-label" id="imageError"></label>
										<button class="btn btn-default btn-xs ladda-button"
											data-style="expand-right" id="choseImage" type="button">
											<span class="ladda-label">Choose Image</span>
										</button>
									</div>
							   </div>
							   <div class="form-group row i-checks">
                       				<label class="col-lg-2 col-form-label">Enable</label>
                                    <div class="col-lg-4">
                                    	<input type="checkbox"  id="isenable" name="isenable" required>
                                    </div>
                               </div>
                               <div class="form-group row">
                               		<div class="col-lg-6">
	                               		<button class="btn btn-primary" onclick="javascript:submitMenuForm()" id="rzp-button" style="float:right">
	                               			Save Menu
		                               	</button>
	                              	</div>
	                           </div>
	                     </form>
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
	    $('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
		   	radioClass: 'iradio_square-green',
		});
    });
    function submitBookingForm(){
    	 $('#menuForm').ajaxSubmit(function( data ){
    		 var obj = $.parseJSON(data);
    		 if(obj.success == 1){
        		 location.href = "adminShowMenus.php";
    		 }else{
        		 alert("Error" + obj.message);
    		 }	 
    	 }
    } 
 </script>	