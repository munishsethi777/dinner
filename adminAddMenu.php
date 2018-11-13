<?php
//include("SessionCheck.php");
require_once('IConstants.inc');
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/MenuMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/MenuPricingMgr.php");
$menu = new Menu();
$pricingDates = "";
$price = "";
$priceDes = "";
if(isset($_POST["seq"])){
	$seq = $_POST["seq"];
	$menuMgr = MenuMgr::getInstance();
	$menu = $menuMgr->findBySeq($seq);
	$menuPricingMgr = MenuPricingMgr::getInstance();
	$menuPricing = $menuPricingMgr->findMenuPricingArrBySlotSeq($seq);
	if(!empty($menuPricing)){
		$pricingDates = $menuPricing["dates"];
		$price = $menuPricing["price"];
		$priceDes = $menuPricing["description"];
	}
}
$imagePath = "images/dummy.jpg";
$isEnabledChecked = "checked";
if(!empty($menu->getImageName())){
	$imagePath = "images/menuImages/".$menu->getSeq() . ".". $menu->getImageName();
}
if(empty($menu->getIsEnabled())){
	$isEnabledChecked = "";
}

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
	                	<form id="menuForm" method="post" enctype="multipart/form-data" action="Actions/MenuAction.php" class="m-t-lg">
                        		<input type="hidden" id ="call" name="call"  value="saveMenu"/>
                        		<input type="hidden" id ="seq" name="seq"  value="<?php echo $menu->getSeq()?>"/>
                        		<input type="hidden" id ="call" name="imageName"  value="<?php echo $menu->getImageName()?>"/>
                       			<div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Title</label>
                                    <div class="col-lg-4">
                                    	<input type="text" value="<?php echo $menu->getTitle()?>"  id="title" name="title" required placeholder="Title" class="form-control">
                                    </div>
                               </div>
                               <div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Description</label>
                                    <div class="col-lg-4">
                                    	<input type="text" value="<?php echo $menu->getDescription()?>" id="description" name="description" required placeholder="Description" class="form-control">
                                    </div>
                               </div>
                               <div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Rate</label>
                                    <div class="col-lg-4">
                                    	<input type="text" value="<?php echo $menu->getRate()?>"  id="rate" name="rate" required placeholder="Rate" class="form-control">
                                    </div>
                               </div>
                               <div class="form-group row">
									<label class="col-sm-2 control-label">Image</label>
									<div class="col-sm-5">
										<input type="file" id="menuImage" name="menuImage"
											class="form-control hidden" /> <label for="menuImage"><a><img
												alt="image" id="badgeImg" class="img" width="92px;"
												src="<?echo $imagePath. "?".time() ?>"></a></label> <label
											class="jqx-validator-error-label" id="imageError"></label>
									</div>
							   </div>
						        <div class="form-group row i-checks">
                       				<label class="col-lg-2 col-form-label">Enable</label>
                                    <div class="col-lg-4">
                                    	<input type="checkbox" <?php echo $isEnabledChecked?>  id="isenable" name="isenable">
                                    </div>
                               </div>
                               
                               <div class="form-group row">
                               	<label class="col-lg-11 col-form-label btn-primary">Add Rates variants for selected dates.</label>
                               </div>
                               
                               <div class="form-group row">
                       				<label class="col-lg-1 col-form-label">Dates</label>
                                    <div class="col-lg-4">
                                    	<input type="text" id="priceDates" value="<?php echo $pricingDates?>" name="priceDates" required placeholder="Select Dates" class="form-control">
                                    </div>
                               
                       				<label class="col-lg-1 col-form-label">Price</label>
                                    <div class="col-lg-2">
                                    	<input type="text" value="<?php echo $price?>"  id="price" name="price" required placeholder="Rate" class="form-control">
                                    </div>
                               		<label class="col-lg-1 col-form-label">Description</label>
                                    <div class="col-lg-2">
                                    	<input type="text" value="<?php echo $priceDes?>" id="priceDescription" name="priceDescription" required placeholder="Description" class="form-control">
                                    </div>
                                    <label class="col-lg-1 col-form-label"> 
                                    		<i class="fa fa-times"></i>
                                    </label>
                               </div>
                               
                               
                               <div class="form-group row">
                               <label class="col-lg-5 col-form-label"> 
                                    		<a href="#"><i class="fa fa-plus"> Add More Dates</i></a> 
                               </label>
                               </div>
                               <div class="form-group row">
                               		<div class="col-lg-6">
	                               		<button class="btn btn-primary" type="button" onclick="javascript:submitMenuForm()" id="rzp-button" style="float:right">
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
	    $('#priceDates').multiDatesPicker({
	       timepicker:false,
	       dateFormat: "d-m-y",
           minDate:new Date()  
   		});
    });
    function submitMenuForm(){
    	if($("#menuForm")[0].checkValidity()) {
    		 $('#menuForm').ajaxSubmit(function( data ){
	    		 var obj = $.parseJSON(data);
	    		 if(obj.success == 1){
	        		 location.href = "adminShowMenus.php";
	    		 }else{
	        		 alert("Error" + obj.message);
	    		 }	 
	    	 });
    	}else{
    		$("#menuForm")[0].reportValidity();
    	}
    } 
    $("#menuImage").change(function(){
    	readIMG(this);
    });
    function readIMG(input){
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#badgeImg').attr('src', e.target.result);
                $("#imageError").text("");
                $("#badgeImg").removeClass("hilight");
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
 </script>	