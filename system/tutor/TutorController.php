<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require './root.php';
}

require ROOTPATH . '/utils/checkUser.php';


require_once ROOTPATH . '/core/Controller.php';
require_once ROOTPATH . '/program/ProgramController.php';
$sel_lang = 'ptb';
require_once ROOTPATH . '/utils/DBAccess.php';

class TutorController extends Controller {

    public function __construct($params = []) {
        parent::__construct();
        if(!isset($params['newUser'])){
            
            checkUser(["professional","admin","tutor"], BASE_URL);
        }
    }

    public function index($params=[]){
        //lists all students that this person 'tutors'
        $data = [];
        //$this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView('views/main.php',$data);
        //  $this->loadView(ROOTPATH . "/ui/footer.php", $data);
        
    }

    
}