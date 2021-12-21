<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require './root.php';
}

require ROOTPATH . '/utils/checkUser.php';

checkUser(["professional","admin"], BASE_URL);


require_once ROOTPATH . '/core/Controller.php';
$sel_lang = 'ptb';
require ROOTPATH . '/lang/' . $sel_lang . "/professional/all.php";
require_once ROOTPATH . '/utils/DBAccess.php';


class ErrorController extends Controller {

    public function __construct($params = []) {
        parent::__construct();
    }
    public function index($params = []) {
     
    }
    
    public function insert($params =[]){
        
    }
    
}
