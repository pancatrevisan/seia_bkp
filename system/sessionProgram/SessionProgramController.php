<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!defined('ROOTPATH')) {
    require '../root.php';
}

/**
 * 
 *  ALTER TABLE `session_program_activity` ADD `next_after_correction_wrong` VARCHAR(100) NOT NULL AFTER `next_after_correction_id`, ADD `next_after_correction_wrong_id` VARCHAR(100) NOT NULL AFTER `next_after_correction_wrong`;
 * 
 */

require_once ROOTPATH . '/utils/checkUser.php';



require_once ROOTPATH . '/core/Controller.php';
require_once ROOTPATH . '/utils/DBAccess.php';
require_once ROOTPATH . '/utils/GetData.php';
require_once ROOTPATH . '/activity/ActivityController.php';
$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/newStimuli.php";

class SessionProgramController extends Controller
{

    public function addToTransferArea($params=[]){
       
        $program_id = $_POST['copy_program'];
        $_SESSION['copy_program'] = $program_id;
        echo "OK";
    }

    public function copyProgram(){
        $program_id = $_SESSION['copy_program'];
        $dest_student = $_POST['dest_student'];

        $SQL = "SELECT * FROM session_program WHERE id='$program_id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            die("Error. SessionProgramController::copyProgram1 ".mysqli_error($db->con));
        }
        $res = mysqli_fetch_assoc($res);




        $date_time = microtime(true);
        $date_time = str_replace('.', '', $date_time); 
        $id="SP_".$date_time;
        $NEW_SESS_ID = $id;
        $student_id = $dest_student;
        $user = $_SESSION['username'];
        $name = $res['name'];

        $SQL = "INSERT INTO session_program(id,student_id,name,owner_id) VALUES('$id','$student_id','$name','$user')";
        $db = new DBAccess();
        if(!$db->query($SQL)){
            die("Error. SessionProgramController::copyProgram2 ".mysqli_error($db->con));
        }


        /////////////////////////////copy activities///////////////////////////////////////
        //get all activities in the existing program
        $SQL = "SELECT * FROM session_program_activity WHERE sessionProgram_id='$program_id'";
        $res = $db->query($SQL);
        if(!$res){
            die("errror. " . mysqli_error($db->con));
        }
        $newIdMap = [];



        $activities = [];
        while($fetch = mysqli_fetch_assoc($res)){
            $date_time = "";
            $id = "";
            do{

            
                $date_time = microtime(true); //date("dmyhis");
                usleep ( 19);
                $date_time = str_replace('.', '', $date_time); 
                $id = "spa_". $date_time;
            }while(in_array($id, $newIdMap));

            $newIdMap[$fetch['id']] = $id;
            if(strlen($fetch['next_on_correct_id']) <=0 || $fetch['next_on_correct_id'] == "undefined" || $fetch['next_on_correct_id'] == "none" )
                $fetch['next_on_correct_id'] = "";
            if(strlen($fetch['next_on_wrong_id']) <=0 || $fetch['next_on_wrong_id'] == "undefined" || $fetch['next_on_wrong_id'] == "none" )
                $fetch['next_on_wrong_id'] = "";
            if(strlen($fetch['next_after_correction_id']) <=0 || $fetch['next_after_correction_id'] == "undefined" || $fetch['next_after_correction_id'] == "none" )
                $fetch['next_after_correction_id'] = "";

            array_push($activities, $fetch);
        }



        ///insert the activities in the new program.
        for($i =0; $i<count($activities); $i++){

                $id = $newIdMap[$activities[$i]['id']];
                $activity_id = $activities[$i]['activity_id'];
                $sessionProgram_id = $NEW_SESS_ID;
                $correction_type = $activities[$i]['correction_type'];
                $correction_value = $activities[$i]['correction_value'];
                $reinforcer_type = $activities[$i]['reinforcer_type'];
                $reinforcer_value = $activities[$i]['reinforcer_value'];
                $next_on_correct = $activities[$i]['next_on_correct'];
                $next_on_wrong = $activities[$i]['next_on_wrong'];
                $position = $activities[$i]['position'];
                $next_after_correction = $activities[$i]['next_after_correction'];
                $next_on_correct_id = "";
                $next_on_wrong_id = "";
                $next_after_correction_id = "";
                if(strlen($activities[$i]['next_on_correct_id']) >0)
                    $next_on_correct_id = $newIdMap[$activities[$i]['next_on_correct_id']];
                if(strlen($activities[$i]['next_on_wrong_id']) >0)
                    $next_on_wrong_id = $newIdMap[$activities[$i]['next_on_wrong_id']];
                if(strlen($activities[$i]['next_after_correction_id']) >0)
                    $next_after_correction_id = $newIdMap[$activities[$i]['next_after_correction_id']];

            $SQL = "INSERT INTO session_program_activity(id,activity_id,sessionProgram_id,correction_type,correction_value,reinforcer_type,reinforcer_value,next_on_correct,next_on_wrong,position,next_on_correct_id,next_on_wrong_id,next_after_correction, next_after_correction_id)" .
            "VALUES('$id','$activity_id','$sessionProgram_id','$correction_type','$correction_value','$reinforcer_type','$reinforcer_value','$next_on_correct','$next_on_wrong','$position','$next_on_correct_id','$next_on_wrong_id','$next_after_correction', '$next_after_correction_id')";
            if(!$db->query($SQL)){
                die("Error. SessionProgramController::copyProgram3 ".mysqli_error($db->con));
            }
        }

        echo "OK";



    }

    public function __construct($params=[])
    {
        parent::__construct();
        if(!isset($params['newUser']))
            checkUser(["professional", "admin","tutor"], BASE_URL);
    }

    public function setSessionFollowActivityOrder($params=[]){
        $id = $_POST['session_program_id'];
        $follow_order = $_POST['order'];

        $SQL ="UPDATE session_program SET follow_activity_order='$follow_order' WHERE id='$id'";
        $db = new DBAccess();
        if(!$db->query($SQL)){
            die(mysqli_error(($db->con)));
        }
        echo $SQL;
    }

    public function runCurriculum($params=[]){

        $data = [];
        $student_id = $params['studentId'];
        
        if(isset($params['athena'])){
            
            $data['athena'] = $params["athena"];
        }
        else{
            $data['athena'] = 'false';
        }
        
        $db = new DBAccess();

        
    

        $SQL = "SELECT * FROM student WHERE id='$student_id'";
        
        $res = $db->query($SQL);
        if(!$res){
            die(mysqli_error($db->con));
        }
        $res = mysqli_fetch_assoc($res);

        $curriculum_id = $res['curriculum_id'];
        

        $SQL = "SELECT * FROM curriculum_program WHERE curriculum_id='$curriculum_id' ORDER BY position ASC";
        $res = $db->query($SQL);
        if(!$res){
            die(mysqli_error($db->con));
        }
        //$res = mysqli_fetch_assoc($res);
        

        

        $program_sessions= [];

        while($fetch = mysqli_fetch_assoc($res)){
            array_push($program_sessions, $fetch['sessionProgram_id']);
        }

        $SQL = "SELECT COUNT(*) AS total FROM curriculum_program WHERE curriculum_id='$curriculum_id' AND student_id='$student_id'";
        $cur_count = $db->query($SQL);
        $cur_count = mysqli_fetch_assoc($cur_count);
        if($cur_count['total']<=0){
            $this->loadView("views/emptySession.php", []);
            die("");
        }


        


        /////////////////////////////////////////////////////
        
       
        isset($params['preview'])? $preview = true: $preview = false;
        isset($params['continue'])?$continueSession = true : $continueSession = false;

        if($data['athena']=='true'){
            $preview = true;
        }
        

        $curriculum = [];
        

        for($i = 0; $i < count($program_sessions); $i++){
            $session_program_id = $program_sessions[$i];
            $sessionprogramTrial_id = "";
            if($continueSession){
                //create new session...
    
            }
            else if(!$preview){
                $sessionprogramTrial_id = $this->newSessionTrial(['student_id'=>$student_id, 'session_program_id'=>$session_program_id]);
            }
            $session_trial = [];
            $SQL = "SELECT * FROM session_program WHERE id='$session_program_id'";
            $res = $db->query($SQL);
            if(!$res){
                die(mysqli_error($db->con));
            }
            $res = mysqli_fetch_assoc($res);

            $session_trial['sessionprogramTrial_id'] = $sessionprogramTrial_id;
            $session_trial['session_program_id'] = $session_program_id;
            $session_trial['student_id'] = $student_id;
            $session_trial['preview'] = $preview;
            $session_trial['session_follow_activity_order'] = $res['follow_activity_order'];
            $session_trial['activities'] = [];

            $SQL = "SELECT spa.id as spa_id, spa.activity_id as spa_activity_id, spa.sessionProgram_id as spa_sessionProgram_id, spa.correction_type as spa_correction_type, " .
            "spa.correction_value as spa_correction_value, spa.reinforcer_type as spa_reinforcer_type, spa.reinforcer_value as spa_reinforcer_value, ". 
            "spa.next_on_correct as spa_next_on_correct, spa.next_on_wrong as spa_next_on_wrong, spa.position as spa_position, ".
            "spa.next_on_correct_id as spa_next_on_correct_id, spa.next_on_wrong_id as spa_next_on_wrong_id, spa.next_after_correction as spa_next_after_correction, spa.next_after_correction_id as spa_next_after_correction_id ".
            "FROM session_program_activity spa WHERE spa.sessionProgram_id='$session_program_id' ORDER BY spa.position";
            $db = new DBAccess();
            $res = $db->query($SQL);
            
            if(!$res){
                die("error. SessionProgramController::runSessionProgram: ".mysqli_error($db->con));
            }

            while($fetch = mysqli_fetch_assoc($res))
            {
                array_push($session_trial['activities'], $fetch);
            }
           
            array_push($curriculum, $session_trial);
        }


        

        $data['instructions'] = [];
        $arr = scandir(ROOTPATH . "/activity/instructions");
        foreach ($arr as $el) {
            if (strcmp($el, '.') != 0 && strcmp($el, '..') != 0) {
                array_push($data['instructions'], "instructions/" . $el);
            }
        }
        $data['session'] = json_encode($curriculum);
        
        $this->loadView("views/runSessionProgram.php", $data);



    }

    public function swapProgramActivityPosition($params){
        $activity1 = $_POST['activity1'];
        $activity2 = $_POST['activity2'];
       /* $position1 = $_POST['position1'];
        $position2 = $_POST['position2'];*/
        $db = new DBAccess();

        $SQL = "SELECT * FROM session_program_activity WHERE id='$activity1'";
        $res = $db->query($SQL);
        if (!$res) {
            die("ERROR");
        }
        $activity1_inDb = mysqli_fetch_assoc($res);

        $SQL = "SELECT * FROM session_program_activity WHERE id='$activity2'";
        $res = $db->query($SQL);
        if (!$res) {
            die("ERROR");
        }
        $activity2_inDb = mysqli_fetch_assoc($res);
        
        $position1 = $activity1_inDb['position'];
        $position2 = $activity2_inDb['position'];

        
        $SQL = "UPDATE session_program_activity SET position='$position2' WHERE id='$activity1'";

        if (!$db->query($SQL)) {
            die("ERROR");
        }

        $SQL = "UPDATE session_program_activity SET position='$position1' WHERE id='$activity2'";

        if (!$db->query($SQL)) {
            die("ERROR");
        }
        $res = [];
        $res['activity1_position'] = $position2;
        $res['activity2_position'] = $position1;

        echo json_encode($res);
    }

    public function updatSessionProgramActivityData($params){
        //print_r($_POST);
        $id              = $_POST['id'];
        $correction_type = $_POST['correction_type'];	
        $correction_value = $_POST['correction_value'];		
        $reinforcer_type = $_POST['reinforcer_type'];		
        $reinforcer_value  = $_POST['reinforcer_value'];		
        $next_on_correct = $_POST['next_on_correct'];		
        $next_on_correct_id = $_POST['next_on_correct_id'];		
        $next_after_correction = $_POST['next_after_correction'];		
        $next_after_correction_id = $_POST['next_after_correction_id'];		
        $next_after_correction_wrong = $_POST['next_after_correction_wrong'];		
        $next_after_correction_wrong_id = $_POST['next_after_correction_wrong_id'];		
        $next_on_wrong = $_POST['next_on_wrong'];	
        $next_on_wrong_id = $_POST['next_on_wrong_id'];	
        $position = $_POST['position'];	

        $SQL = "UPDATE session_program_activity set correction_type='$correction_type', correction_value='$correction_value', reinforcer_type='$reinforcer_type', reinforcer_value='$reinforcer_value', next_on_correct='$next_on_correct', next_on_wrong='$next_on_wrong', position='$position', next_on_wrong_id='$next_on_wrong_id', next_on_correct_id='$next_on_correct_id', next_after_correction='$next_after_correction', next_after_correction_id='$next_after_correction_id', next_after_correction_wrong='$next_after_correction_wrong', next_after_correction_wrong_id='$next_after_correction_wrong_id' "
        ."WHERE id='$id'";
        $db = new DBAccess();

        if(!$db->query($SQL)){
            die("error. SessionProgramController::updateSessionProgramActivityData: " . mysqli_error($db->con));
        }
        echo $SQL;
        //echo $SQL;

    }

    public function newSessionTrial($params=[]){
        $date_time = microtime(true);
        $date_time = str_replace('.', '', $date_time); 
        $id="SPTrial_".$date_time;
        $session_program_id  = $params['session_program_id'];
        $student_id = $params['student_id'];
        $professional_id = $_SESSION['username'];
        
        $date = date("Y-m-d H:i:sa");
        $SQL = "INSERT INTO sessionprogram_trial (id, session_program_id, student_id, professional_id, last_date)" .
        " VALUES('$id','$session_program_id','$student_id','$professional_id','$date')";
        $db = new DBAccess();
        if(!$db->query($SQL)){
            die('SessionProgramController::newSessionTrial ' .mysqli_error($db->con));
        }

        return $id;

    }


    public function updatePreferenceList($params=[]){

        $sessionprogramTrial_id= $_POST['sessionprogramTrial_id'];
        $student_id	= $_POST['student_id'];
        $professional_id= $_SESSION['username'];
        $sessionactivity_id	= $_POST['spa_id'];
        $result	= $_POST['result'];
        $result_data= $_POST['result_data'];
        $start_date	= $_POST['start_date'];
        $end_date	= $_POST['end_date'];

        $SQL = "UPDATE student SET preference_list='$result_data' WHERE id='$student_id'";

        $db = new DBAccess();
        if(!$db->query($SQL)){
            die('Error. SessionProgramController::SaveSesionProgramACtivityTrial ' . mysqli_error($db->con));
        }

        return "SAVE_DAT_SHIT_OK";
    }

    public function saveSessionProgramActivityTrial($program=[]){
        $date_time = microtime(true);
        $date_time = str_replace('.', '', $date_time); 
        $id="SPATrial_".$date_time;

        $sessionprogramTrial_id= $_POST['sessionprogramTrial_id'];
        $student_id	= $_POST['student_id'];
        $professional_id= $_SESSION['username'];
        $sessionactivity_id	= $_POST['spa_id'];
        $result	= $_POST['result'];
        $result_data= $_POST['result_data'];
        $start_date	= $_POST['start_date'];
        $end_date	= $_POST['end_date'];

        $SQL = "INSERT INTO sessionprogram_activity_trial(id, sessionprogramTrial_id, student_id, professional_id, sessionactivity_id, result, " .
            "result_data, start_date, end_date) VALUES('$id', '$sessionprogramTrial_id', '$student_id', '$professional_id', '$sessionactivity_id', '$result', " .
            "'$result_data', '$start_date', '$end_date')";

        $db = new DBAccess();
        if(!$db->query($SQL)){
            die('Error. SessionProgramController::SaveSesionProgramACtivityTrial ' . mysqli_error($db->con));
        }

        return "OK";

    }

    public function runSessionProgram($params=[]){
        $session_program_id = $params['session_id'];
        $student_id = $params['student_id'];
        isset($params['preview'])? $preview = true: $preview = false;
        isset($params['continue'])?$continueSession = true : $continueSession = false;
        if(isset($params['athena'])){
            
            $data['athena'] = $params["athena"];
        }
        else{
            $data['athena'] = 'false';
        }
        if($data['athena']=='true'){
            $preview = TRUE;
        }
        
        $sessionprogramTrial_id = "";
        if($continueSession){
            //create new session...

        }
        else if(!$preview){
            $sessionprogramTrial_id = $this->newSessionTrial(['student_id'=>$student_id, 'session_program_id'=>$session_program_id]);
        }
        $session_trial = [];
        
        $db = new DBAccess();
        $SQL = "SELECT * FROM session_program WHERE id='$session_program_id'";
        $res = $db->query($SQL);
        if(!$res){
            die(mysqli_error($db->con));
        }
        $res = mysqli_fetch_assoc($res);

        $program_name = $res['name'];
        $session_trial['sessionprogramTrial_id'] = $sessionprogramTrial_id;
        $session_trial['session_program_id'] = $session_program_id;
        $session_trial['student_id'] = $student_id;
        $session_trial['preview'] = $preview;
        $session_trial['session_follow_activity_order'] = $res['follow_activity_order'];
        $session_trial['activities'] = [];

        $SQL = "SELECT spa.id as spa_id, spa.activity_id as spa_activity_id, spa.sessionProgram_id as spa_sessionProgram_id, spa.correction_type as spa_correction_type, " .
        "spa.correction_value as spa_correction_value, spa.reinforcer_type as spa_reinforcer_type, spa.reinforcer_value as spa_reinforcer_value, ". 
        "spa.next_on_correct as spa_next_on_correct, spa.next_on_wrong as spa_next_on_wrong, spa.position as spa_position, ".
        "spa.next_on_correct_id as spa_next_on_correct_id, spa.next_on_wrong_id as spa_next_on_wrong_id, spa.next_after_correction as spa_next_after_correction, spa.next_after_correction_id as spa_next_after_correction_id, spa.next_after_correction_wrong_id as spa_next_after_correction_wrong_id ".
        "FROM session_program_activity spa WHERE spa.sessionProgram_id='$session_program_id' ORDER BY spa.position";
        $db = new DBAccess();
        $res = $db->query($SQL);
        
        if(!$res){
            die("error. SessionProgramController::runSessionProgram: ".mysqli_error($db->con));
        }

        if(mysqli_num_rows($res)<=0){
            $data['program_name']=$program_name;
            $this->loadView("views/emptyProgram.php", $data);    
            die("");
        }

        while($fetch = mysqli_fetch_assoc($res))
        {
            array_push($session_trial['activities'], $fetch);
        }

        $data = [];

        $data['instructions'] = [];
        $arr = scandir(ROOTPATH . "/activity/instructions");
        foreach ($arr as $el) {
            if (strcmp($el, '.') != 0 && strcmp($el, '..') != 0) {
                array_push($data['instructions'], "instructions/" . $el);
            }
        }   
        $curriculum = [];
        array_push($curriculum, $session_trial);
        $data['session'] = json_encode($curriculum);
        
        $this->loadView("views/runSessionProgram.php", $data);

        



    }

    public function removeActivityFromSessionProgram($params=[]){

        $session_program_id = $_POST['sessionId'];
        $removeId = $_POST['removeId'];
        //$activities = json_decode($_POST['activities']);


        $db = new DBAccess();

        $SQL = "SELECT * FROM session_program_activity WHERE id='$removeId' AND sessionProgram_id='$session_program_id'";
        $res = $db->query($SQL);
        if (!$res) {
            die("SesionProgramController::removeActivityFromSessionProgram 491 " . mysqli_error(($db->con)));
        }
        $res = mysqli_fetch_assoc($res);
        $REMOVE_POSITION = $res['position'];

        $positions_to_update = [];
        $SQL = "SELECT * FROM session_program_activity WHERE sessionProgram_id='$session_program_id' AND position>'$REMOVE_POSITION' ";
        $res = $db->query($SQL);
        if (!$res) {
            die("SesionProgramController::removeActivityFromSessionProgram 500 " . mysqli_error(($db->con)));
        }
        while($fetch = mysqli_fetch_assoc($res)){
            $positions_to_update[$fetch['id']] = $fetch['position'] -1;
        }


        //REMOVE
        $SQL = "DELETE FROM session_program_activity WHERE id='$removeId' AND sessionProgram_id='$session_program_id'";
        if (!$db->query($SQL)) {
            die("SesionProgramController::removeActivityFromSessionProgram 510 " . mysqli_error(($db->con)));
        }

        //UPDATE in DATABASE.
        foreach($positions_to_update as $act_id => $new_pos){
            $SQL = "UPDATE session_program_activity SET position='$new_pos' WHERE id='$act_id' AND sessionProgram_id='$session_program_id'";
            if(!$db->query($SQL)){
                die("Error. SessionProgramController::removeActivityFromSessionProgram:  517" . mysqli_error($db->con));
            }
        }
        


        
        
       /* foreach ($activities as $key => $value) {
            
            $SQL = "UPDATE session_program_activity SET position='$value' WHERE id='$key' AND sessionProgram_id='$session_program_id'";
            
            if (!$db->query($SQL)) {
                die("ERROR " . mysqli_error(($db->con)));
            }
        }*/
        echo json_encode($positions_to_update);


        

    }
    public function addActivityToSessionProgram($params=[]){
        $session_program_id = $_POST['sessionProgram_id'];
        $activity_id        = $_POST['activity_id'];
       
        //$position           = $_POST['position'];
        $date_time = microtime(true); //date("dmyhis");
        $date_time = str_replace('.', '', $date_time); 
        $id = "spa_". $date_time;
        isset($_POST['return_data'])?$return_data = $_POST['return_data']:$return_data = false;
        isset($_POST['json'])?$json=$_POST['json']:$json=false;
       
        $db  = new DBAccess();
        

        $SQL = "SELECT COUNT(*) AS total FROM session_program_activity WHERE sessionProgram_id='$session_program_id'";
        $res = $db->query($SQL); 
        if(!$res){
            die("error. SessionProgramController::addActivityToSessionProgram ".mysqli_error($db->con));
        }
        $res = mysqli_fetch_assoc($res);
        $NEW_POSITION = $res['total'];



        $SQL = "INSERT INTO session_program_activity(id,activity_id,sessionProgram_id,position) ".
        " VALUES('$id','$activity_id','$session_program_id',$NEW_POSITION)";

        //$db  = new DBAccess();
        if(!$db->query($SQL)){
            die("error. SessionProgramController::addActivityToSessionProgram ".mysqli_error($db->con));
        }

        $SQL = "SELECT spa.id as spa_id, spa.sessionProgram_id as spa_sessionProgram_id, spa.activity_id as spa_activity_id, spa.correction_type as spa_correction_type, spa.correction_value as spa_correction_value,spa.reinforcer_type as spa_reinforcer_type,spa.reinforcer_value as spa_reinforcer_value,".
        "spa.next_on_correct as spa_next_on_correct, spa.next_on_correct_id as spa_next_on_correct_id,  spa.next_on_wrong as spa_next_on_wrong, spa.next_on_wrong_id as spa_next_on_wrong_id,  spa.position as spa_position, spa.next_after_correction as spa_next_after_correction, spa.next_after_correction_id as spa_next_after_correction_id, a.name as a_name, a.id as a_id FROM session_program_activity spa JOIN activity a ON ".
        "spa.activity_id=a.id WHERE spa.id='$id'";

        $res  = $db->query($SQL);
        if(!$res){
            die("error. SessionProgramController::addActivityToSessionProgram ".mysqli_error($db->con));
        }
        
        $res = mysqli_fetch_assoc($res);
  
        
        $spa = [];

        require_once ROOTPATH . '/activity/ActivityController.php';
        $ac = new ActivityController();
         
        $spa['id'] = $res['spa_id'];
        $spa['name'] = $res['a_name'];
        $spa['thumbnail'] = $ac->getThumbnail(['id'=>$res['a_id']]);;
        $spa['activity_id'] = $res['a_id'];
        $spa['sessionProgram_id'] = $res['spa_sessionProgram_id'];
        $spa['correction_type'] = $res['spa_correction_type'];
        $spa['correction_value'] = $res['spa_correction_value'];
        $spa['reinforcer_type'] = $res['spa_reinforcer_type'];
        $spa['reinforcer_value'] = $res['spa_reinforcer_value'];
        $spa['next_on_correct'] = $res['spa_next_on_correct'];
        $spa['next_on_wrong'] = $res['spa_next_on_wrong'];
        $spa['position'] = $res['spa_position'];
        
        if($return_data){
            if($json){
                
                echo json_encode($spa);
            }
            else{
                return $spa;
            }

        }
    }


    private function fixProgramActivityOrder($params=[]){
        $session_program_id = $params['session_program_id'];
        $SQL = "SELECT * FROM session_program_activity WHERE sessionProgram_id='$session_program_id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            die("Error. SessionProgramController:: fixProgramActivityOrder: " . mysqli_error($db->con));
        }
        $to_fix = [];
        $counter = 0;
        while($fetch = mysqli_fetch_assoc($res)){
            $to_fix[$fetch['id']] = $counter;
            $counter = $counter + 1;
        }

        foreach($to_fix as $act_id => $act_pos){
            $SQL = "UPDATE session_program_activity SET position='$act_pos' WHERE id='$act_id' ";
            
            if(!$db->query($SQL)){
                die("Error. SessionProgramController:: fixProgramActivityOrder: " . mysqli_error($db->con));
            }
        }


    }

    public function index($params = [])
    {
        $data = [];

        $data['page_title'] = "Programação de Sessões";
        isset($params['query']) ? $data['query'] = $params['query'] : $data['query'] = "";
        isset($params['page']) ? $data['page'] = $params['page'] : $data['page'] = 0;
        $student = $params['studentId'];
        
        $data['student_id'] = $student;
        if(isset($params['athena'])){
            
            $data['athena'] = $params["athena"];
        }
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/mainView.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }


    public function editFullSession($params=[]){
        
       
        global $lang;
        $data = [];
        $data['curriculum_id'] = $params['id'];
        isset($params['athena'])? $data['athena']=$params['athena']:$data['athena'] = 'false';
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/editTHEsession.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
        
    }

    public function loadSessionProgram($params=[]){
        $session_program_id = $_POST['session_id'];
        $owner_id = $_SESSION['username'];
        $SQL = "SELECT * FROM session_program WHERE id='$session_program_id'";// AND owner_id='$owner_id'";

        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            die("error. SessionProgramController::loadSessionProgram ".mysqli_error($db->con));

        }
        

        $sess = mysqli_fetch_assoc($res);
        $sess['activities'] = [];
        $SQL = "SELECT spa.id as id, spa.activity_id as spa_aid, spa.sessionProgram_id as spa_spid, spa.correction_type as spa_correction_type, " .
        "spa.correction_value as spa_correction_value, spa.reinforcer_type as spa_reinforcer_type, spa.reinforcer_value as spa_reinforcer_value, " .
        "spa.next_on_correct as spa_next_on_correct, spa.next_on_correct_id as spa_next_on_correct_id, spa.next_on_wrong as spa_next_on_wrong, spa.next_on_wrong_id as spa_next_on_wrong_id,  spa.position as position, spa.next_after_correction as spa_next_after_correction, spa.next_after_correction_id as spa_next_after_correction_id, spa.next_after_correction_wrong as spa_next_after_correction_wrong,spa.	next_after_correction_wrong_id as 	spa_next_after_correction_wrong_id, a.id as a_id, a.name as name " .
        "  FROM session_program_activity spa JOIN activity a ON spa.activity_id=a.id WHERE sessionProgram_id='$session_program_id' ORDER BY spa.position";
        
        require_once ROOTPATH . '/activity/ActivityController.php';
        $ac = new ActivityController();
        
        $res = $db->query($SQL);
        if(!$res){
            die("error . SessionProgramController::loadSessionProgram: ".mysqli_error($db->con));
        }
        while($a = mysqli_fetch_assoc($res)){
            $a['thumbnail'] = $ac->getThumbnail(['id'=>$a['a_id']]);;
            
            $correction_id = $a['spa_reinforcer_value'];
            $SQL = "SELECT name from activity WHERE id= '$correction_id'";
            $n = $db->query($SQL);
            $n = mysqli_fetch_assoc($n);
            if($n)
                $a['reinforcer_name'] = $n['name'];
            else
                    $a['reinforcer_name'] = " ";
            array_push($sess['activities'],$a);
        }
        $pos = [];

        for($i = 0; $i < count($sess['activities']); $i++){
            array_push($pos, $sess['activities'][$i]['position']);
        }
        $count_val = array_count_values ( $pos );
        //print_r($count_val);
        //print_r($pos);
        $sess['count_pos']  = count($pos);
        $sess['count_val']  = count($count_val);
        if(count($pos) > count($count_val) ){
           
            $sess['NEED_TO_FIX_ORDER'] = "Precisa sim ";
            $this->fixProgramActivityOrder(["session_program_id"=>$session_program_id]);
            $this->loadSessionProgram([]);
        }
        else{
            echo json_encode($sess);    
        }
        

    }
    public function updateSessionProgramName($params=[]){
        $name = $_POST['sessionName'];
        $id   = $_POST['sessionId'];

        //echo "update session $id name: $name";

        $SQL = "UPDATE session_program SET name='$name' WHERE id='$id'";
        $db= new DBAccess();
        if(!$db->query($SQL)){
            die("error. sessionProgramController::updateSessionProgramName " . mysqli_error($db->con));
        }
        echo "OK";
    }
    public function newSessionProgram($params=[]){

        $date_time = microtime(true);
        $date_time = str_replace('.', '', $date_time); 
        $id="SP_".$date_time;
        $student_id = $params['student_id'];
        $user = $_SESSION['username'];
        $SQL = "INSERT INTO session_program(id,student_id,name,owner_id) VALUES('$id','$student_id','','$user')";
        $db = new DBAccess();
        if(!$db->query($SQL)){
            die("Error. SessionProgramController::newSessionProgram ".mysqli_error($db->con));
        }
        header("location:index.php?action=editSessionProgram&sessionId=".$id);
        //$this->editSessionProgram(['sessionId'=>]);
    }

    public function removeSessionProgram($params=[]){

        $user_id = $_SESSION['username'];
        $session_program_id = $_POST['session_id'];

        $SQL = "UPDATE session_program SET active=FALSE WHERE id='$session_program_id'";
        $db = new DBAccess();
        if(!$db->query($SQL)){
            die("Error. SessionProgramController::removeSessionProgram. " . mysqli_error($db->con));
        }
        echo "OK";
    }
    public function editSessionProgram($params=[]){

        $id = $params['sessionId'];
        $data=[];
        $data['sessionId'] = $id;

        $db = new DBAccess();
        $SQL = "SELECT * FROM session_program WHERE id='$id'";
        $res = $db->query($SQL);
        if(!$res){
            die('Error. SessionProgramController::editSessionProgram '.mysqli_error($db->con));
        }
        $res = mysqli_fetch_assoc($res);
        $data['student_id'] = $res['student_id'];
        $data['follow_activity_order'] = $res['follow_activity_order'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/editSessionProgram.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function removeSessionFromCurriculum($params=[]){
        $session_program_id = $_POST['curriculum_id'];
        $removeId = $_POST['removeId'];
        $activities = json_decode($_POST['sessions']);


        $db = new DBAccess();

        $SQL = "DELETE FROM curriculum_program WHERE id='$removeId' AND curriculum_id='$session_program_id'";
        if (!$db->query($SQL)) {
            die("ERROR " . mysqli_error(($db->con)));
        }
        
        foreach ($activities as $key => $value) {    
            $SQL = "UPDATE curriculum_program SET position='$value' WHERE id='$key' AND curriculum_id='$session_program_id'";
            
            if (!$db->query($SQL)) {
                die("ERROR " . mysqli_error(($db->con)));
            }
        }
        echo "OK";
    }
   
    public function swapCurriculumProgramPosition($params=[]){
        $activity1 = $_POST['activity1'];
        $activity2 = $_POST['activity2'];
        $position1 = $_POST['position1'];
        $position2 = $_POST['position2'];
        $db = new DBAccess();
        $SQL = "UPDATE curriculum_program SET position='$position2' WHERE id='$activity1'";

        if (!$db->query($SQL)) {
            die("ERROR");
        }

        $SQL = "UPDATE curriculum_program SET position='$position1' WHERE id='$activity2'";

        if (!$db->query($SQL)) {
            die("ERROR");
        }
        echo "OK";
    }


    public function loadCurriculum($params=[]){
        $curriculum_id = $_POST['curriculum_id'];
        $SQL ="SELECT * FROM curriculum_program WHERE curriculum_id='$curriculum_id' ORDER BY position";
        $db = new DBAccess();

        $res =$db->query($SQL);
        if(!$res) {
            die(mysqli_error($db->con));
        }
        $ret = [];
        while($fetch = mysqli_fetch_assoc($res)){
            $sessionProgram_id = $fetch['sessionProgram_id'];
            $SQL ="SELECT * FROM session_program WHERE id='$sessionProgram_id'";
            $sp = $db->query($SQL);
            $sp = mysqli_fetch_assoc($sp);
            $fetch["name"] = $sp['name'];
            $fetch['date'] = $sp["date"];
            array_push($ret, $fetch);
        }
        echo json_encode($ret);
    }

    public function addSessionToProgram($params=[]){
        
        $date_time = microtime(true); //date("dmyhis");
        $date_time = str_replace('.', '', $date_time); 
        $id = "progSess_" . $date_time;

        $session_id = $_POST['session_id'];
        $curriculum_id = $_POST['curriculum_id'];
        $position = $_POST['position'];
        $student_id = $_POST['student_id'];

        $SQL = "INSERT INTO curriculum_program (id, sessionProgram_id, curriculum_id, student_id, position) ".
        " VALUES('$id', '$session_id','$curriculum_id', '$student_id', $position) ";

        $db= new DBAccess();

        if(!$db->query($SQL)){
            die("error. " . mysqli_error($db->con));
        }

        $SQL = "SELECT * FROM session_program WHERE id='$session_id'";

        $res = $db->query($SQL);

        if(!$res){
            die("error. " . mysqli_error($db->con));
            
        }

        $res = mysqli_fetch_assoc($res);

        $ret = [
            "id" => $id,
            "session_id"=>$session_id,
            "curriculum_id"=>$curriculum_id,
            "position"=>$position,
            "student_id"=>$student_id,
            "name" => $res['name'],
            "date" => $res["date"]
        ];

        echo json_encode($ret);

    } 

    public function getSessionPrograms($params=[]){
        $number_of_results = 12;
        $userName = $_SESSION['username'];
        $query = isset($params['query'])?$query = $params['query']:$query="";
        $offset = isset($params['offset'])?$offset = $params['offset']:$offset = 0;
        $is_json = isset($params['json'])? $is_json = true: $is_json = false;
        $student = $params['studentId'];

        
        $SQL = "SELECT * FROM session_program WHERE active=TRUE  AND student_id = '$student' AND (name LIKE '%$query') ORDER BY date "; // ;

        if(isset($params['query'])){
            $SQL = $SQL . " LIMIT $number_of_results OFFSET $offset";
        }

        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            die("Error. SessionProgramController::getPrograms ".mysqli_error($db->con));
        }
        $json = [];
        while($fetch = mysqli_fetch_assoc($res)){
            array_push($json,$fetch);
        }
        if($is_json){
            echo json_encode($json);
        }
        else{
            return $json;
        }
    }
}
