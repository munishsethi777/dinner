<?php
require_once('../IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Menu.php");
require_once($ConstantsArray['dbServerUrl'] ."Managers/MenuMgr.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/FileUtil.php");
$call = "";
if(isset($_GET["call"])){
	$call = $_GET["call"];
}else{
	$call = $_POST["call"];
}

$success = 1;
$message = "";
if($call == "saveMenu"){
	try{
		$title = $_POST["title"];
		$desciption = $_POST["description"];
		$rate = $_POST["rate"];
		$imageType = $_POST['imageName'];
		$seq = $_POST["seq"];
		if(empty( $_POST["seq"])){
			$seq = 0;
		}
		$isEnabled = 0;
		if (isset ($_POST ['isenable'] )) {
			$isEnabled = 1;
		}
		if(isset($_FILES["menuImage"])){
			$file = $_FILES["menuImage"];
			$filename = $file["name"];
			if(!empty($filename)){
				$imageType = pathinfo($filename, PATHINFO_EXTENSION);
			}
		}
		$menu = new Menu();
		$menu->setSeq($seq);
		$menu->setDescription($desciption);
		$menu->setRate($rate);
		$menu->setTitle($title);
		$menu->setImageName($imageType);
		$menu->setIsEnabled($isEnabled);
		$menuMgr = MenuMgr::getInstance();
		$id = $menuMgr->saveMenu($menu);
		if(isset($_FILES["menuImage"])){
			$file = $_FILES["menuImage"];
			$filename = $file["name"];
			if(!empty($filename)){
				$uploaddir = $ConstantsArray["ImagePath"];
				$name = $id . ".".$imageType;
				FileUtil::uploadImageFiles($file,$uploaddir,$name);
			}
		}
	}catch (Exception $e){
		$success = 0;
		$message  = $e->getMessage();
	}
	$response = new ArrayObject();
	$response["success"]  = $success;
	$response["message"]  = $message;
	echo json_encode($response);
	return;
}
if($call == "getAllMenus"){
	$menuMgr = MenuMgr::getInstance();
	$bookingJson = $menuMgr->getAllMenusForGrid();
	echo json_encode($bookingJson);
}
if($call == "deleteMenus"){
	    $ids = $_GET["ids"];
	    $imageNames = $_GET["imagenames"];
	    try{
	       	$menuMgr = MenuMgr::getInstance();
	        $flag = $menuMgr->deleteBySeqs($ids,$imageNames);
	        $message = "Menu(s) Deleted successfully";
	    }catch(Exception $e){
	        $success = 0;
	        $message = $e->getMessage();
	        //$message = ErrorUtil::checkReferenceError(LearningPlan::$className,$e);
	    }
	    $response = new ArrayObject();
	    $response["message"] = $message;
	    $response["success"] =  $success;
	    echo json_encode($response);
}
if($call == "getMenusByTimeSlot"){
	$menuMgr = MenuMgr::getInstance();
	$timeSlotSeq = $_GET["timeSlotSeq"];
	$selectedDate = $_GET["selectedDate"];
	$bookingSeq = $_GET["bookingSeq"];
	$menus = $menuMgr->getMenusAndSeats($selectedDate,$timeSlotSeq,$bookingSeq);
	echo json_encode($menus);
}
if($call == "getMenus"){
	$menuMgr = MenuMgr::getInstance();
	$menus = $menuMgr->getAllMenuTitle();
	echo json_encode($menus);
}
