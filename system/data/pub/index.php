<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

if(!defined('ROOTPATH')){
    require '../root.php';
}

if(isset($_SESSION['username'])){
    if($_SESSION['role']=='professional')
    {
        $url = BASE_URL . "/professional";   
        header("location:$url");
    }
    else if($_SESSION['role']=='admin')
    {
        echo "admin logado";
    }
}
else{
    $url = BASE_URL;
    header("location:$url");
}