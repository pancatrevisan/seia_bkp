<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require '../root.php';
}


require_once ROOTPATH . '/utils/checkUser.php';


checkUser(["admin"], BASE_URL);


require ROOTPATH . '/core/Controller.php';
require_once ROOTPATH . '/utils/DBAccess.php';
/*
if(!isset($_GET["action"])){
    $loc =  ROOTPATH . "/auth/index.php";
    echo "root: " . ROOTPATH;
    header("location:index.php" );
}*/

class AdminController extends Controller
{
    public function __construct(){
        
    }
    public function index($param=[]){
        
    }
    
    public function listUsers($params=[]){
        
    }
    
    public function approveUser($params=[]){
        
    }
}