<?php
class DateUtil {
	public static function StringToDate($dateStr) {
		return DateTime::createFromFormat ( 'm/d/Y h:i A', $dateStr );
	}
	public static function StringToDateByGivenFormat($format, $dateStr) {
		return DateTime::createFromFormat ( $format, $dateStr );
	}
	
	public static function facebook_style_date_time($timestamp){
		$difference = time() - $timestamp;
		$periods = array("sec", "min", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
		
		if ($difference > 0) { // this was in the past time
		$ending = "ago";
		} else { // this was in the future time
		$difference = -$difference;
		$ending = "to go";
		}
		for($j = 0; $difference >= $lengths[$j]; $j++) $difference /= $lengths[$j];
		$difference = round($difference);
		if($difference != 1) $periods[$j].= "s";
		$text = "$difference $periods[$j] $ending";
		return $text;
	}
	public static function getTimeDiffTillNow($date) {
		$timestamp = strtotime(date_format ( $date, "Y-m-d H:i:s" ));
		return self::facebook_style_date_time($timestamp);
		$diff = $date->diff ( new DateTime () );
		$years = $diff->y;
		$months = $diff->m;
		$days = $diff->d;
		$hours = $diff->h;
		$minuts = $diff->i;
		$time = date_format ( $date, "H:i A" );
		$str = null;
		if (! empty ( $years )) {
			if ($years > 1) {
				$years .= " Years ago ";
			} else {
				$years .= " Year ago";
			}
			$str = $years;
		} else if (! empty ( $months )) {
			if ($months > 1) {
				$months .= " Months ago";
			} else {
				$months .= " Month ago";
			}
			$str .= $months;
		} else if (! empty ( $days )) {
			if ($days >= 2) {
				$days .= " Days ago";
			} else {
				$days = "Yesterday " . $time;
			}
			$str .= $days;
		} else if (! empty ( $hours )) {
			if ($hours < 24) {
				$hours = "Today " . $time;
			}
			$str .= $hours;
		} else {
			$str .= "Today " . $time;
		}
		return $str;
	}
	
	public static function getDateDiffTillNow($date) {
		$diff = $date->diff ( new DateTime () );
		$years = $diff->y;
		$months = $diff->m;
		$days = $diff->d;
		$hours = $diff->h;
		$minuts = $diff->i;
		$time = "on " . date_format ( $date, "M d" );
		$str = null;
		if (! empty ( $years )) {
			if ($years > 1) {
				$years .= " Years ago ";
			} else {
				$years .= " Year ago ";
			}
			$str = $years . $time;
		} else if (! empty ( $months )) {
			if ($months > 1) {
				$months .= " Months ago ";
			} else {
				$months .= " Month ago ";
			}
			$str .= $months . $time;
		} else if (! empty ( $days )) {
			if ($days >= 2) {
				$days .= " Days ago ";
			} else {
				$days = "Yesterday ";
			}
			$str .= $days . $time ;
		} else if (! empty ( $hours )) {
			if ($hours < 24) {
				$hours = "Today " . $time;
			}
			$str .= $hours;
		} else {
			$str .= "Today " . $time;
		}
		return $str;
	}
	public static function getDatesSlicesTillNow($date) {
		$date = DateUtil::StringToDateByGivenFormat("Y-m-d H:i:s",$date);
		$currentDate = new DateTime ();
		$diff = $date->diff ( $currentDate );
		$years = $diff->y;
		$months = $diff->m;
		$days = $diff->d;
		$hours = $diff->h;
		$minuts = $diff->i;
		$time = date_format ( $date, "H:i A" );
		$dateArr = array();
		//if (! empty ( $days )) {
			while ( $date <= $currentDate ) {
				$strDate = $date->format("d-n-y");	
				array_push($dateArr, $strDate);
				$date->modify('+1 day');
			}
		//}
		
		return $dateArr;
	}
	
	public static function getDateDiffTillFutureDate($date) {
		$currentDate = new DateTime();
		$diff = $currentDate->diff ( $date );
		$years = $diff->y;
		$months = $diff->m;
		$days = $diff->d;
		$hours = $diff->h;
		$minuts = $diff->i;
		$time = "on " . date_format ( $date, "M d" );
		$str = null;
		if (! empty ( $years )) {
			if ($years > 1) {
				$years .= " Years ";
			} else {
				$years .= " Year ";
			}
			$str = $years . $time;
		} else if (! empty ( $months )) {
			if ($months > 1) {
				$months .= " Months ";
			} else {
				$months .= " Month ";
			}
			$str .= $months . $time;
		} else if (! empty ( $days )) {
			if ($days >= 2) {
				$days .= " Days ";
			} else {
				$days = "Yesterday ";
			}
			$str .= $days . $time;
		} else if (! empty ( $hours )) {
			if ($hours < 24) {
				$hours = "Today " . $time;
			}
			$str .= $hours;
		} else {
			$str .= "Today " . $time;
		}
		return $str;
	}

	public static function isValidateDate($date,$dateFormat){
		$d = DateTime::createFromFormat($dateFormat, $date);
		return $d && $d->format($dateFormat) === $date;
	}
}
?>
