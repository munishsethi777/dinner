<?php
    require_once('IConstants.inc');
    require_once($ConstantsArray['dbServerUrl'] ."Utils/SessionUtil.php");
    $sessionUtil = SessionUtil::getInstance();
    $sessionUtil->destroySession();
?>
