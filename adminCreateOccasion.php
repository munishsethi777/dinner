<?php
include("SessionCheck.php");
require_once('IConstants.inc');
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/OccasionMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "BusinessObjects/Occasion.php");
$Occasion = new Occasion();
$isEnableChecked = "checked";
if(isset($_POST["seq"])){
	$seq = $_POST["seq"];
	$OccasionMgr = OccasionMgr::getInstance();
	$Occasion = $OccasionMgr->findBySeq($seq);
	if(!empty($Occasion->getIsEnabled())){
		$isEnableChecked = "checked";
	}else{
		$isEnableChecked = "";
	}
}
?>
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Create Occasion</title>
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
	                        <h5>Create Occasion</h5>
	                    </div>
	                    <div class="ibox-content">
		                	<form id="OccasionForm" method="post" enctype="multipart/form-data" action="Actions/OccasionAction.php" class="m-t-lg">
	                        		<input type="hidden" id ="call" name="call"  value="saveOccasion"/>
	                        		<input type="hidden" id ="seq" name="seq"  value="<?php echo $Occasion->getSeq()?>"/>
	                        		<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Title</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" maxLength="25" value="<?php echo $Occasion->getTitle()?>"  id="title" name="title" required placeholder="Title" class="form-control">
	                                    </div>
	                               </div>
	                               <div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Description</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" maxLength="250" value="<?php echo $Occasion->getDescription()?>"  id="description" name="description" required placeholder="Description" class="form-control">
	                                    </div>
	                               </div>
	                               <div class="form-group row i-checks">
	                       				<label class="col-lg-2 col-form-label">Enabled</label>
	                                    <div class="col-lg-4">
	                                    	<input type="checkbox" <?php echo $isEnableChecked?>  id="isenabled" name="isenabled">
	                                    </div>
	                                </div>
	                               <div class="form-group row">
                               	   <div class="col-lg-6">
	                               		<button class="btn btn-primary" type="button" onclick="submitOccasionForm()" style="float:right">
	                               			Save Occasion
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
 $(document).ready(function(){
	    $('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
		   	radioClass: 'iradio_square-green',
		});
  });

function submitOccasionForm(){
	if($("#OccasionForm")[0].checkValidity()) {
  	 $('#OccasionForm').ajaxSubmit(function( data ){
			 var obj = $.parseJSON(data);
	    	 if(obj.success == 1){
	        	 location.href = "adminShowOccasions.php";
	    	 }else{
	        	 alert("Error" + obj.message);
	  		 }	 
		  });
	}else{
		$("#OccasionForm")[0].reportValidity();
	}
} 
</script>