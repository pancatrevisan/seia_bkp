<?php
session_start();
require 'StimuliController.php';

$action = "index";
if(isset($_GET["action"])){
    $action = $_GET["action"];
}
$keys = array_keys($_GET);
$params = [];
if(count($keys)>1)
{
    $i = 0;
    for($i =0; $i<count($keys); $i++)
    {
        if($keys[$i]!='action')
        {
            $params[$keys[$i]] = $_GET[$keys[$i]];
        }
    }
}
$a = new StimuliController();
$a->$action($params);