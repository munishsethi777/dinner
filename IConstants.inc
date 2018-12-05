<?php
 $docroot = $_SERVER["DOCUMENT_ROOT"];
  $ConstantsArray = array('dbServerUrl' => $docroot .'/dinner/'
    ,'imagefolderpath' => $docroot .'/dinner/images/'
    ,'docspath' => $docroot .'/dinner/docs/'
    ,'ImagePath' => $docroot .'/dinner/Images/menuImages/')
  ;
  $ConstantsArray['ApplicationURL'] = "http://localhost/dinner/";

  $ConstantsArray["CUSTOM_FIELD_PREFIX"]  = "cus_";
  //$ConstantsArray = array('dbServerUrl' => '/home/satyain/public_html/giftcard/');
  //Logger::configure($ConstantsArray['dbServerUrl'] .'log4php/log4php.xml');
  $ConstantsArray['ApplicationURL'] = "http://localhost/dinner";
  $Log4PHP_MyDBLogger = "myDBLogger";
  $Log4PHP_MyMailerLogger = "myMailerLogger";
  $Log4PHP_MyFileLogger = "myFileLogger";
  $CUSTOM_FIELD_PREFIX = "cus_"

?>