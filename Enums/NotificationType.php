<?php
require_once($ConstantsArray['dbServerUrl'] ."Enums/BasicEnum.php");
class NotificationType extends BasicEnum{
	const bookingClosure = "bookingClosure";
	const cakeOrder = "cakeOrder";
}