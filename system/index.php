<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require './root.php';
}
if(file_exists('./install')){
    echo "Instalar?";
    $url = BASE_URL . "/install";   
    header("location:$url");    
    die(0);
}
if(!isset($_SESSION['username'])){
    $url = BASE_URL . "/auth";   
    header("location:$url"); 
}
if(isset($_SESSION['username'])){
    if($_SESSION['role']=="professional"){
        $url = BASE_URL . "/professional";   
        header("location:$url"); 
    }
    else if($_SESSION['role']=="student"){
        $url = BASE_URL . "/student";   
        header("location:$url");
    }
    else if($_SESSION['role']=="admin"){
        $url = BASE_URL . "/admin";   
        header("location:$url");
    }
}


?>