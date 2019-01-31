<?php
  class FilterUtil{
      public static function getFilters(){
        $pagenum = intval($_GET['pagenum']);
        $pagesize = intval($_GET['pagesize']);
        $start = $pagenum;
        if($pagenum > 1){
             $start = $pagenum * $pagesize;    
        }       
        $filter["start"] = $start;
        $filter["limit"] = $pagesize;
        return $filter;
      }
  
  private static function appendLimit($query){
    if(isset($_GET['pagenum'])){
        $pagenum = intval($_GET['pagenum']);
        $pagesize = intval($_GET['pagesize']);
        $start = $pagenum;
        //if($pagenum > 1){
             $start = $pagenum * $pagesize;    
       // }       
        $query = $query . " limit " . $start . "," . $pagesize;
    }elseif(isset($_POST['pagenum'])){
	    	$pagenum = intval($_POST['pagenum']);
	    	$pagesize = intval($_POST['pagesize']);
	    	$start = $pagenum;
	    	//if($pagenum > 1){
	    	$start = $pagenum * $pagesize;
	    	// }
	    	$query = $query . " limit " . $start . "," . $pagesize;
    }
    return $query;         
  }
  
  private static function appendSorting($query){
    if (isset($_GET['sortdatafield']))
    {
        $sortfield = $_GET['sortdatafield'];
        $sortorder = $_GET['sortorder'];  
        $where = "";
        $groupBy = "";
        if ($sortfield != NULL)
        {
        	
        	if($sortfield == "menus.title" || $sortfield == "usedtimes"){
        		$wherePos = strpos(strtolower ($query),'where');
        		$where = "";
        		if ($wherePos !== false) {
        			$q = substr($query, 0,$wherePos). " ";
        			$where = substr($query,$wherePos) ;
        			$query = $q;
        		}
        		$groupByPos = strpos(strtolower ($query),'group by');
        		if ($groupByPos !== false) {
        			$q = substr($query, 0,$groupByPos). " ";
        			$groupBy = substr($query,$groupByPos) ;
        			$query = $q;
        		}
        		if($sortfield == "menus.title"){
	        		$joinPos = strpos(strtolower ($query),'inner join bookingdetails');
	        		if($joinPos !== false){
	        		}else{
	        			$query .= " inner join bookingdetails on bookings.seq = bookingdetails.bookingseq inner join menus on bookingdetails.menuseq = menus.seq";
	        		}
        		}
        	}
        	$query .= $where . " " . $groupBy;
            if ($sortorder == "desc")
            {
                $query = $query . " ORDER BY" . " " . $sortfield . " DESC";
            }
            else if ($sortorder == "asc")
            {
                $query = $query . " ORDER BY" . " " . $sortfield . " ASC";
            }            
        }
    }  
    return $query;  
  }
  public static function applyFilter($query,$isApplyLimit = true,$isAppendGroupBy= false){
    // filter data.
    $isMenuField = true;
    if (isset($_GET['filterscount']))
    {
        $filterscount = $_GET['filterscount'];
        
        if ($filterscount > 0)
        {
            if (strpos(strtolower ($query),'where') !== false) {
                $where = " AND (";
            }else{
                 $where = " WHERE (";    
            }
           
            $tmpdatafield = "";
            $tmpfilteroperator = "";
            for ($i=0; $i < $filterscount; $i++)
            {
            	
                // get the filter's value.
                $filtervalue = $_GET["filtervalue" . $i];
                // get the filter's condition.
                $filtercondition = $_GET["filtercondition" . $i];
                // get the filter's column.
                $filterdatafield = $_GET["filterdatafield" . $i];
                // get the filter's operator.
                $filteroperator = $_GET["filteroperator" . $i];
                
                if ($tmpdatafield == "")
                {
                    $tmpdatafield = $filterdatafield;            
                }
                else if ($tmpdatafield <> $filterdatafield)
                {
                    $where .= ")AND(";
                }
                else if ($tmpdatafield == $filterdatafield)
                {
                    if ($tmpfilteroperator == 0)
                    {
                        $where .= " AND ";
                    }
                    else $where .= " OR ";    
                }
                
                // build the "WHERE" clause depending on the filter's condition, value and datafield.
                switch($filtercondition)
                {
                    case "NOT_EMPTY":
                    case "NOT_NULL":
                        $where .= " " . $filterdatafield . " NOT LIKE '" . "" ."'";
                        break;
                    case "EMPTY":
                    case "NULL":
                        $where .= " " . $filterdatafield . " LIKE '" . "" ."'";
                        break;
                    case "CONTAINS_CASE_SENSITIVE":
                        $where .= " BINARY  " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
                        break;
                    case "CONTAINS":
                        $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
                        break;
                    case "DOES_NOT_CONTAIN_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
                        break;
                    case "DOES_NOT_CONTAIN":
                        $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
                        break;
                    case "EQUAL_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield . " = '" . $filtervalue ."'";
                        break;
                    case "EQUAL":
                    	if($filterdatafield == "menus.title"){
                    		$joinPos = strpos(strtolower ($query),'inner join bookingdetails');
                    		if($joinPos !== false){
                    		}else{
                    			$query .= " inner join bookingdetails on bookings.seq = bookingdetails.bookingseq inner join menus on bookingdetails.menuseq = menus.seq";
                    		}
                    	}
                        $where .= " " . $filterdatafield . " = '" . $filtervalue ."'";
                        break;
                    case "NOT_EQUAL_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield . " <> '" . $filtervalue ."'";
                        break;
                    case "NOT_EQUAL":
                        $where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";
                        break;
                    case "GREATER_THAN":
                    	$where .= " " . $filterdatafield . " > '" . $filtervalue ."'";
                        break;
                    case "LESS_THAN":
                        $where .= " " . $filterdatafield . " < '" . $filtervalue ."'";
                        break;
                    case "GREATER_THAN_OR_EQUAL":
                    	if($filterdatafield == "bookedon"){
                    		//$filtervalue .= " 00:00:00";
                    		//$bookedOn = DateUtil::StringToDateByGivenFormat("d-m-Y H:i:s",$filtervalue);
                    		$bookedOn = DateUtil::StringToDateByGivenFormat("D M d Y H:i:s e+",$filtervalue);
                    		$filtervalue = $bookedOn->format("Y-m-d H:i:s");
                    	}
                    	if($filterdatafield == "bookingdate"){
                    		$filtervalue .= " 00:00:00";
                    		$bookedOn = DateUtil::StringToDateByGivenFormat("d-m-Y H:i:s",$filtervalue);
                    		$filtervalue = $bookedOn->format("Y-m-d H:i:s");
                    	}
                        $where .= " " . $filterdatafield . " >= '" . $filtervalue ."'";
                        break;
                    case "LESS_THAN_OR_EQUAL":
                    	if($filterdatafield == "bookedon"){
                    		//$filtervalue .= " 00:00:00";
                    		//$bookedOn = DateUtil::StringToDateByGivenFormat("d-m-Y H:i:s",$filtervalue);
                    		$bookedOn = DateUtil::StringToDateByGivenFormat("D M d Y H:i:s e+",$filtervalue);
                    		$filtervalue = $bookedOn->format("Y-m-d H:i:s");
                    	}
                    	if($filterdatafield == "bookingdate"){
                    		$filtervalue .= " 00:00:00";
                    		$bookedOn = DateUtil::StringToDateByGivenFormat("d-m-Y H:i:s",$filtervalue);
                    		$filtervalue = $bookedOn->format("Y-m-d H:i:s");
                    	}
                    	
                        $where .= " " . $filterdatafield . " <= '" . $filtervalue ."'";
                        break;
                    case "STARTS_WITH_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
                        break;
                    case "STARTS_WITH":
                        $where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
                        break;
                    case "ENDS_WITH_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
                        break;
                    case "ENDS_WITH":
                        $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
                        break;
                }
                                
                if ($i == $filterscount - 1)
                {
                    $where .= ")";
                }
                $tmpfilteroperator = $filteroperator;
                $tmpdatafield = $filterdatafield;            
            }
            // build the query.
            //if($filterdatafield != "menus.title" || !$isMenuField){
            	$query = $query . $where;
            //}
        }
        
        
          
      }
      
      if($isAppendGroupBy){
      	$query .= " group by bookings.seq ";
      }
      
      //apply Sorting
      $query = FilterUtil::appendSorting($query);
     
       
      //apply limit
      if($isApplyLimit){
        $query = FilterUtil::appendLimit($query);
      }
      
      return $query;
  }
}
?>
