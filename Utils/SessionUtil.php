<?php
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
			session_start();
		   	self::$sessionUtil = new SessionUtil();
			return self::$sessionUtil;
		}
		return self::$sessionUtil;
	}

    public function createAdminSession(Admin $admin){
        $arr = new ArrayObject();
        $arr[0] = $admin->getSeq();
        $arr[1] = $admin->getName();
        $arr[2] = $admin->getUserName();
        $_SESSION[self::$ADMIN_LOGGED_IN] = $arr;
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

    public function getAdminLoggedInName(){
      if( $_SESSION[self::$ADMIN_LOGGED_IN] != null){
                $arr = $_SESSION[self::$ADMIN_LOGGED_IN];
                return $arr[1];
        }
    }
    
    public function getAdminLoggedInUserName(){
    	if($_SESSION[self::$ADMIN_LOGGED_IN] != null){
    			$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
    			return $arr[2];
    	}
    }

    
    public function getAdminLoggedInSeq(){
        if($_SESSION[self::$ADMIN_LOGGED_IN] != null){
	    				$arr = $_SESSION[self::$ADMIN_LOGGED_IN];
	    				return $arr[0];
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

  

}
?>