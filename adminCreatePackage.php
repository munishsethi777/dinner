<?php
include("SessionCheck.php");
require_once('IConstants.inc');
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/PackageMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "BusinessObjects/Package.php");
$package = new Package();
$isEnableChecked = "checked";
if(isset($_POST["seq"])){
	$seq = $_POST["seq"];
	$packageMgr = PackageMgr::getInstance();
	$package = $packageMgr->findBySeq($seq);
	if(!empty($package->getIsEnabled())){
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
<title>Admin - Create Package</title>
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
	                        <h5>Create Package</h5>
	                    </div>
	                    <div class="ibox-content">
		                	<form id="packageForm" method="post" enctype="multipart/form-data" action="Actions/PackageAction.php" class="m-t-lg">
	                        		<input type="hidden" id ="call" name="call"  value="savePackage"/>
	                        		<input type="hidden" id ="seq" name="seq"  value="<?php echo $package->getSeq()?>"/>
	                        		<div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Title</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" maxLength="25" value="<?php echo $package->getTitle()?>"  id="title" name="title" required placeholder="Title" class="form-control">
	                                    </div>
	                               </div>
	                               <div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Description</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" maxLength="250" value="<?php echo $package->getDescription()?>"  id="description" name="description" required placeholder="Description" class="form-control">
	                                    </div>
	                               </div>
	                               <div class="form-group row">
	                       				<label class="col-lg-2 col-form-label">Price</label>
	                                    <div class="col-lg-4">
	                                    	<input type="text" value="<?php echo $package->getPrice()?>"  id="price" name="price" required placeholder="Price" class="form-control">
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
	                               		<button class="btn btn-primary" type="button" onclick="submitPackageForm()" style="float:right">
	                               			Save Package
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

function submitPackageForm(){
	if($("#packageForm")[0].checkValidity()) {
  	 $('#packageForm').ajaxSubmit(function( data ){
			 var obj = $.parseJSON(data);
	    	 if(obj.success == 1){
	        	 location.href = "adminShowPackages.php";
	    	 }else{
	        	 alert("Error" + obj.message);
	  		 }	 
		  });
	}else{
		$("#packageForm")[0].reportValidity();
	}
} 
</script>