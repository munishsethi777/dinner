<?php
require_once($ConstantsArray['dbServerUrl'] ."Enums/BasicEnum.php");
class BookingStatus extends BasicEnum{
	const rescheduled = "Rescheduled";
	const cancel = "Cancelled";
}