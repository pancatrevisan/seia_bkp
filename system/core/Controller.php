<?php header('Access-Control-Allow-Origin: *'); ?>
<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

if(!defined('ROOTPATH')){
    require '../root.php';
}
class Controller {
    public function __construct(){
        
    }
    protected function loadView($view, $data){
        ob_start();
        
        require "$view";
        $out = ob_get_clean();
        echo $out;
        
    }
    
}
