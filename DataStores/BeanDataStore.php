<?php
require_once ("MainDB.php5");
require_once ($ConstantsArray ['dbServerUrl'] . "log4php/Logger.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Managers/AdminMgr.php");
require_once ($ConstantsArray ['dbServerUrl'] . "Utils/FilterUtil.php");
Logger::configure ( $ConstantsArray ['dbServerUrl'] . "log4php/log4php.xml" );
class BeanDataStore {
	private $className;
	private $tableName;
	private $companySeq;
	private $logger ;
	private $isManager;
	private $loggedInAdminSeq;
	public function __construct($className_, $tableName) {
		$this->className = $className_;
		$this->tableName = $tableName;
		$sessionUtil = SessionUtil::getInstance ();
		$this->companySeq = $sessionUtil->getAdminLoggedInCompanySeq ();
		$this->loggedInAdminSeq = $sessionUtil->getAdminLoggedInSeq();
		$this->logger = Logger::getLogger ( "logger" );
		$this->isManager = $sessionUtil->getLoggedInRole() == "manager";
	}
	private function key_implode($array) {
		$fields = array ();
		foreach ( $array as $field => $val ) {
			if (is_null ( $val )) {
				$fields [] = "$field = NULL";
			} else {
				$fields [] = "$field = '$val'";
			}
		}
		$result = join ( ', ', $fields );
		return $result;
	}
	public function save($object) {
	    $id = null;
		try {
			$columnValueArry [] = array ();
			$columns [] = array ();
			$count = 0;
			$class = new ReflectionClass ( $this->className );
			$methods = $class->getMethods ( ReflectionMethod::IS_PUBLIC );
			$id;
			if($this->className == "ChatroomChat"){
				$id = $object->getPost_Id();
			}else{
				$id = $object->getSeq ();
			}
			if($id == 0){
				$this->checkAdminPackageLimit();
			}
			foreach ( $methods as $method ) {
				$methodName = $method->name;
				if (! $this->startsWith ( $methodName, "set" )) {
					if ($count > 0) {
						$reflect = new ReflectionMethod ( $object, $methodName );
						if ($reflect->isPublic ()) {
							$val = call_user_func ( array (
									$object,
									$methodName 
							) );
							$column = strtolower ( substr ( $methodName, 3 ) );
							$columns [] = $column;
							$value = call_user_func ( array (
									$object,
									$methodName 
							) );
							if ($value instanceof DateTime) {
								if($column == "createdon" && $id > 0){
									continue;
								}
								$value = $value->format ( 'Y-m-d H:i:s' );
							}
							// if($id > 0){
							// $value = "'" . $value . "'";
							// }
							$columnValueArry [$column] = $value;
						}
					}
					$count ++;
				}
			}
			unset ( $columnValueArry [0] );
			unset ( $columns [0] );
			$SQL = "";
			$db_New = MainDB::getInstance ();
			$conn = $db_New->getConnection ();
			
			if ($id > 0) { // update query
				$columnString = implode ( '=?,', array_keys ( $columnValueArry ) );
				$columnString .= "=?";
				$SQL = "Update " . strtolower ( $this->tableName ) . " set " . $columnString . " where seq = " . $id;
				$STH = $conn->prepare ( $SQL );
				$STH->execute ( array_values ( $columnValueArry ) );
			} else { // Insert Query
				$columnString = implode ( ',', array_keys ( $columnValueArry ) );
				$valueString = implode ( ',', array_fill ( 0, count ( $columnValueArry ), '?' ) );
				$SQL = "INSERT INTO " . $this->tableName . " ({$columnString}) VALUES ({$valueString})";
				$STH = $conn->prepare ( $SQL );
				$STH->execute ( array_values ( $columnValueArry ) );
				$id = $conn->lastInsertId ();
			}
			$this->throwException ( $STH->errorInfo () );
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured in BeanDataStore:" . $e );
			throw $e ;
		}
		return $id;
	}
	
	// RollBack
	public function saveObject($object, $conn) {
		$columnValueArry [] = array ();
		$columns [] = array ();
		$count = 0;
		$class = new ReflectionClass ( $this->className );
		$methods = $class->getMethods ( ReflectionMethod::IS_PUBLIC );
		$id = $object->getSeq();
		try {
			if($id == 0){
				//$this->checkAdminPackageLimit();
			}
			foreach ( $methods as $method ) {
				$methodName = $method->name;
				if (! $this->startsWith ( $methodName, "set" )) {
					if ($count > 0) {
						$reflect = new ReflectionMethod ( $object, $methodName );
						if ($reflect->isPublic ()) {
							$val = call_user_func ( array (
									$object,
									$methodName 
							) );
							$column = strtolower ( substr ( $methodName, 3 ) );
							$columns [] = $column;
							$value = call_user_func ( array (
									$object,
									$methodName 
							) );
							if ($value instanceof DateTime) {
								$value = $value->format ( 'Y-m-d H:i:s' );
							}
							// if($id > 0){
							// $value = "'" . $value . "'";
							// }
							$columnValueArry [$column] = $value;
						}
					}
					$count ++;
				}
			}
			unset ( $columnValueArry [0] );
			unset ( $columns [0] );
			$SQL = "";
			$db_New = MainDB::getInstance ();
			
			if ($id > 0) { // update query
				$columnString = $this->key_implode ( $columnValueArry );
				$SQL = "Update " . strtolower ( $this->tableName ) . " set " . $columnString . " where seq = " . $id;
				$STH = $conn->prepare ( $SQL );
				$STH->execute ();
			} else { // Insert Query
				$columnString = implode ( ',', array_keys ( $columnValueArry ) );
				$valueString = implode ( ',', array_fill ( 0, count ( $columnValueArry ), '?' ) );
				$SQL = "INSERT INTO " . $this->tableName . " ({$columnString}) VALUES ({$valueString})";
				$STH = $conn->prepare ( $SQL );
				$STH->execute ( array_values ( $columnValueArry ) );
				$id = $conn->lastInsertId ();
			}
			$this->throwException ( $STH->errorInfo () );
			return $id;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e;
		}
		return null;
	}
	
	function checkAdminPackageLimit(){
		if($this->className == "User" || $this->className == "Company"){
			$adminSeq = $this->loggedInAdminSeq;
			if(!empty($adminSeq)){
				$sql = "select * from adminpackages where adminseq = $adminSeq";
				$result = $this->executeQuery($sql);
				if(!empty($result)){
					$adminPackge = $result[0];
					$totalLearners = $adminPackge["totallearners"];
					$totalCompanies = $adminPackge["totalcompanies"];
					if($this->className == "User"){
						$sql = "select count(*) from users where adminseq = $adminSeq";
						$userCount = $this->executeCountQueryWithSql($sql);
						if($userCount >= $totalLearners){
							throw new Exception ( StringConstants::USER_LIMIT_MESSAGE );
						}
					}else if($this->className == "Company"){
						$sql = "select count(*) from admincompanies where adminseq = $adminSeq";
						$companiesCount = $this->executeCountQueryWithSql($sql);
						if($companiesCount >= $totalCompanies){
							throw new Exception ( StringConstants::COMPANY_LIMIT_MESSAGE );
						}
					}
				}
			}
		}
	}
	
	function findAll($isApplyFilter = false) {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$sql = "select * from " . $this->tableName;
			if ($isApplyFilter) {
				$sql = FilterUtil::applyFilter ( $sql );
			}
			$STH = $conn->prepare ( $sql );
			$STH->execute ();
			$objList = $STH->fetchAll ( PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className );
			$this->throwException ( $STH->errorInfo () );
			return $objList;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e;
		}
	}
	function findAllByCompany($isApplyFilter = false) {
		try {
			$sql = "select ". $this->tableName.".* from " . $this->tableName . " where companyseq =" . $this->companySeq;
			if($this->isManager){
				$sql = $this->appendManagerCriteria($sql);
			}
			if ($isApplyFilter) {
				$sql = FilterUtil::applyFilter ( $sql );
			}
			
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( $sql );
			$STH->execute ();
			$objList = $STH->fetchAll ( PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className );
			$this->throwException ( $STH->errorInfo () );
			return $objList;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	function findBySeq($seq) {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( "select * from " . $this->tableName . " where seq = " . $seq );
			$STH->execute ();
			$obj = $STH->fetchObject ( $this->className );
			$this->throwException ( $STH->errorInfo () );
			return $obj;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	function findArrayBySeq($seq) {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( "select * from " . $this->tableName . " where seq = " . $seq );
			$STH->execute ();
			$obj = $STH->fetch(PDO::FETCH_ASSOC);
			$this->throwException ( $STH->errorInfo () );
			return $obj;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e;
		}
	}
	public function deleteBySeq($seq) {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( "delete from " . $this->tableName . " where seq = " . $seq );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function deleteInList($ids) {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( "delete from " . $this->tableName . " where seq in(" . $ids . ")" );
			$flag = $STH->execute ();
			$this->throwException ( $STH->errorInfo () );
			return $flag;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function deleteByAttribute($colValuePair = null) {
		try {
			foreach ( $colValuePair as $key => $value ) {
				$query_array [] = " $key in ('$value') ";
			}
			$query = "delete FROM " . $this->tableName;
			if ($colValuePair != null) {
				$query .= " WHERE " . implode ( " AND ", $query_array );
			}
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( $query );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw  $e ;
		}
	}
	public function deleteAll() {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( "delete from " . $this->tableName );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function deleteAllByCompany() {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( "delete from " . $this->tableName . " where companyseq = " . $this->companySeq );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function executeConditionQuery($colValuePair, $isApplyFilter = false) {
		try {
			$query_array = array ();
			foreach ( $colValuePair as $key => $value ) {
				if ($value != null || $value==0) {
					$query_array [] = $this->tableName.".".$key . ' = ' . "'" . $value . "'";
				}
			}
			$query = "SELECT ".$this->tableName.".* FROM " . $this->tableName;
			
			if (count ( $query_array ) > 0) {
				$query .= " WHERE " . implode ( " AND ", $query_array );
			}
			if($this->isManager){
				$query = $this->appendManagerCriteria($query);
			}
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query );
			}
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( $query );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
			$objList = $STH->fetchAll ( PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className );
			return $objList;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function executeInListQuery($colValues, $isApplyFilter = false) {
		try {
			$colName = $colValues;
			if (is_array ( $colValues )) {
				$colName = key ( $colValues );
			}
			$query = "SELECT ".$this->tableName.".* FROM " . $this->tableName . " where ".$this->tableName.".$colName in($colValues[$colName])";
			if($this->isManager){
				$query = $this->appendManagerCriteria($query);
			}
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query );
			}
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( $query );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
			$objList = $STH->fetchAll ( PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className );
			return $objList;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function executeInList($colValues, $isApplyFilter = false) {
		try {
			$colName = $colValues;
			if (is_array ( $colValues )) {
				$colName = key ( $colValues );
			}
			$query = "SELECT * FROM " . $this->tableName . " where $colName in($colValues[$colName])";
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query );
			}
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( $query );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
			$objList = $STH->fetchAll ();
			return $objList;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw  $e ;
		}
	}
	public function updateByAttributes($colValuePair, $condiationPair = null) {
		try {
			foreach ( $condiationPair as $key => $value ) {
				$query_array [] = $key . ' = ' . "'" . $value . "'";
			}
			foreach ( $colValuePair as $key => $value ) {
				if ($value instanceof DateTime) {
					$value = $value->format ( 'Y-m-d H:i:s' );
				}
				$attribute_array [] = $key . ' = ' . "'" . $value . "'";
			}
			$query = "update " . $this->tableName . " set " . implode ( " , ", $attribute_array );
			if ($condiationPair != null) {
				$query .= " WHERE " . implode ( " AND ", $query_array );
			}
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( $query );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
			return true;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function updateByAttributesWithBindParams($colValuePair, $condiationPair = null) {
		try {
			$paramValueArr = array ();
			foreach ( $colValuePair as $key => $value ) {
				if ($value instanceof DateTime) {
					$value = $value->format ( 'Y-m-d H:i:s' );
				}
				$attribute_array [] = $key . ' = ?';
				array_push ( $paramValueArr, $value );
			}
			foreach ( $condiationPair as $key => $value ) {
				$query_array [] = $key . ' = ?';
				array_push ( $paramValueArr, $value );
			}
			$query = "update " . $this->tableName . " set " . implode ( " , ", $attribute_array );
			if ($condiationPair != null) {
				$query .= " WHERE " . implode ( " AND ", $query_array );
			}
			
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( $query );
			$STH->execute ( $paramValueArr );
			$this->throwException ( $STH->errorInfo () );
			return true;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function executeCountQuery($colValuePair = null, $isApplyFilter = false) {
		try {
			$query = "SELECT  COUNT(DISTINCT ".$this->tableName.".seq) FROM " . $this->tableName;
			if ($colValuePair != null) {
				foreach ( $colValuePair as $key => $value ) {
					$query_array [] = $this->tableName .".".$key . ' = ' . "'" . $value . "'";
				}
				$query .= " WHERE " . implode ( " AND ", $query_array );
			}
			if($this->isManager){
				$query = $this->appendManagerCriteria($query,true);
			}
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query, false );
			}
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$STH = $conn->prepare ( $query );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
			$result = $STH->fetch ( PDO::FETCH_NUM );
			$count = intval ( $result [0] );
			return $count;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw  $e ;
		}
	}
	
	public function appendManagerCriteria($query,$isCountQuery = false){
		$managerCriteria = AdminMgr::getInstance()->getManagerCriteriaDetail($this->loggedInAdminSeq);
		$managerCriteria = json_decode($managerCriteria,true);
		$criteriaType = $managerCriteria["criteriatype"];
		$criteriaValue = $managerCriteria["criteriavalue"];
		$wherePos = strpos(strtolower ($query),'where');
		
		$queryWithouCondition = $query;
		$queryWhere = " WHERE ";
		$sortBy = "";
		$sortPos = strpos(strtolower ($query),'order by');
		if ($sortPos !== false) {
			$sortBy = substr($query, $sortPos);
		}else{
			$sortPos = strlen($query);
		}
		if ($wherePos !== false) {
			$queryWithouCondition = substr($query, 0,$wherePos);
			$queryWhere = substr($query, $wherePos);
			if(strpos(strtolower ($query),'order by') !== false){
				$queryWhere = str_replace($sortBy, "", $queryWhere);
			}
 			//$queryWhere .= " AND ";
		}
		if($criteriaType == ManagerCriteriaType::LEARNING_PLAN){
			if($this->tableName == "users" || $this->tableName == "userlogs"){
				$queryWithouCondition .= " left join learningplanusers lpu on users.seq = lpu.userseq and lpu.learningplanseq in ($criteriaValue)";
				$queryWithouCondition .= " left join userlearningprofiles ulp on users.seq = ulp.userseq";
				$queryWithouCondition .= " left join learningplanprofiles lpp on ulp.tagseq = lpp.learningprofileseq ";
				$queryWhere .= " AND (lpp.learningplanseq in ($criteriaValue) or lpu.learningplanseq in ($criteriaValue) or users.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition . " " . $queryWhere ;
				if(!$isCountQuery && $this->tableName != "userlogs"){
					$query .= " group by users.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
				
			}
			else if($this->tableName == "learningprofiles"){
				$queryWithouCondition .= " left join learningplanprofiles lpp on learningprofiles.seq = lpp.learningprofileseq ";
				$queryWhere .= " AND (lpp.learningplanseq in ($criteriaValue) or learningprofiles.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition . " " . $queryWhere ;
				if(!$isCountQuery){
					$query .= " group by learningprofiles.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			
			}else if($this->tableName == "learningplans"){
				$queryWhere .= " AND (learningplans.seq in ($criteriaValue) or learningplans.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition . " " . $queryWhere ;
				if(!$isCountQuery){
					$query .= " group by learningplans.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			
			}else if($this->tableName == "modules"){
				$queryWithouCondition .= " left join companymodules m_cm on modules.seq = m_cm.moduleseq 
				left join learningplanmodules m_lpm on modules.seq = m_lpm.courseseq ";
				$queryWhere .= " AND (m_lpm.learningplanseq in ($criteriaValue) or m_cm.adminseq = $this->loggedInAdminSeq )";
				$query = $queryWithouCondition . " " . $queryWhere ;
				if(!$isCountQuery){
					$query .= " group by modules.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			}else if($this->tableName == "classroomtrainings"){
				$queryWithouCondition .= " left join learningplantrainings on learningplantrainings.trainingseq = classroomtrainings.seq";
				$queryWhere .= " AND (learningplantrainings.learningplanseq in ($criteriaValue) or  classroomtrainings.adminseq = $this->loggedInAdminSeq )";
				$query = $queryWithouCondition ." ". $queryWhere . " " . $sortBy;
				if(!$isCountQuery){
					$query .= " group by classroomtrainings.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			
			}else if($this->tableName == "badges"){
				$queryWithouCondition .= " left join badgetypedetails badgetypedetails1 on badgetypedetails1.badgeseq = badges.seq";
				$queryWhere .= " AND badgetype like 'learningplan' and badgetypedetails1.badgetypeseq in ($criteriaValue) or  badges.adminseq = $this->loggedInAdminSeq";
				$query = $queryWithouCondition ." ". $queryWhere ;
				if(!$isCountQuery){
					$query .= " group by badges.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			
			}else if($this->tableName == "questions"){
				$queryWithouCondition .= " left join modulequestions on modulequestions.questionseq = questions.seq 
										left join learningplanmodules on learningplanmodules.courseseq = modulequestions.moduleseq";
				$queryWhere .= " AND (learningplanmodules.learningplanseq in ($criteriaValue) or  questions.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition ." ". $queryWhere;
				if(!$isCountQuery){
					$query .= " group by questions.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			}
		}
		if($criteriaType == ManagerCriteriaType::LEARNING_PROFILE){
			if($this->tableName == "users" || $this->tableName == "userlogs"){
				$queryWithouCondition .= " left join userlearningprofiles ulp on users.seq = ulp.userseq";
				$queryWhere .= " AND (ulp.tagseq in ($criteriaValue) or users.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition . " " . $queryWhere ;
				if(!$isCountQuery && $this->tableName != "userlogs"){
					$query .= " group by users.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			}else if($this->tableName == "learningprofiles"){
				$queryWhere .= " AND (learningprofiles.seq in ($criteriaValue) or learningprofiles.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition . " " . $queryWhere;
				if(!$isCountQuery && $this->tableName != "userlogs"){
					$query .= " group by learningprofiles.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			}else if($this->tableName == "learningplans"){
				$queryWithouCondition .= " left join learningplanprofiles m_lpp on learningplans.seq = m_lpp.learningplanseq";
				$queryWhere .= " AND (m_lpp.learningprofileseq in ($criteriaValue) or learningplans.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition . " " . $queryWhere;
				if(!$isCountQuery && $this->tableName != "userlogs"){
					$query .= " group by learningplans.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			}else if($this->tableName == "modules"){
				$queryWithouCondition .= " left join companymodules m_cm on modules.seq = m_cm.moduleseq 
						left join learningplanmodules m_lpm on modules.seq = m_lpm.courseseq 
left join learningplanprofiles m_lpp on m_lpm.learningplanseq = m_lpp.learningplanseq ";
				$queryWhere .= " AND (m_lpp.learningprofileseq in ($criteriaValue) or m_cm.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition . " " . $queryWhere ;
				if(!$isCountQuery){
					$query .= " group by modules.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			}else if($this->tableName == "classroomtrainings"){
				$queryWithouCondition .= " left join learningplantrainings on learningplantrainings.trainingseq = classroomtrainings.seq ";
				$queryWithouCondition .= " left join learningplanprofiles m_lpp on learningplantrainings.learningplanseq = m_lpp.learningplanseq  ";
				$queryWhere .= " AND (m_lpp.learningprofileseq in ($criteriaValue) or classroomtrainings.adminseq = $this->loggedInAdminSeq)";
				$query = $queryWithouCondition ." ". $queryWhere;
				if(!$isCountQuery){
					$query .= " group by classroomtrainings.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			}else if($this->tableName == "badges"){
				$queryWithouCondition .= " left join badgetypedetails badgetypedetails1 on badgetypedetails1.badgeseq = badges.seq";
				$queryWithouCondition .= " left join learningplanprofiles m_lpp on badgetypedetails1.badgetypeseq = m_lpp.learningplanseq  ";
				$queryWhere .= " AND ((badgetype like 'learningplan' and m_lpp.learningprofileseq in ($criteriaValue)) or ( badges.adminseq = $this->loggedInAdminSeq))";
				$query = $queryWithouCondition ." ". $queryWhere;
				if(!$isCountQuery){
					$query .= " group by badges.seq " . $sortBy;
				}else{
					$query .= " " . $sortBy;
				}
			}else if($this->tableName == "questions"){
				$queryWithouCondition .= " left join modulequestions on modulequestions.questionseq = questions.seq 
										left join learningplanmodules on learningplanmodules.courseseq = modulequestions.moduleseq
						left join learningplanprofiles on learningplanprofiles.learningplanseq = learningplanmodules.learningplanseq";
				$queryWhere .= " AND (learningplanprofiles.learningprofileseq in ($criteriaValue) or questions.adminseq = $this->loggedInAdminSeq) ";
				$query = $queryWithouCondition ." ". $queryWhere;
				if(!$isCountQuery){
					$query .= " group by questions.seq";
				}else{
					$query .= " " . $sortBy;
				}
			}
				
		}
		return $query;
	}
	
	public function executeCountQueryWithSql($query, $isApplyFilter = false) {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			if($this->isManager){
				$query = $this->appendManagerCriteria($query,true);
			}
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query, false );
			}
			$STH = $conn->prepare ( $query );
			$STH->execute ();
			$this->throwException ( $STH->errorInfo () );
			$result = $STH->fetch ( PDO::FETCH_NUM );
			$count = intval ( $result [0] );
			return $count;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function executeQuery($query, $isApplyFilter = false, $ommitIntegerArrayElements =false) {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			if($this->isManager){
				$query = $this->appendManagerCriteria($query);
			}
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query );
			}
			$sth = $conn->prepare ( $query );
			$sth->execute ();
			$this->throwException ( $sth->errorInfo () );
			$objList = null;
			if($ommitIntegerArrayElements){
				$objList = $sth->fetchAll(PDO::FETCH_ASSOC);
			}else{
				$objList = $sth->fetchAll();
			}
			return $objList;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function insertWithQuery($query) {
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			if($this->isManager){
				$query = $this->appendManagerCriteria($query);
			}
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query );
			}
			$sth = $conn->prepare ( $query );
			$sth->execute ();
			$this->throwException ( $sth->errorInfo () );
			$id = $conn->lastInsertId();
			return $id;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function executeObjectQuery($query, $isApplyFilter = false) {
		$objList = null;
		try {
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$sth = $conn->prepare ( $query );
            if($this->isManager){
               $query = $this->appendManagerCriteria($query);
            }
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query );
			}
			$sth->execute ();
			$this->throwException ( $sth->errorInfo () );
			$objList = $sth->fetchAll ( PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className );
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw  $e ;
		}
		return $objList;
	}
	public function executeAttributeQuery($attributes, $colValuePair, $isApplyFilter = false) {
		try {
			$paramValueArr = array();
			foreach ( $colValuePair as $key => $value ) {
				if ($value != '') {
					$query_array [] = $key . ' = ' . "?";
					array_push ( $paramValueArr, $value );
				}
			}
			$columns = implode ( ", ", $attributes );
			$query = "SELECT " . $columns . " FROM " . $this->tableName . " WHERE " . implode ( " AND ", $query_array );
			if ($isApplyFilter) {
				$query = FilterUtil::applyFilter ( $query, false );
			}
			$db = MainDB::getInstance ();
			$conn = $db->getConnection ();
			$sth = $conn->prepare ( $query );
			$sth->execute ($paramValueArr);
			$this->throwException ( $sth->errorInfo () );
			$objList = $sth->fetchAll ();
			return $objList;
		} catch ( Exception $e ) {
			$this->logger->error ( "Error occured :" . $e );
			throw $e ;
		}
	}
	public function throwException($error) {
	    if ($error [2] != "") {
		    throw new Exception ( $error [2] );
		}
	}
	function startsWith($haystack, $needle) {
		$length = strlen ( $needle );
		return (substr ( $haystack, 0, $length ) === $needle);
	}
	function endsWith($haystack, $needle) {
		$length = strlen ( $needle );
		if ($length == 0) {
			return true;
		}
		return (substr ( $haystack, - $length ) === $needle);
	}
}
?>
