<?php
  class FileUtil{
    public static function uploadFiles($file,$path){
        if(move_uploaded_file($file['tmp_name'], $path .basename($file['name'])))
        {
            return $file['name'];
        }
        else
        {
            throw new Exception("Error During file upload");
        }
    }
    
    public static function uploadImageFiles($file,$path,$name){
    	
    	$tempName = $file['tmp_name'];
    	if(is_array($tempName)){
    		$tempName = $tempName[0];
    	}
        if(move_uploaded_file($tempName, $path.$name))
        {
            return $name;
        }
        else
        {
            throw new Exception("Error During file upload ".$path);
        }
    }
    
    public static function getAllFilesFromDir($dirPath){
    	$fileNames = array_slice(scandir($dirPath), 2);    	
    	return $fileNames;
    }
    
    public static function deletefile($path){
    	if(file_exists ($path)){
    		unlink($path);
    	}
    }
    
    public static function size_as_kb($size=0) {
    	if($size < 1024) {
    		return $size;
    	} elseif($size < 1048576) {
    		$size_kb = round($size/1024);
    		return $size_kb;
    	} else {
    		$size_mb = round($size/1048576, 1);
    		return "{$size_mb} MB";
    	}
    }
  }
?>
