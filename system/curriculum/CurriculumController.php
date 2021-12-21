<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["professional","admin"], BASE_URL);

require_once ROOTPATH . '/core/Controller.php';
require_once ROOTPATH . '/utils/DBAccess.php';
require_once ROOTPATH . '/utils/GetData.php';
$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/newStimuli.php";

class CurriculumController extends Controller {

    public function __construct() {
        parent::__construct();
    }
    
    
    
}
