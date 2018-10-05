<?php
require_once($ConstantsArray['dbServerUrl'] ."BusinessObjects/Company.php");
require_once($ConstantsArray['dbServerUrl'] ."DataStores/CompanyDataStore.php");
require_once($ConstantsArray['dbServerUrl'] ."Utils/AuthUtil.php");
class SessionUtil{
    private static $LOGIN_MODE = "loginMode";
    private static $ADMIN_SEQ = "adminSeq";
    private static $ADMIN_NAME = "adminName";
    private static $ADMIN_USERNAME = "adminUserName";
    private static $ADMIN_LOGGED_IN = "adminLoggedIn";
    private static $ADMIN_COMPANY_SEQ = "adminCompanySeq";
    private static $ADMIN_COMPANY_NAME = "adminCompanyName";
    private static $MANAGER_LEARNER_SEQ = "managerLearnerSeq";
    
    //manager credentials
    private static $MANAGER_LEARNINGPLANS = "managerLearningPlans";
    private static $MANAGER_LEARNERPROFILES = "managerLearnerProfiles";

    private static $USER_SEQ = "userSeq";
    private static $USER_USERNAME = "userUserName";
    private static $USER_LOGGED_IN = "userLoggedIn";
    private static $USER_COMPANY_SEQ = "userCompanyseq";
    private static $USER_COMPANY_NAME = "userCompanyName";
    private static $LEARNER_MANAGER_SEQ = "learnerManagerSeq";
    private static $COMPANY_TYPE = "companyType";

	private static $USER_IMAGE = "userimage";


    private static $ROOM_ID = "roomId";

    private static $ROLE = "role";
    
  
    
	private static $sessionUtil;	
	public static function getInstance(){
		if(!self::$sessionUtil){
			//if (!headers_sent()){
            	session_start();
		    //}
			self::$sessionUtil = new SessionUtil.php();
			return self::$sessionUtil;
		}
		return self::$sessionUtil;
	}

    public function createAdminSession(Admin $admin){
        $CDS = CompanyDataStore::getInstance();
        $company = $CDS->FindBySeq($admin->getCompanySeq());
		if(!empty($admin->getLastCompanySeq())){
			$admin->setCompanySeq($admin->getLastCompanySeq());
		}
		//$paymentDueDate = $admin->getNextPaymentDueOn();
		$isPaymentDue = false;
		//if(!empty($paymentDueDate)){
		//	$paymentDueDate = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$paymentDueDate);
		//	if(new DateTime() > $paymentDueDate){
		//		$isPaymentDue = true;
		//	}
		//}
        $arr = new ArrayObject();
        $arr[0] = $admin->getSeq();
        $arr[1] = $admin->getName();
        $arr[2] = $admin->getCompanySeq();
        $arr[3] = $company->getName();
        $arr[4] = $company->getPermisions();
        $arr[5] = $admin->getUserName();
        $arr[6] = $admin->getLastCompanySeq();
        $arr[7] = 0;//$admin->getIsTrialPeriod();
        $arr[8] = false;//$isPaymentDue;
        $arr[9] = $company->getS3Url() . "/" . $company->getBucketName() . "/";

        $_SESSION[self::$ADMIN_LOGGED_IN] = $arr;
        $_SESSION[self::$LOGIN_MODE] = 'admin';
        $_SESSION[self::$ROLE] = RoleType::ADMIN;
        if($admin->getIsManager()){
            $_SESSION[self::$ROLE] = RoleType::MANAGER;
            $_SESSION[self::$MANAGER_LEARNER_SEQ] = $admin->getUserSeq();
            $adminManager = AdminMgr::getInstance();
            $loggedInManagerCriteria = $adminManager->getManagerCriteria($admin->getSeq());
            if($loggedInManagerCriteria->getCriteriaType() == "learningPlan"){
            	$_SESSION[self::$MANAGER_LEARNINGPLANS] = $loggedInManagerCriteria->getCriteriaValue();//explode(",",$loggedInManagerCriteria->getCriteriaValue());
            }else{
            	$_SESSION[self::$MANAGER_LEARNERPROFILES] = $loggedInManagerCriteria->getCriteriaValue();//explode(",",$loggedInManagerCriteria->getCriteriaValue());
            }
        }
        AuthUtil::init();
    }
    public function setPermissionsOnSession(){
    	AuthUtil::init();
    }
    public function setNexPaymentDueDate(){
    	
    }
    public function getManagerLearningPlans(){
    	if( $_SESSION[self::$ADMIN_LOGGED_IN] != null &&
    			$_SESSION[self::$ROLE] == "manager"){
    				return $_SESSION[self::$MANAGER_LEARNINGPLANS];
    	}
    	return null;
    }
    public function createUserSession(User $user){
        $CDS = CompanyDataStore::getInstance();
        $company = $CDS->FindBySeq($user->getCompanySeq());
        $adminManager = AdminMgr::getInstance();
        $learnerManagerSeq =  $adminManager->findManagerSeqByLearnerSeq($user->getSeq());
        $arr = new ArrayObject();
        $arr[0] = $user->getSeq();
        $arr[1] = $user->getUserName();
        $arr[2] = $user->getCompanySeq();
        $arr[3] = $user->getAdminSeq();
        $arr[4] = $company->getName();
        $arr[5] = $company->getPermisions();
        $arr[6] = $company->getS3Url() . "/" . $company->getBucketName() . "/";
        $_SESSION[self::$USER_LOGGED_IN] = $arr;
        $_SESSION[self::$LOGIN_MODE] = 'user';
        $_SESSION[self::$ROLE] = RoleType::USER;
        $_SESSION[self::$LEARNER_MANAGER_SEQ] = $learnerManagerSeq;
        $_SESSION[self::$USER_IMAGE] = $user->getUserImage();
        
    }
    public function refreshAdminSession(){
    	$adminSeq = self::getAdminLoggedInSeq();
    	if(!empty($adminSeq)){
    		$ADS = AdminDataStore::getInstance();
    		$admin = $ADS->findBySeq($adminSeq);
    		self::createAdminSession($admin);
    		return true;
    	}
    	return false;
    }
    public function refreshSession(){
    	$userSeq = self::getUserLoggedInSeq();
    	if(!empty($userSeq)){
    		$UDS = UserDataStore::getInstance();
    		$user = $UDS->findBySeq($userSeq);
    		self::createUserSession($user);
    		return true;
    	}
    	return false;
    }
    public function checkAdminPermission($permission){
    	$permissions = $this->getAdminLoggedInCompanyPermissions();    	
    	if(!in_array($permission,$permissions)){
    		echo "Invalid Execution. You are not permitted to view this page";
    		die;
    	}
    }
    
    public function getManagerLoggedInUserSeq(){
    	if( $_SESSION[self::$ADMIN_LOGGED_IN] != null && 
    			$_SESSION[self::$ROLE] == "manager"){
    			return $_SESSION[self::$MANAGER_LEARNER_SEQ];
    	}
    	return null;
    }
    public function isSessionAdmin(){
        if($_SESSION[self::$LOGIN_MODE] == "admin" &&
            $_SESSION[self::$ADMIN_LOGGED_IN] != null){
                return true;
        }
    }
    public function isSessionUser(){
        if($_SESSION[self::$LOGIN_MODE] == "user" &&
            $_SESSION[self::$USER_LOGGED_IN] != null){
                return true;
        }
    }
    public function getAdminLoggedInName(){
      if($_SESSION[self::$LOGIN_MODE] == "admin" &&
            $_SESSION[self::$ADMIN_LOGGED_IN] != null){
                $arr = $_SESSION[self::$ADMIN_LOGGED_IN];
                return $arr[1];
        }
    }
    
    public function getAdminLoggedInUserName(){
    	if($_SESSION[self::$LOGIN_MODE] == "admin" &&
    			$_SESSION[self::$ADMIN_LOGGED_IN] != null){
    				$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
    				return $arr[5];
    	}
    }
    public function getAdminLoggedInLastCompanySeq(){
    	if($_SESSION[self::$LOGIN_MODE] == "admin" &&
    			$_SESSION[self::$ADMIN_LOGGED_IN] != null){
    				$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
    				return $arr[6];
    	}
    }

    public function getUserLoggedInName(){
      if($_SESSION[self::$LOGIN_MODE] == "user" &&
            $_SESSION[self::$USER_LOGGED_IN] != null){
                $arr = $_SESSION[self::$USER_LOGGED_IN];
                return $arr[1];
        }
        return null;
    }
    

    public function getUserLoggedInImageName(){
    	if($_SESSION[self::$LOGIN_MODE] == "user" &&
    			$_SESSION[self::$USER_LOGGED_IN] != null){
    				$arr = $_SESSION[self::$USER_LOGGED_IN];
    				return $_SESSION[self::$USER_IMAGE];
    	}
    	return null;
    }

    public function getUserLoggedInSeq(){
    if(!empty($_SESSION)){
        if($_SESSION[self::$LOGIN_MODE] == "user" &&
            $_SESSION[self::$USER_LOGGED_IN] != null){
                $arr = $_SESSION[self::$USER_LOGGED_IN];
                return $arr[0];
        }
    }
    return null;
    }


    public function getUserLoggedInCompanySeq(){
      if($_SESSION[self::$LOGIN_MODE] == "user" &&
            $_SESSION[self::$USER_LOGGED_IN] != null){
                $arr = $_SESSION[self::$USER_LOGGED_IN];
                return $arr[2];
        }
        return null;
    }
    public function getUserLoggedInCompanyName(){
      if($_SESSION[self::$LOGIN_MODE] == "user" &&
            $_SESSION[self::$USER_LOGGED_IN] != null){
                $arr = $_SESSION[self::$USER_LOGGED_IN];
                return $arr[4];
        }
        return null;
    }
    public function getUserLoggedInAdminSeq(){
      if($_SESSION[self::$LOGIN_MODE] == "user" &&
            $_SESSION[self::$USER_LOGGED_IN] != null){
                $arr = $_SESSION[self::$USER_LOGGED_IN];
                return $arr[3];
        }
        return null;
    }
	
    public function getUserLoggedInManagerSeq(){
    	if($_SESSION[self::$LOGIN_MODE] == "user" &&
    			$_SESSION[self::$USER_LOGGED_IN] != null){
    				return  $_SESSION[self::$LEARNER_MANAGER_SEQ];
    	}
    	return null;
    }
 	public function getUserLoggedInCompanyS3Url(){
      if($_SESSION[self::$LOGIN_MODE] == "user" &&
            $_SESSION[self::$USER_LOGGED_IN] != null){
                $arr = $_SESSION[self::$USER_LOGGED_IN];
                return $arr[6];
        }
        return null;
    }
    public function getUserLoggedInCompanyPermissions(){
    	if($_SESSION[self::$LOGIN_MODE] == "user" &&
    			$_SESSION[self::$USER_LOGGED_IN] != null){
    				$arr = $_SESSION[self::$USER_LOGGED_IN];
    				$permissions = $arr[5];
    				if(!empty($permissions)){
    					$permissions = explode(",", $permissions);
    					return $permissions;
    				}
    				return array();
    	}
    	return array();
    }
    
    
    public function getAdminLoggedInCompanySeq(){
    	if(isset($_SESSION)){
	        if((count($_SESSION) > 0) && !empty($_SESSION[self::$LOGIN_MODE])){
	          if($_SESSION[self::$LOGIN_MODE] == "admin" &&
	                $_SESSION[self::$ADMIN_LOGGED_IN] != null){
	                    $arr = $_SESSION[self::$ADMIN_LOGGED_IN];
	                    return $arr[2];
	            }
	        }
        }
    }
    public function isAdminPaymentDue(){
    	if(isset($_SESSION)){
    		if((count($_SESSION) > 0) && !empty($_SESSION[self::$LOGIN_MODE])){
    			if($_SESSION[self::$LOGIN_MODE] == "admin" &&
    					$_SESSION[self::$ADMIN_LOGGED_IN] != null){
    						$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
    						return $arr[8];
    			}
    		}
    	}
    }
    
//     public function getAdminLoggedInCompanyType(){
//     	if(isset($_SESSION)){
//     		if((count($_SESSION) > 0) && !empty($_SESSION[self::$LOGIN_MODE])){
//     			if($_SESSION[self::$LOGIN_MODE] == "admin" &&
//     					$_SESSION[self::$ADMIN_LOGGED_IN] != null){
//     						$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
//     						return $arr[5];
//     			}
//     		}
//     	}
//     }
    
    public function getAdminLoggedInCompanyName(){
      if($_SESSION[self::$LOGIN_MODE] == "admin" &&
            $_SESSION[self::$ADMIN_LOGGED_IN] != null){
                $arr = $_SESSION[self::$ADMIN_LOGGED_IN];
                return $arr[3];
        }
        return null;
    }
    
    public function getAdminLoggedInTrialPeriod(){
    	if($_SESSION[self::$LOGIN_MODE] == "admin" &&
    			$_SESSION[self::$ADMIN_LOGGED_IN] != null){
    				$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
    				$isTrial = false;
    				if(!empty($arr[7])){
    					$isTrial = true;
    				}
    				return $isTrial;
    	}
    	return false;
    }
    
    public function getAdminLoggedInCompanyPermissions(){
    	if($_SESSION[self::$LOGIN_MODE] == "admin" &&
    			$_SESSION[self::$ADMIN_LOGGED_IN] != null){
    				$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
    				$permissions = $arr[4];
    				if(!empty($permissions)){
    					$permissions = explode(",", $permissions);
    					return $permissions;
    				}
    				return array();
    	}
    	return array();
    }
    
    public function getAdminLoggedInSeq(){
        if((count($_SESSION) > 0) && !empty($_SESSION[self::$LOGIN_MODE])){
            if($_SESSION[self::$LOGIN_MODE] == "admin" &&
                $_SESSION[self::$ADMIN_LOGGED_IN] != null){
                    $arr = $_SESSION[self::$ADMIN_LOGGED_IN];
                    return $arr[0];
            }
        }
    }
    
    public function getAdminLoggedInCompanyS3Url(){
    	if((count($_SESSION) > 0) && !empty($_SESSION[self::$LOGIN_MODE])){
    		if($_SESSION[self::$LOGIN_MODE] == "admin" &&
    				$_SESSION[self::$ADMIN_LOGGED_IN] != null){
    					$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
    					return $arr[9];
    		}
    	}
    }

    public function getLoggedInRole(){
    	if(array_key_exists(self::$ROLE, $_SESSION)){
        	return $_SESSION[self::$ROLE];
    	}else{
    		return null;
    	}
    }

    public function destroySession(){
        $boolAdmin = self::isSessionAdmin();
        $boolUser = self::isSessionUser();
        $_SESSION = array();
        session_destroy();
        if($boolAdmin == true){
            header("Location:adminLogin.php");
            die;
        }

        if($boolUser == true){
            header("Location:userLogin.php");
            die;

        }
        AuthUtil::destroy();
    }
    public function sessionCheck($loginType){
        $bool = self::isSessionAdmin();
        if($loginType == LoginType::USER){
            $bool = self::isSessionUser();
            if($bool == false){
                header("location: userLogin.php");
                die;
            }
        }else{      	
            if($bool == false){
                header("location: adminLogin.php");
                die;
            }else{
            	$isPaymentDue = $this->isAdminPaymentDue();
            	if($isPaymentDue){
            		header("location: paymentForm.php");
            		die;
            	}else{
            		$page = basename ( $_SERVER ['PHP_SELF'] );
	            	if($page != 'adminPackage.php'){
		            	if(!AuthUtil::isAuthenticate($page)){
		            		header("location: logout.php");
		            		die;
		            	}
	            	}
            	}
            }
        }
    }

    public function setRoomIdOnSessision($roomId){
    	$_SESSION[self::$ROOM_ID] = $roomId;
    }
    
    public function getRoomId(){
    	return $_SESSION[self::$ROOM_ID];
    }

    public function createMobileUserSession($GET){
    	if(isset($GET["userSeq"])){
	    	$userSeq = $GET["userSeq"];
	    	$companySeq = $GET["companySeq"];
	    	$arr = new ArrayObject();
	        $arr[0] = $userSeq;
	        $arr[2] = $companySeq;
	        $_SESSION[self::$USER_LOGGED_IN] = $arr;
	        $_SESSION[self::$LOGIN_MODE] = 'user';
	        $_SESSION[self::$ROLE] = RoleType::USER;
    	}
    }
  

}
?>