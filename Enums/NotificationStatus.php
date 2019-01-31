<?php
require_once($ConstantsArray['dbServerUrl'] ."Enums/BasicEnum.php");
class NotificationStatus extends BasicEnum{
	const pending = "pending";
	const sent = "sent";
	const failed = "failed";
}