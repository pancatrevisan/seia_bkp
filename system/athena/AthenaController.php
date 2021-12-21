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
require ROOTPATH . '/lang/' . $sel_lang . "/professional/all.php";
require_once ROOTPATH . '/utils/DBAccess.php';

class AthenaController extends Controller {

    public function __construct($params = []) {
        parent::__construct();
        if(!isset($params['newUser'])){
            
            checkUser(["professional","admin","athena"], BASE_URL);
        }
    }
    public function index($params = []) {
        $data = [];
        
        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/main.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);

    }

    public function generalStats($params=[]){
        $data = [];
        
        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/stats.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function users($params = []) {
        $data = [];
        
        global $lang;
        $data['page_title'] = $lang['page_name'];
        isset($params['page'])?$data['page']=$params['page']:$data['page']=1;
        isset($params['query'])?$data['query']=$params['query']:$data['query']="";

        
        
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/users.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);

    }

    public function viewUserAccessLog($params = []){
        $user = $params['user'];
        $data['user'] = $user;
        

        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/userAccessLogs.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
       
    }
    public function viewUserActivities($params=[]){


        
        $user = $params['user'];
        $data['user'] = $user;
        isset($params['page'])?$data['page']=$params['page']:$data['page']=1;
        
        //echo "view user activities ";
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/userActivities.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);

    }
    public function viewUserStudents($params=[]){

        
        $user = $params['user'];
        $data['user'] = $user;
        
        isset($params['page'])?$data['page']=$params['page']:$data['page']=1;
        isset($params['query'])?$data['query']=$params['query']:$data['query']="";
        
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/userStudents.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function studentTotalReport($params=[]){
        $student_id = $params['studentId'];
        $data['studentId'] = $student_id;
        isset($params['page'])?$data['page']=$params['page']:$data['page']=1;
        isset($params['query'])?$data['query']=$params['query']:$data['query']="";
        
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/studentTotalReport.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function listUsers_json($params=[]){
        checkUser(["professional","admin","athena"], BASE_URL);
        $SQL = "SELECT * FROM user";
        $db = new DBAccess();

        $res = $db->query($SQL);
        if(!$res){
            die("Error : " . mysqli_error($db->con));
        }
        $result = [];
        while($r = mysqli_fetch_assoc($res)){
            array_push($result, $r);
        }
        
        $js = json_encode($result);

        echo $js;

    }
    
}
