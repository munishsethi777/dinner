<?php
require_once('IConstants.inc');
require_once($ConstantsArray['dbServerUrl'] ."Utils/SessionUtil.php");
$session = SessionUtil::getInstance();
$session->sessionCheck();
?>