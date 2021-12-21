<?php

if(!defined('ROOTPATH')){
    define('ROOTPATH', __DIR__);    
}

if(!defined('BASE_URL')){
    $path = "http://" . $_SERVER['SERVER_NAME'] ;
	
	if( strstr($path,"localhost")){
	$path = $path . "/seia/system"; 
	}
	else
	{
		////TODO: REMOVE
		$path = $path . "/seia/system"; 	
	}    
    
    define('BASE_URL',  $path);
}