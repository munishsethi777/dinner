<?php
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/TimeSlotMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/MenuMgr.php");
$timeSlot = new TimeSlot();
$menuMgr = MenuMgr::getInstance();
$menus = $menuMgr->findAll();
$selectedMenuSeqs = array();
if(isset($_POST["seq"])){
	$seq = $_POST["seq"];
	$timeSlotMgr = TimeSlotMgr::getInstance();
	$timeSlot = $timeSlotMgr->findBySeq($seq);
	$selectedMenuSeqs = $menuMgr->getMenusSeqsByTimeSlot($seq);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Time Slot</title>
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
	                        <h5>Create Time Slot</h5>
	                    </div>
	                </div>
	                <div class="ibox-content">
	                	<form id="timeSlotForm" method="post" action="Actions/TimeSlotAction.php">
	                			<input type="hidden" id ="call" name="call"  value="saveTimeSlot"/>
                        		<input type="hidden" id ="seq" name="seq"  value="<?php echo $timeSlot->getSeq()?>"/>
                        		<div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Time</label>
                                    <div class="col-lg-4">
                                    	<input type="text" value="<?php echo $timeSlot->getTitle()?>"  id="title" name="title" required placeholder="Time 6 PM to 8 PM" class="form-control">
                                    </div>
                               </div>
                                <div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Description</label>
                                    <div class="col-lg-4">
                                    	<input type="text" value="<?php echo $timeSlot->getDescription()?>"  id="description" name="description"  placeholder="Description" class="form-control">
                                    </div>
                               </div>
                               <div class="form-group row">
                       				<label class="col-lg-2 col-form-label">Seats</label>
                                    <div class="col-lg-4">
                                    	<input type="text" value="<?php echo $timeSlot->getSeats()?>"  id="seats" name="seats" required placeholder="Seats" class="form-control touchspin3">
                                    </div>
                               </div>
                               <div class="form-group row i-checks">
                       				<label class="col-lg-2 col-form-label">Menus</label>
                                    <div class="col-lg-4">
                                    	<select class="form-control chosen-select" required id="menuDD" name="menus[]" multiple>
											<?php foreach ($menus as $menu){
												$menuSeq = $menu->getSeq();
												$selected = "";
												if(in_array($menuSeq, $selectedMenuSeqs)){
													$selected = "selected";
												}	
												?>
												<option <?php echo $selected ?> value="<?php echo $menuSeq?>"><?php echo $menu->getTitle()?></option>
												
											<?php }?>
										</select> <label class="jqx-validator-error-label" id="lpError"></label>
								    </div>
                               </div>
                               <div class="form-group row">
                               		<div class="col-lg-6">
	                               		<button class="btn btn-primary" type="button" onclick="javascript:submitMenuForm()" id="rzp-button" style="float:right">
	                               			 Save 
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
	    $(".chosen-select").chosen({width:"100%"});
	    $(".touchspin3").TouchSpin({
            verticalbuttons: true,
            buttondown_class: 'btn btn-white',
            buttonup_class: 'btn btn-white'
        });
    });
    function submitMenuForm(){
         if($('#menuDD').val() == ""){
         	alert("Select menu option");
         }
    	 $('#timeSlotForm').ajaxSubmit(function( data ){
    		 var obj = $.parseJSON(data);
    		 if(obj.success == 1){
        		 location.href = "adminShowMenus.php";
    		 }else{
        		 alert("Error" + obj.message);
    		 }	 
    	 });
    } 
 </script>	