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

class ProfessionalController extends Controller {

    public function __construct($params = []) {
        parent::__construct();
        if(!isset($params['newUser'])){
            
            checkUser(["professional","admin"], BASE_URL);
        }
    }
    public function changeAvatar($params = []){
        //echo 'change avatar :D';
        
       // die('okay');
        
        $user_id = $_SESSION['username'];
        
        if(isset($_FILES['stimuli_file'])){
            $origin = $_FILES['stimuli_file']['tmp_name'];
            
            $img = null;
            
            if (exif_imagetype($origin) == IMAGETYPE_JPEG){ // jpeg
                $img = imagecreatefromjpeg ( $origin);
            }
            else if (exif_imagetype($origin) == IMAGETYPE_GIF){ // gif
                $img = imagecreatefromgif ( $origin);
            }
            else if (exif_imagetype($origin) == IMAGETYPE_BMP){ // bmp
                $img = imagecreatefrombmp ( $origin);
            }
            else if (exif_imagetype($origin) == IMAGETYPE_PNG){ // png
                $img = imagecreatefrompng ( $origin);
            }
            else{
                die('error creating image.');
            }
            
            if($img == null){
                die('error creating image.');
            }
            
            $path = ROOTPATH . "/data/user/".$user_id."/avatar.png";
            
            imagepng($img,$path,0,PNG_ALL_FILTERS );
            
            echo "AVATAR_SWAP";
        }
        
        
    }
    public function profile($params=[]){
        $data = [];
        
        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/profile.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }
    public function index($params = []) {
        $data = [];
        
        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/main.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }
    
    public function student($params=[]){
        $data = [];
        
        isset($_POST['query'])?$data['query'] = $_POST['query']: $data['query']="";
        isset($params['page'])?$data['page']=$params['page']:$data['page']=1;
        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/student.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function addExistingTutorship($params=[]){
        $params = ["professional_id"=>$_POST['professional_id'], 'student_id'=>$_POST['student_id']];

        echo $this->addStudentTutorship($params);
    }

    public function addStudentTutorship($params=[]){
        $db = new DBAccess();
        
        $date_time = date("dmyhis");
        $tutorship_id = "tutorship" . $date_time;
        $professional_id = $params['professional_id'];
        $student_id = $params['student_id'];

        $SQL = "SELECT COUNT(*) as total FROM student_tutorship WHERE student_id='$student_id' AND professional_id='$professional_id'";
        $res = $db->query($SQL);
        if(!$res){
            die(mysqli_error($db->con));
        }
        $res = mysqli_fetch_assoc($res);
        if($res['total']>0){
            echo "ALREADY_EXISTS";
            return;
        }


        $SQL= "INSERT INTO student_tutorship(id,professional_id,student_id) ".
        "VALUES('$tutorship_id','$professional_id', '$student_id')";

        if(!$db->query($SQL)){
            die('error');
        }
        if(isset($params['json'])){
            echo "OK";
        }
        else{
            return "OK";
        }
            
    }



    public function setPerformedTutorial($params=[]){
        $user_id = $_SESSION['username'];
        $SQL = "UPDATE user SET tuto_finished=TRUE WHERE username='$user_id'";
        $db = new DBAccess();
        $_SESSION['tuto_finished'] = "1";
        if(!$db->query($SQL)){
            die("Error. " . mysqli_error($db->con));
        }


    }
    public function newStudentFromData($params=[]){
        
        $date_time = date("dmyhis");
        $student_id = "student" . $date_time;
        $student_name = $params['studentName'];
        $student_birthday = $params['birthday'];
        $student_city = $params['city'];
        $student_state = $params['state'];
        $student_sex = $params['sex'];
        $student_medication = $params['medication'];
        
        
        $date_time = date("dmyhis");
        $tutorship_id = "tutorship" . $date_time;
        
        $professional_id = $params['username'];
        
        $db = new DBAccess();
        
        $SQL = "INSERT INTO student(id, name, birthday,sex, city, state, medication) " .
                "VALUES('$student_id','$student_name','$student_birthday','$student_sex','$student_city','$student_state','$student_medication')";
        if(!$db->query($SQL)){
            die('error');
        }
        
        $SQL= "INSERT INTO student_tutorship(id,professional_id,student_id) ".
                "VALUES('$tutorship_id','$professional_id', '$student_id')";
        
        if(!$db->query($SQL)){
            die('error');
        }
        
        $student_dir = ROOTPATH . "/data/student/$student_id";
        if(!mkdir($student_dir)){
            die('Error creating directory');
        }
        
        if(strcmp($student_sex, 'male') == 0)
        {
            $src = ROOTPATH . "/data/pub/avatars/boy.png";
            $dst = ROOTPATH . "/data/student/$student_id/avatar.png";
            copy($src,$dst);
        }
        else if(strcmp($student_sex, 'female') == 0)
        {
            $src = ROOTPATH . "/data/pub/avatars/girl.png";
            $dst = ROOTPATH . "/data/student/$student_id/avatar.png";
            copy($src,$dst);
        }

        //id	student_id	name	description	category	active

        $curr_id = 'curr_' . $student_id;
        $aval_id = 'aval_' . $student_id;


        $SQL = "INSERT INTO curriculum(id,student_id, active) VALUES('$curr_id','$student_id',1)";
        
        if(!$db->query($SQL)){
            die("error. ProfessionalCOntroller::newStudent ".mysqli_error(($db->con)));
        }

        $SQL = "INSERT INTO curriculum(id,student_id, active) VALUES('$aval_id','$student_id',1)";
        if(!$db->query($SQL)){
            die("error. ProfessionalCOntroller::newStudent ".mysqli_error(($db->con)));
        }
       /* $pc = new ProgramController();
        $curr_id = $pc->addNew(['student'=>$student_id]);
        $aval_id = $pc->addNew(['student'=>$student_id,'aval'=>'aval']);*/
        
        $SQL = "UPDATE student SET curriculum_id='$curr_id', evaluation_id='$aval_id' WHERE id='$student_id'";
        if(!$db->query($SQL)){
            echo "error";
            echo $SQL;
            die('error');
        }
    }

    public function updateStudentData(){
        
        
        
        $student_id = $_POST['student_id'];
        
        $student_name = $_POST['studentName'];
        $student_birthday = $_POST['birthday'];
        $student_city = $_POST['city'];
        $student_state = $_POST['state'];
        $student_sex = $_POST['sex'];
        $student_medication = $_POST['medication'];
        
        
        
        $db = new DBAccess();
        
        $SQL = "UPDATE student SET ".
        "name='$student_name',birthday='$student_birthday',sex='$student_sex',city='$student_city',state='$student_state',medication='$student_medication' WHERE id='$student_id'";
        if(!$db->query($SQL)){
            die('error: ' . mysqli_error($db->con));
        }
        header("location:".BASE_URL."/professional/index.php?action=editStudent&studentId=".$student_id);
    }
    public function editStudentData($params=[]){
        
        $student_id = $params['studentId'];
        $SQL = "SELECT * FROM student WHERE id='$student_id'";

        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            die("Error ProfessionalCOntroller:editStudentData " . mysqli_error($db->con));
        }
        $data = mysqli_fetch_assoc($res);
        $data['sql'] = $SQL;
        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/editStudentData.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }
    
    public function newStudent($params=[]){
        
        $date_time = date("dmyhis");
        
        
        $student_id = "student" . $date_time;
        
        $student_name = $_POST['studentName'];
        $student_birthday = $_POST['birthday'];
        $student_city = $_POST['city'];
        $student_state = $_POST['state'];
        $student_sex = $_POST['sex'];
        $student_medication = $_POST['medication'];
        
        $date_time = date("dmyhis");
        $tutorship_id = "tutorship" . $date_time;
        
        $professional_id = $_SESSION['username'];
        
        $db = new DBAccess();
        
        $SQL = "INSERT INTO student(id, name, birthday,sex, city, state, medication) " .
                "VALUES('$student_id','$student_name','$student_birthday','$student_sex','$student_city','$student_state','$student_medication')";
        if(!$db->query($SQL)){
            die('error');
        }
        
        $SQL= "INSERT INTO student_tutorship(id,professional_id,student_id) ".
                "VALUES('$tutorship_id','$professional_id', '$student_id')";
        
        if(!$db->query($SQL)){
            die('error');
        }
        
        $student_dir = ROOTPATH . "/data/student/$student_id";
        if(!mkdir($student_dir)){
            die('Error creating directory');
        }
        
        if(strcmp($student_sex, 'male') == 0)
        {
            $src = ROOTPATH . "/data/pub/avatars/boy.png";
            $dst = ROOTPATH . "/data/student/$student_id/avatar.png";
            copy($src,$dst);
        }
        else if(strcmp($student_sex, 'female') == 0)
        {
            $src = ROOTPATH . "/data/pub/avatars/girl.png";
            $dst = ROOTPATH . "/data/student/$student_id/avatar.png";
            copy($src,$dst);
        }

        //id	student_id	name	description	category	active

        $curr_id = 'curr_' . $student_id;
        $aval_id = 'aval_' . $student_id;


        $SQL = "INSERT INTO curriculum(id,student_id, active) VALUES('$curr_id','$student_id',1)";
        
        if(!$db->query($SQL)){
            die("error. ProfessionalCOntroller::newStudent ".mysqli_error(($db->con)));
        }

        $SQL = "INSERT INTO curriculum(id,student_id, active) VALUES('$aval_id','$student_id',1)";
        if(!$db->query($SQL)){
            die("error. ProfessionalCOntroller::newStudent ".mysqli_error(($db->con)));
        }
       /* $pc = new ProgramController();
        $curr_id = $pc->addNew(['student'=>$student_id]);
        $aval_id = $pc->addNew(['student'=>$student_id,'aval'=>'aval']);*/
        
        $SQL = "UPDATE student SET curriculum_id='$curr_id', evaluation_id='$aval_id' WHERE id='$student_id'";
        if(!$db->query($SQL)){
            echo "error";
            echo $SQL;
            die('error');
        }
        
       // $pc = new ProgramController();
        
        
        header("location:index.php?action=editStudent&studentId=".$student_id);
    }
    
    public function swapStudentAvatar($params=[]){
        
       
        $s_id = $_POST['student_id'];
        if(isset($_FILES['stimuli_file'])){
            $origin = $_FILES['stimuli_file']['name'];
            $SQL = "UPDATE student SET avatar = '$origin' WHERE id='$s_id'";
            $db = new DBAccess();
            if($db->query($SQL)){
                $url = '/data/student/' . $s_id."/" . $origin;
                move_uploaded_file($_FILES['stimuli_file']['tmp_name'], ROOTPATH . $url);
            }
        }
        
        echo "AVATAR_SWAP";
    }
    
    public function editStudent($params=[]){
        $data['studentId'] = $params['studentId'];
       
        global $lang;
        $data['page_title'] = $lang['page_name'];
        if(isset($params['athena'])){
            
            $data['athena'] = $params["athena"];
        }
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/editStudent.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }
    
    public function showNewStudentForm($params = []) {
        $data = [];
        
        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/newStudent.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }


    public function removeStudent($params=[]){
        $user_id = $_SESSION['username'];
        $student_id = $_POST['student_id'];

        $SQL = "DELETE FROM student_tutorship WHERE professional_id='$user_id' AND student_id='$student_id'";
        $db= new DBAccess();

        
        if(!$db->query($SQL)){
            die("Error. ProfessionalController::removeStudent. " . mysqli_error($ddb->con));
        }
        return "OK";
    }


    /*********************************************************************************************************/
    public function getSessions_json($params=[]){
        $student_id = $params['studentId'];
        $SQL = "";
        if(isset($params['startDate'])){
            $tmp = explode("-",$params['startDate']);

            $s_date = mktime(0,0,0,$tmp[1],$tmp[2],$tmp[0]);
            $s_date = date("Y-m-d H:i:s", $s_date);



            $tmp = explode("-",$params['endDate']);

            $e_date = mktime(0,0,0,$tmp[1],$tmp[2],$tmp[0]);
            $e_date = date("Y-m-d H:i:s", $e_date);

            
            $SQL = "SELECT spt.id as spt_id, spt.session_program_id as spt_session_program_id, spt.student_id as spt_student_id, spt.professional_id as spt_professional_id, spt.last_date as spt_last_date," .
             " sp.name as sp_name FROM sessionprogram_trial spt JOIN session_program sp ON spt.session_program_id=sp.id WHERE spt.student_id='$student_id' AND spt.last_date >='$s_date' AND spt.last_date <= '$e_date'  ORDER BY spt.last_date DESC" ;    
        }
        else{
            $SQL = $SQL = "SELECT spt.id as spt_id, spt.session_program_id as spt_session_program_id, spt.student_id as spt_student_id, spt.professional_id as spt_professional_id, spt.last_date as spt_last_date," .
            " sp.name as sp_name FROM sessionprogram_trial spt JOIN session_program sp ON spt.session_program_id=sp.id WHERE spt.student_id='$student_id' ORDER BY spt.last_date DESC" ;    
            //$SQL = "SELECT * FROM sessionprogram_trial WHERE student_id='$student_id' ORDER BY last_date DESC";
        }
        $programs = array();

        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){

            
            echo "ERROR " . mysqli_error($db->con);
        }
        while($fetch = mysqli_fetch_assoc($res))
        {
            $sessionprogramTrial_id = $fetch['spt_id'];
            $SQL = "SELECT COUNT(*) as total FROM sessionprogram_activity_trial WHERE sessionprogramTrial_id='$sessionprogramTrial_id' ";
            $c_res = $db->query($SQL);
            $c_res = mysqli_fetch_assoc($c_res);
            $c_res = $c_res['total'];

            if($c_res > 0){
                array_push($programs, ($fetch));
            }
                
        }
        echo json_encode($programs);
        
    }

    public function getSession_json($params=[]){
        $student_id = $params['studentId'];
        $session_id = $params['sessionId'];


        $db = new DBAccess();
        $SQL = "SELECT * FROM sessionprogram_trial WHERE id='$session_id'";
        $res = $db->query($SQL);
        $fetch = mysqli_fetch_assoc($res);


        $sess = [];
        $sess['id'] = $fetch['id'];
        $sess['session_program_id'] = $fetch['session_program_id'];
        $sess['student_id'] = $fetch['student_id'];
        $sess['professional_id'] = $fetch['professional_id'];
        $sess['date'] = $fetch['last_date'];

        $sessionprogram_id = $sess['session_program_id'] ;
        $SQL = "SELECT * FROM session_program WHERE id='$sessionprogram_id'";
        $res = $db->query($SQL);
        $fetch = mysqli_fetch_assoc($res);

        $sess['program_name'] = $fetch['name'];

        $sess['trials'] = [];



        $SQL = "SELECT spat.id as spat_id,  spat.sessionprogramTrial_id as spat_sessionprogramTrial_id, spat.student_id as spat_student_id, ".
        "spat.professional_id as spat_professional_id, spat.sessionactivity_id as spat_sessionactivity_id, spat.result as spat_result, spat.result_data as spat_result_data, spat.start_date, ".
        "spat.end_date as spat_end_date, spa.id as spa_id, spa.sessionProgram_id as spa_sessionProgram_id, spa.correction_type as spa_correction_type, " .
        "spa.correction_value as spa_correction_value, spa.reinforcer_type as spa_reinforcer_type, spa.reinforcer_value as spa_reinforcer_value, spa.next_on_correct as spa_next_on_correct," .
        "spa.next_on_wrong as spa_next_on_wrong, spa.position as spa_position, spa.next_on_correct_id as spa_next_on_correct_id, spa.next_on_wrong_id as spa_next_on_wrong_id, ".
        "spa.next_after_correction as spa_next_after_correction, spa.next_after_correction_id as spa_next_after_correction_id, a.id as a_id, a.name as a_name, ".
        "a.difficulty as a_difficulty ".
        "FROM sessionprogram_activity_trial spat JOIN session_program_activity spa ON spat.sessionactivity_id=spa.id JOIN activity a ON spa.activity_id=a.id WHERE spat.sessionprogramTrial_id='$session_id'";


        
       
        $res = $db->query($SQL);
        if(!$res){
            echo "ERROR " . mysqli_error($db->con);
        }

        

        while($fetch = mysqli_fetch_assoc($res))
        {
            
            array_push($sess['trials'], $fetch);

        }
        echo json_encode($sess);
    }

    public function getLastSession_json($params=[]){
        $student_id = $params['studentId'];
        $SQL = "SELECT * FROM sessionprogram_trial WHERE student_id='$student_id'  ORDER BY last_date DESC LIMIT 1";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            echo "ERROR " . mysqli_error($db->con);
        }
        $fetch = mysqli_fetch_assoc($res);
        echo json_encode($fetch);
    }

    public function tableReport($params=[]){
        $data = [];
        $data['type'] = $params['type'];
        $student_id = $params['studentId'];
        $data['studentId'] = $params['studentId'];
        $user = $_SESSION['username'];
        $data['results'] = [];
        if($data['type']=="full_session"){
            
            
        }
        else if($data['type']=="program"){
            $session_id = $params['program_trial_id'];
        
            $SQL = "SELECT spat.id as spat_id,  spat.sessionprogramTrial_id as spat_sessionprogramTrial_id, spat.student_id as spat_student_id, ".
        "spat.professional_id as spat_professional_id, spat.sessionactivity_id as spat_sessionactivity_id, spat.result as spat_result, spat.result_data as spat_result_data, spat.start_date, ".
        "spat.end_date as spat_end_date, spa.id as spa_id, spa.sessionProgram_id as spa_sessionProgram_id, spa.correction_type as spa_correction_type, " .
        "spa.correction_value as spa_correction_value, spa.reinforcer_type as spa_reinforcer_type, spa.reinforcer_value as spa_reinforcer_value, spa.next_on_correct as spa_next_on_correct," .
        "spa.next_on_wrong as spa_next_on_wrong, spa.position as spa_position, spa.next_on_correct_id as spa_next_on_correct_id, spa.next_on_wrong_id as spa_next_on_wrong_id, ".
        "spa.next_after_correction as spa_next_after_correction, spa.next_after_correction_id as spa_next_after_correction_id, a.id as a_id, a.name as a_name, ".
        "a.difficulty as a_difficulty ".
        "FROM sessionprogram_activity_trial spat JOIN session_program_activity spa ON spat.sessionactivity_id=spa.id JOIN activity a ON spa.activity_id=a.id WHERE spat.sessionprogramTrial_id='$session_id' ".
        " ORDER BY spat.end_date DESC";

        }
        else if($data['type']=="activity_trials"){
            $activity_id = $params['activity_id'];
            
            $SQL = "SELECT spat.id as spat_id,  spat.sessionprogramTrial_id as spat_sessionprogramTrial_id, spat.student_id as spat_student_id, ".
        "spat.professional_id as spat_professional_id, spat.student_id as spat_student_id, spat.sessionactivity_id as spat_sessionactivity_id, spat.result as spat_result, spat.result_data as spat_result_data, spat.start_date, ".
        "spat.end_date as spat_end_date, spa.id as spa_id, spa.sessionProgram_id as spa_sessionProgram_id, spa.correction_type as spa_correction_type, " .
        "spa.correction_value as spa_correction_value, spa.reinforcer_type as spa_reinforcer_type, spa.reinforcer_value as spa_reinforcer_value, spa.next_on_correct as spa_next_on_correct," .
        "spa.next_on_wrong as spa_next_on_wrong, spa.position as spa_position, spa.next_on_correct_id as spa_next_on_correct_id, spa.next_on_wrong_id as spa_next_on_wrong_id, ".
        "spa.next_after_correction as spa_next_after_correction, spa.next_after_correction_id as spa_next_after_correction_id, a.id as a_id, a.name as a_name, ".
        "a.difficulty as a_difficulty ".
        "FROM sessionprogram_activity_trial spat JOIN session_program_activity spa ON spat.sessionactivity_id=spa.id JOIN activity a ON spa.activity_id=a.id WHERE a.id='$activity_id' AND spat.student_id='$student_id' ORDER BY spat.end_date DESC";

        }
        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            echo "ERROR " . mysqli_error($db->con);
        }

        
        $Results = [];
        while($fetch = mysqli_fetch_assoc($res))
        {
            //$fetch['spat_result_data'] = '['. substr($fetch['spat_result_data'],1,strlen($fetch['spat_result_data'])-1).']';
            $fetch['spat_result_data'] = json_decode($fetch['spat_result_data'],true);//transforma pra array, pra poder voltar pra json senão da erro.
           
            //$fetch['spat_result_data'] = str_replace('/','\/', $fetch['spat_result_data'] );
            array_push($Results, $fetch);
            

        }
        
        if($data['type']=="activity_trials"){
            if(sizeof($Results)>0){
                $data['report_description'] = $Results[0]['a_name'];        
            }
            
        }
        
       
        $data['results'] = json_encode($Results);
        
        
        
        
        $this->loadView("views/tableReport.php", $data);

       /* $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/sessionReport.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);*/
    }

    public function getProgramReport_json($params=[]){
        $program_id = $params['program_id'];
        $student_id = $params['student_id'];

        $SQL = "SELECT spt.id as spt_id, spt.session_program_id as spt_session_program_id, spt.last_date as spt_last_date, spat.sessionprogramTrial_id as spat_sessionprogramTrial_id, spat.result as spat_result 
        FROM sessionprogram_trial spt JOIN sessionprogram_activity_trial spat ON spt.id=spat.sessionprogramTrial_id  WHERE spt.session_program_id='$program_id'  ORDER BY spt.last_date DESC ";

        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            die("ProfessionalController::getProgramReport_json ".mysqli_error($db->con));
        }
        $ret = [];
        $ret['sql'] = $SQL;
        $ret['num_res'] = mysqli_num_rows($res);
        $ret['programs'] = [];
        while($fetch = mysqli_fetch_assoc($res) ){
            $program = $fetch['spt_id'];
            if(!isset($ret['programs'][$program])){
                $ret['programs'][$program] = [];
                $ret['programs'][$program]['date'] = $fetch['spt_last_date'];
                $ret['programs'][$program]['activities'] = [];
            }


            array_push($ret['programs'][$program]['activities'], $fetch);
        }

        echo json_encode($ret);
        
    }

    public function removeSessionReport($params=[]){
        $sessionId = $_POST['session_id'];

        $SQL = "DELETE FROM sessionprogram_trial WHERE id='$sessionId' ";
        $db = new DBAccess();
        if(!$db->query($SQL)){
            die("ProfessionalController::removeSessionReport " . mysqli_error($db->con));
        }

       

        echo "OK";
    }

    public function sessionReport($params=[]){
        $data = [];
        $data['sessionId'] = $params['sessionId'];
        $data['studentId'] = $params['studentId'];
        $user = $_SESSION['username'];

        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/sessionReport.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function studentReport($params=[]){
        $data = [];
        $data['studentId'] = $params['studentId'];
        $user = $_SESSION['username'];

        global $lang;
        $data['page_title'] = $lang['page_name'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/studentReport.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
        
    }
}
