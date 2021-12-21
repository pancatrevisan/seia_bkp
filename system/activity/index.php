<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

if(!defined('ROOTPATH')){
    require '../root.php';
}

require 'ActivityController.php';

$action = "index";
$data = $_GET;
$keys = array_keys($_GET);
if(isset($_GET["action"])){
    $action = $_GET["action"];
    $keys = array_keys($_GET);
} elseif (isset($_POST["action"])){
    $action = $_POST["action"];
    $keys = array_keys($_POST);
    $data = $_POST;
}

$params = [];
if(count($keys)>1)
{
    $i = 0;
    for($i =0; $i<count($keys); $i++)
    {
        if($keys[$i]!='action')
        {
            $params[$keys[$i]] = $data[$keys[$i]];
        }
    }
}
$a = new ActivityController();
$a->$action($params);
