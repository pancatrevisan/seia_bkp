<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';



require_once ROOTPATH . '/core/Controller.php';
require_once ROOTPATH . '/utils/DBAccess.php';
require_once ROOTPATH . '/utils/GetData.php';
require_once ROOTPATH . '/activity/ActivityController.php';
$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/newStimuli.php";

class ProgramController extends Controller
{

    public function __construct($params=[])
    {
        parent::__construct();
        if(!isset($params['newUser']))
            checkUser(["professional", "admin","tutor"], BASE_URL);
    }
    public function setAutoGuide($params = [])
    {
        $id = $params['programId'];
        $guide_id = $params['guide'];
        $SQL = "UPDATE program SET guide_id='$guide_id' WHERE id='$id'";
        $db = new DBAccess();
        if (!$db->query($SQL)) {
            die('ERROR programController:setAutoGuide ' . mysqli_error($db->con));
        }
    }
    public function editAutoProgram($params = [])
    {
        $programId = $params['programId'];

        $SQL = "SELECT * FROM program WHERE id='$programId'"; // AS ga INNER JOIN activity AS a ON ga.activity_id=a.id WHERE ga.group_id='$programId' AND a.auto_guide=TRUE";
        echo "<br> $SQL <Br>";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if (!$res) {
            echo 'ERROR programController:editAutoProgram ' . mysqli_error($db->con);
        }
        $fetch = mysqli_fetch_assoc($res);
        $guide_id = $fetch['guide_id'];
        //echo "guide: $guide_id <br>";
        header("location:" . BASE_URL . "/activity/index.php?action=edit&id=$guide_id&programId=$programId");
    }
    public function newAutoProgram($params = [])
    {
        $name = $params['groupName'];
        $cat  = $params['autoCategory'];
        if (!isset($params['programId'])) {
            die("ERROR: set program id at ProgramController::newAutoProgram");
        }
        if ($params['type'] == "MTS") {

            $programId = $params['programId'];

            $group_id = $this->addGroup_noEcho([
                'groupName' => $name,
                'programId' => $programId, 'auto' => 1, 'category' => $cat
            ]);
            echo "<br> new group: $group_id <br>";
            $activityId = "";


            //$this->addActivityToGroup_noEcho(['groupId'=>$group_id,'activityId'=>$activityId,'templateId'=>'pubMatchingtoSample']);
            $activityController = new ActivityController();
            $activityController->newFromTemplate(['groupId' => $group_id, 'auto_guide' => 'TRUE', 'templateId' => 'pubMatchingtoSample']);
            //http://localhost/seia/system/activity/index.php?action=newFromTemplate&templateId=pubMatchingtoSample&groupId=
        }
    }

    public function addGroup_noEcho($params = [])
    {
        $name =         $params['groupName'];
        $programId =    $params['programId'];
        $owner_id = $_SESSION['username'];
        $antecedent = "";
        $consequence = "";
        $behavior = "";
        $category = $params['category'];

        $date_time = date("dmyhis");
        $id = $owner_id . $date_time;
        isset($params['auto']) ? $auto = 'TRUE' : $auto = 'FALSE';
        $db = new DBAccess();

        $SQL = "SELECT COUNT(*) AS total FROM program WHERE curriculumId='$programId'";
        $res = $db->query($SQL);
        if (!$res) {
            die("ERROR");
        }
        $fetch = mysqli_fetch_assoc($res);
        $position = $fetch['total'];

        $SQL = "INSERT INTO program(id, curriculumId, owner_id, name,antecedent, behavior, consequence, position,auto,category) VALUES('$id','$programId', '$owner_id', '$name','$antecedent', '$behavior', '$consequence', $position,$auto,'$category')";


        if (!$db->query($SQL)) {

            die("ERROR");
        }
        return $id;
    }

    public function program_set($params = [])
    {
        $json = json_decode($_POST['keys'], true);
        $g_id = $_POST['id'];
        $user = $_SESSION['username'];

        $SQL = "UPDATE program SET  "; // FROM group_activity 
        foreach ($json as $key => $value) {
            $SQL = $SQL . " $key='$value',";
        }
        $SQL = substr($SQL, 0, -1);
        $SQL = $SQL . "WHERE active=1 AND id='$g_id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        $activities = array();
        if (!$res) {
            echo "ERROR";
            // header("location:index.php");
        }
        echo json_encode('OKAY');
    }


    public function activityGroup_set($params = [])
    {
        $json = json_decode($_POST['keys'], true);
        $g_id = $_POST['id'];
        $user = $_SESSION['username'];

        $SQL = "UPDATE program SET  "; // FROM group_activity 
        foreach ($json as $key => $value) {
            $SQL = $SQL . " $key='$value',";
        }
        $SQL = substr($SQL, 0, -1);
        $SQL = $SQL . "WHERE active=1 AND id='$g_id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        $activities = array();
        if (!$res) {
            echo "ERROR";
            // header("location:index.php");
        }
        echo json_encode('OKAY');
    }

    public function removeActivityFromGroup($params = [])
    {

        $groupId = $_POST['groupId'];
        $removeId = $_POST['removeId'];
        $affected_activities = json_decode($_POST['activities'], true);

        $db = new DBAccess();

        $SQL = "DELETE FROM group_activity WHERE id='$removeId' AND group_id='$groupId'";
        if (!$db->query($SQL)) {
            die("ERROR");
        }

        foreach ($affected_activities as $key => $value) {

            $SQL = "UPDATE group_activity SET position='$value' WHERE id='$key' AND group_id='$groupId'";
            if (!$db->query($SQL)) {
                die("ERROR");
            }
        }
        echo "OK";
    }

    public function swapActivity($params = [])
    {
        $groupId = $_POST['groupId'];
        $activity1 = $_POST['activity1'];
        $activity2 = $_POST['activity2'];
        $position1 = $_POST['position1'];
        $position2 = $_POST['position2'];
        $db = new DBAccess();
        $SQL = "UPDATE group_activity SET position=$position2 WHERE id='$activity1'";

        if (!$db->query($SQL)) {
            die("ERROR");
        }

        $SQL = "UPDATE group_activity SET position=$position1 WHERE id='$activity2'";

        if (!$db->query($SQL)) {
            die("ERROR");
        }
        echo "OK";
    }


    public function addGroup($params = [])
    {
        $name =         $_POST['groupName'];
        $programId =    $_POST['programId'];
        $owner_id = $_SESSION['username'];
        $antecedent     = $_POST["groupAntecedent"];
        $behavior       = $_POST["groupBehavior"];
        $consequence    = $_POST["groupConsequence"];
        $category       = $_POST["groupCategory"];
        $date_time = date("dmyhis");
        $id = $owner_id . $date_time;

        $db = new DBAccess();

        $SQL = "SELECT COUNT(*) AS total FROM program WHERE curriculumId='$programId'";
        $res = $db->query($SQL);
        if (!$res) {
            die("ERROR");
        }
        $fetch = mysqli_fetch_assoc($res);
        $position = $fetch['total'];

        $SQL = "INSERT INTO program(id, curriculumId, owner_id, name,antecedent, behavior, consequence, position,category) VALUES('$id','$programId', '$owner_id', '$name','$antecedent', '$behavior', '$consequence', $position,'$category')";



        if (!$db->query($SQL)) {

            die("ERROR");
        }
        echo $id;
    }

    public function resetProgram($params = [])
    {
        $programId = $params['programId'];
        $SQL = "SELECT *FROM group_activity WHERE group_id='$programId'";

        $db = new DBAccess();
        $res = $db->query($SQL);
        if ($res) {
            while ($fetch = mysqli_fetch_assoc($res)) {
                $ga_id = $fetch['id'];
                $a_id = $fetch['activity_id'];
                $SQL = "DELETE FROM group_activity WHERE id='$ga_id'";

                if (!$db->query($SQL)) {
                    die("Reset Program ERROR. " . mysqli_error($db->con));
                }

                $SQL = "UPDATE activity SET active=0 WHERE id='$a_id'";
                if (!$db->query($SQL)) {
                    die("ERROR. " . mysqli_error($db->con));
                }
            }
        }
        if (isset($params['json'])) {
            echo "OK";
        }
    }

    public function addActivityToGroup_noEcho($params = [])
    {
        $owner_id = $_SESSION['username'];
        $date_time = date("dmyhis");
        $id = $owner_id . $date_time;

        $group_id = $params['groupId'];
        $activity_id = $params['activityId'];

        $db = new DBAccess();


        $SQL = "SELECT COUNT(*) AS total FROM group_activity WHERE group_id='$group_id'";
        $res = $db->query($SQL);
        $res = mysqli_fetch_assoc($res);

        $position = $res['total'] + 1;

        $id = $id . $res['total'];


        $SQL = "INSERT INTO group_activity(id,group_id,owner_id,activity_id,active,position) "
            . "VALUES('$id','$group_id','$owner_id','$activity_id',1,$position)";


        if (!$db->query($SQL)) {
            return ("ERROR");
        }
    }

    public function addActivityToGroup($params = [])
    {
        $owner_id = $_SESSION['username'];
        $date_time = date("dmyhis");
        $id = $owner_id . $date_time;
        $position = $_POST['position'];

        $group_id = $_POST['groupId'];
        $activity_id = $_POST['activityId'];

        $SQL = "INSERT INTO group_activity(id,group_id,owner_id,activity_id,active,position) "
            . "VALUES('$id','$group_id','$owner_id','$activity_id',1,$position)";

        $db = new DBAccess();
        if (!$db->query($SQL)) {
            echo ("ERROR");
        }
        $SQL = "SELECT * FROM activity WHERE id='$activity_id'";


        $res = $db->query($SQL);
        if (!$res) {
            echo "ERROR";
            // header("location:index.php");
        }

        $fetch = mysqli_fetch_assoc($res);

        $fetch['position'] = $position;
        $fetch['group_activity_id'] = $id;

        echo json_encode($fetch);
    }

    public function removeGroup($params = [])
    {
        $id = $_POST['groupId'];
        $owner_id = $_SESSION['username'];


        $SQL = "DELETE FROM program WHERE id='$id' AND owner_id='$owner_id'";
        //

        $db = new DBAccess();
        if (!$db->query($SQL)) {
            die("ERROR");
        }

        $SQL = "DELETE FROM group_activity WHERE group_id='$id'";
        if (!$db->query($SQL)) {
            die("ERROR");
        }

        echo $id;
    }

    public function index($params = [])
    {

        header("location:" . BASE_URL);
        $data = [];

        $data['page_title'] = "Programas de Ensino";
        isset($params['query']) ? $data['query'] = $params['query'] : $data['query'] = "";
        isset($params['page']) ? $data['page'] = $params['page'] : $data['page'] = 0;


        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/mainView.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function filter_form($params = [])
    {
    }

    public function groupActivity_set($params = [])
    {


        $json = json_decode($_POST['keys'], true);
        $act_id = $_POST['id'];
        $user = $_SESSION['username'];

        $SQL = "UPDATE group_activity SET  "; // FROM group_activity 
        foreach ($json as $key => $value) {
            $SQL = $SQL . " $key='$value',";
        }
        $SQL = substr($SQL, 0, -1);

        $SQL = $SQL . "WHERE owner_id='$user' AND active=1 AND id='$act_id'";




        $db = new DBAccess();
        $res = $db->query($SQL);
        $activities = array();
        if (!$res) {
            echo "ERROR";
            // header("location:index.php");
        }

        echo json_encode('OKAY');
    }

    public function removeProgram($params = [])
    {
        //TODO: remove group_activities
        $id = $_POST['programId'];
        $user = $_SESSION['username'];
        $SQL = "UPDATE program SET active=0 WHERE id='$id' AND owner_id='$user'";

        $db = new DBAccess();

        if (!$db->query($SQL)) {
            die("ERROR");
        }

        echo $id;
    }

    public function addNew($params = [])
    {


        $student = $params['student'];
        $date_time = date("dmyhis");
        $id = $student . $date_time;
        $SQL = "INSERT INTO program(id,student_id, active) VALUES" .
            "('$id','$student', 1)";
        if (isset($params['aval'])) {
            if ($params['aval'] == 'aval') {
                $id = $id . 'aval';
                $SQL = "INSERT INTO program(id,student_id, active, type) VALUES" .
                    "('$id','$student', 1,'aval')";
            }
        }

        //return ID;
        //echo $SQL . "<br>";
        $db = new DBAccess();

        if (!$db->query($SQL)) {
            die("ERROR. ProgramController::addNew " .mysqli_error($db->con));
        }
        //echo "add new program $user $name $category";
        return $id;
    }

    function  getGroupActivity_json($params = [])
    {
        isset($params['id']) ? $act_id = $params['id'] : $act_id = "";
        $user = $_SESSION['username'];

        $SQL = "SELECT * FROM group_activity WHERE owner_id='$user' AND active=1 AND id='$act_id'";

        $db = new DBAccess();
        $res = $db->query($SQL);
        $activities = array();
        if (!$res) {
            echo "ERROR";
            // header("location:index.php");
        }
        while ($fetch = mysqli_fetch_assoc($res)) {
            array_push($activities, ($fetch));
        }
        echo json_encode($activities);
    }


    function getGroups_json($params = [])
    {
        ///get groups
        $programId =  $_POST['programId'];
        $user = $_SESSION['username'];

        $SQL  = "SELECT * FROM program WHERE owner_id='$user' AND curriculumId='$programId' and active=1 ORDER BY 'position'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if (!$res) {
            echo "ERROR 1";
            // header("location:index.php");
        }
        $groups = array();
        while ($fetch = mysqli_fetch_assoc($res)) {
            array_push($groups, ($fetch));
        }

        for ($i = 0; $i < count($groups); $i++) {
            $group_id = $groups[$i]['id'];
            $groups[$i]['activities'] = array();

            $SQL = "SELECT a.id, a.name, a.antecedent, a.behavior, a.consequence, b.position,  b.reinforcement_type, b.reinforcement_value, b.correction_type, b.correction_value, b.id as group_activity_id FROM activity as a LEFT JOIN group_activity as b on a.id= b.activity_id WHERE b.group_id='$group_id' AND b.owner_id='$user' AND b.active=1 AND auto_guide=FALSE ORDER BY b.position";

            //$SQL  = "SELECT * FROM group_activity WHERE group_id='$group_id' AND owner_id='$user' AND active=1";

            $res = $db->query($SQL);
            if (!$res) {
                echo "ERROR 2";
                // header("location:index.php");
            }
            while ($fetch = mysqli_fetch_assoc($res)) {
                array_push($groups[$i]['activities'], ($fetch));
            }
        }
        echo json_encode($groups);
    }


    function getProgramData_json($params = [])
    {
        $programId =  $_POST['programId'];

        $user = $_SESSION['username'];

        $SQL  = "SELECT * FROM curriculum WHERE id='$programId' AND active=1";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if (!$res) {
            echo "ERROR";
            // header("location:index.php");
        }

        $fetch = mysqli_fetch_assoc($res);
        echo json_encode($fetch);
    }

    function getProgram_json($params = [])
    {
        $programId =  $_POST['programId'];
        $groupId = $_POST['groupId'];
        $user = $_SESSION['username'];

        $SQL  = "SELECT * FROM program WHERE owner_id='$user' AND curriculumId='$programId' AND id='$groupId' and active=1";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if (!$res) {
            echo "ERROR";
            // header("location:index.php");
        }

        $fetch = mysqli_fetch_assoc($res);
        echo json_encode($fetch);
    }

    function getGroup_json($params = [])
    {
        $programId =  $_POST['programId'];

        $user = $_SESSION['username'];

        $SQL  = "SELECT * FROM program WHERE  id='$programId' AND active=1";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if (!$res) {
            echo "ERROR";
            // header("location:index.php");
        }

        $fetch = mysqli_fetch_assoc($res);
        echo json_encode($fetch);
    }

    public function edit($params = [])
    {

        isset($params['studentId']) ? $student = $params['studentId'] : $student = '';
        isset($params['id']) ? $id = $params['id'] : $id = '';
        if ($id == null && $student == null)
            header("location:index.php");
        $db = new DBAccess();




        $data = [];

        $data['page_title'] = "Editar Currículo";

        $user = $_SESSION['username'];
        $SQL = "SELECT * FROM curriculum WHERE (id='$id' OR student_id='$student')";



        $res = $db->query($SQL);
        if (!$res) {
            header("location:index.php");
        }

        $fetch = mysqli_fetch_assoc($res);

        $data['student_id'] = $fetch['student_id'];
        $data['name'] = $fetch['name'];
        $data['description'] = $fetch['description'];
        $data['category'] = $fetch['category'];
        $data['programId'] = $fetch['id'];
        $data['curriculum_type'] = $fetch['type'];



        /**********************************************************************/

        /**********************************************************************/

        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/editProgram.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function new($params = [])
    {
        $data = [];

        $data['page_title'] = "Novo Programa de Ensino";

        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/newProgram.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function checkLastRun($params = [])
    {
        $s_id = $params['studentId'];
        $SQL = "SELECT * FROM session WHERE student_id='$s_id' ORDER BY last_date DESC LIMIT 1";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if (!$res) {
            return null;
        }
        $fetch = mysqli_fetch_assoc($res);
        return $fetch;
    }
    public function run($params = [])
    {
        $data = [];

        isset($params['curriculumId']) ? $curriculumId = $params['curriculumId'] : $curriculumId = "NULL";
        isset($params['type']) ? $type = $params['type'] : $type = "curriculum";
        isset($params['preview']) ? $preview = $params['preview'] : $preview = "preview";
        isset($params['programId']) ? $programId = $params['programId'] : $programId = "NULL";

        isset($params['studentId']) ? $studentId = $params['studentId'] : $studentId = "";
        isset($params['continue']) ? $continue = $params['continue'] : $continue = "new";

        isset($params['session_id']) ? $session_id = $params['session_id'] : $session_id = "NULL";
        $user_id = $_SESSION['username'];

        //////
        $data['instructions'] = [];
        $arr = scandir(ROOTPATH . "/activity/instructions");
        foreach ($arr as $el) {
            if (strcmp($el, '.') != 0 && strcmp($el, '..') != 0) {
                array_push($data['instructions'], "instructions/" . $el);
            }
        }
        //////

        $data['preview'] = $preview;
        $data['type']    = $type;
        $data['curriculumId'] = $curriculumId;
        $data['programId'] = $programId;

        $session = [];
        $session['type'] = $type;
        $session['preview'] = $preview;
        $session['programs'] = [];
        $session['continue'] = $continue;
        $session['studentId'] = $studentId;

        if ($curriculumId == "NULL" && $programId == "NULL" && $session_id == "NULL") {
            die('ERROR ProgramController::run: no session, curriculum or program');
        }

        if ($preview == 'run') {
            /**
             * For a random run, check which group_activities already have an trial and remove it 
             * from the results. Shuffle the 'activityQueue' in 'views/runProgram.php'
             */

            if ($continue == 'new') {
                $ids = $this->newSession(['student_id' => $studentId, 'curriculum_id' => $curriculumId]);
                $session['session_id'] = $ids['session_id'];
            } else {

                $ids = $this->loadSession(['session_id' => $session_id]);
                $session['session_id'] = $session_id;
                $curriculumId = $ids['curriculum_id'];
                $session['last_program'] = $ids['last_program_id'];
                $session['last_group_activity'] = $ids['last_activity_group_id'];
            }


            // $prog_id = $data['programId'];
            $SQL = "SELECT p.name as p_name, p.id as p_id, p.reinforcement_type as p_reinforcement_type, p.reinforcement_value as p_reinforcement_value, p.error_type as p_correction_type, p.error_value as p_correction_value," .
                "ga.id as ga_id, ga.position as ga_position, ga.reinforcement_type as ga_reinforcement_type, ga.reinforcement_value as ga_reinforcement_value, ga.correction_type as ga_correction_type, ga.correction_value as ga_correction_value," .
                "a.id as activity_id " .
                " FROM  program p  JOIN group_activity ga ON ga.group_id=p.id JOIN activity a ON ga.activity_id=a.id WHERE p.curriculumId='$curriculumId' AND p.active=1 ORDER BY p.position";

            $db = new DBAccess();
            $res = $db->query($SQL);
            if (!$res) {
                die("ERROR ProgramController::run " . mysqli_error($db->con));
            }

            $prog_act = [];
            while ($fetch = mysqli_fetch_assoc($res)) {
                $progId = $fetch['p_id'];

                if (!isset($prog_act[$progId])) {
                    $prog = [];
                    $prog['id'] = $progId;
                    $prog['name'] = $fetch['p_name'];
                    $prog['id'] = $fetch['p_id'];
                    $prog['reinforcement_type'] = $fetch['p_reinforcement_type'];
                    $prog['reinforcement_value'] = $fetch['p_reinforcement_value'];
                    $prog['correction_type'] = $fetch['p_correction_type'];
                    $prog['correction_value'] = $fetch['p_correction_value'];
                    $prog['activities'] = [];
                    array_push($session['programs'], $prog);

                    $prog_act[$progId] = [];
                }
                $act = [];
                $act['activity_id'] = $fetch['activity_id'];
                $act['program_trial_id'] = $ids[$progId];
                $act['ga_program_id'] = $progId;
                $act['ga_id'] = $fetch['ga_id'];
                $act['ga_position'] = $fetch['ga_position'];
                $act['ga_reinforcement_type'] = $fetch['ga_reinforcement_type'];
                $act['ga_reinforcement_value'] = $fetch['ga_reinforcement_value'];
                $act['ga_correction_type'] = $fetch['ga_correction_type'];
                $act['ga_correction_value'] = $fetch['ga_correction_value'];

                array_push($prog_act[$progId], $act);
            }

            for ($i = 0; $i < count($session['programs']); $i++) {
                $prog_id = $session['programs'][$i]['id'];
                $session['programs'][$i]['program_trial'] = $ids[$prog_id];
                foreach ($prog_act[$prog_id] as $a) {
                    array_push($session['programs'][$i]['activities'], $a);
                }
            }
        } else { ///PREVIEW ONLY

           // if ($type == "curriculum") {
                $prog_id = $data['programId'];
                if($type=="curriculum"){

                
                    $SQL = "SELECT p.name as p_name, p.id as p_id, p.reinforcement_type as p_reinforcement_type, p.reinforcement_value as p_reinforcement_value, p.error_type as p_correction_type, p.error_value as p_correction_value," .
                    "ga.id as ga_id, ga.position as ga_position, ga.reinforcement_type as ga_reinforcement_type, ga.reinforcement_value as ga_reinforcement_value, ga.correction_type as ga_correction_type, ga.correction_value as ga_correction_value," .
                    "a.id as activity_id " .
                    " FROM  program p  JOIN group_activity ga ON ga.group_id=p.id JOIN activity a ON ga.activity_id=a.id WHERE p.curriculumId='$curriculumId' AND p.active=1 ";
                }
                else{
                    $SQL = "SELECT p.name as p_name, p.id as p_id, p.reinforcement_type as p_reinforcement_type, p.reinforcement_value as p_reinforcement_value, p.error_type as p_correction_type, p.error_value as p_correction_value," .
                    "ga.id as ga_id, ga.position as ga_position, ga.reinforcement_type as ga_reinforcement_type, ga.reinforcement_value as ga_reinforcement_value, ga.correction_type as ga_correction_type, ga.correction_value as ga_correction_value," .
                    "a.id as activity_id " .
                    " FROM  program p  JOIN group_activity ga ON ga.group_id=p.id JOIN activity a ON ga.activity_id=a.id WHERE p.curriculumId='$curriculumId' AND p.active=1 AND p.id='$programId'";
                }
                //echo $SQL . "<br>";
                $db = new DBAccess();
                $res = $db->query($SQL);
                if (!$res) {
                    die("ERROR ProgramController::run " . mysqli_error($db->con));
                }

                $prog_act = [];
                while ($fetch = mysqli_fetch_assoc($res)) {
                    $progId = $fetch['p_id'];

                    if (!isset($prog_act[$progId])) {
                        $prog = [];
                        $prog['id'] = $progId;
                        $prog['name'] = $fetch['p_name'];
                        $prog['id'] = $fetch['p_id'];
                        $prog['reinforcement_type'] = $fetch['p_reinforcement_type'];
                        $prog['reinforcement_value'] = $fetch['p_reinforcement_value'];
                        $prog['correction_type'] = $fetch['p_correction_type'];
                        $prog['correction_value'] = $fetch['p_correction_value'];
                        $prog['activities'] = [];
                        array_push($session['programs'], $prog);

                        $prog_act[$progId] = [];
                    }
                    $act = [];
                    $act['activity_id'] = $fetch['activity_id'];
                    $act['ga_id'] = $fetch['ga_id'];
                    $act['ga_position'] = $fetch['ga_position'];
                    $act['ga_program_id'] = $progId;
                    $act['ga_reinforcement_type'] = $fetch['ga_reinforcement_type'];
                    $act['ga_reinforcement_value'] = $fetch['ga_reinforcement_value'];
                    $act['ga_correction_type'] = $fetch['ga_correction_type'];
                    $act['ga_correction_value'] = $fetch['ga_correction_value'];

                    array_push($prog_act[$progId], $act);
                }

                for ($i = 0; $i < count($session['programs']); $i++) {
                    $prog_id = $session['programs'][$i]['id'];
                    foreach ($prog_act[$prog_id] as $a) {
                        array_push($session['programs'][$i]['activities'], $a);
                    }
                }
            //} 
            /*else {
                $prog_id = $data['programId'];
                $SQL = "SELECT * FROM group_activity WHERE group_id='$prog_id' AND active=1 AND guide_id=0";
                echo $SQL . "<br>";
                $db = new DBAccess();
                $res = $db->query($SQL);
                if (!$res) {
                    die("ERROR ProgramController::run " . mysqli_error($db->con));
                }

                while ($fetch = mysqli_fetch_assoc($res)) {

                    print_r($fetch) . "<br>";
                }
                die('pausa');
            }*/
        }

        $data['session'] = json_encode($session);
        $this->loadView("views/runProgram.php", $data);
    }




    public function newProgramTrial($params = [])
    {

        $owner_id = $_SESSION['username'];
        $date_time = microtime(true); //date("dmyhis");
        $date_time = str_replace('.', '', $date_time); 
        $id = $owner_id . $date_time;
        $program_id = $params['program_id'];
        $student_id = $params['student_id'];
        $session_id = $params['session_id'];


        $db = new DBAccess();

        $SQL = "INSERT INTO program_trial(id,program_id,student_id,professional_id,session_id) "
            . "VALUES('$id','$program_id','$student_id','$owner_id','$session_id')";
        if (!$db->query($SQL)) {
            die('ERROR. ProgramController:newProgramTrial: ' . mysqli_error($db->con));
        }
        return $id;
    }


    public function updateSessionStatus($params = [])
    {
        $id = $params['session_id'];
        $last_trial = $params['last_trial'];
        $date = date("Y-m-d H:i:sa");
        $SQL = "UPDATE session SET last_trial='$last_trial', last_date='$date'" .
            " WHERE id='$id'";

        $db = new DBAccess();
        if (!$db->query($SQL)) {
            die("error: ProgramController::updateSessionStatus: " . mysqli_error($db->con));
        }
    }

    public function finishSession($params = [])
    {
        $id = $_POST['session_id'];
        $date = date("Y-m-d H:i:sa");
        $SQL = "UPDATE session SET complete=1, last_date='$date'" .
            " WHERE id='$id'";
        $db = new DBAccess();
        if (!$db->query($SQL)) {
            die("error: ProgramController::finishSession: " . mysqli_error($db->con));
        }
        echo "FINISH";
    }


    public function saveTrial($params = [])
    {
        $date_time = microtime(true); //date("dmyhis");
        $date_time = str_replace('.', '', $date_time); 
        $id = "TRIAL_" . $date_time;

        $program_trial_id   = $_POST["program_trial_id"];
        $student_id         = $_POST["student_id"];
        $professional_id    = $_SESSION['username'];
        $activity_id        = $_POST["activity_id"];
        $result             = $_POST["result"];
        $result_data        = $_POST["result_data"];
        $start_date         = $_POST["start_date"];
        $end_date           = $_POST["end_date"];
        $session_id         = $_POST["session_id"];
        $groupactivity_id   = $_POST['groupactivity_id'];

        $SQL = "INSERT INTO trial(id, program_trial_id, student_id, professional_id, activity_id, result, result_data, start_date, end_date, session_id,groupactivity_id) " .
            "VALUES('$id','$program_trial_id','$student_id','$professional_id','$activity_id','$result','$result_data','$start_date','$end_date','$session_id','$groupactivity_id')";

        $db = new DBAccess();
        if (!$db->query($SQL)) {
            die("error ProgramController::saveTrial: " . mysqli_error($db->con));
        }
        $this->updateSessionStatus(['session_id' => $session_id, 'last_trial' => $id]);
        echo "OK";
    }



    public function getSessionData_json($params = [])
    {
        $session_id = $params['sessionId'];
        $student_id = $params['studentId'];
        $db = new DBAccess();
        $SQL = "SELECT * FROM session WERE id='$session_id' AND student_id='$student_id' ORDER BY last_date DESC";
        $res = $db->query($SQL);
        if ($res) {
            die('Error: ProgramController::getSessionData_json ' . mysqli_error($db->con));
        }

        $fetch = mysqli_fetch_assoc($res);
    }
    public function newSession($params = [])
    {

        $owner_id = $_SESSION['username'];
        $date_time = date("dmyhis");

        $session_id = "SESS_" . $owner_id . $date_time;
        $student_id     = $params['student_id'];
        $curriculum_id  = $params['curriculum_id'];

        $ret = [];
        $ret['session_id'] = $session_id;


        $db = new DBAccess();

        $date = date("Y-m-d H:i:sa");
        $SQL = "INSERT INTO session(id,student_id,professional_id,last_date, complete, curriculum_id, last_trial) " .
            "VALUES('$session_id','$student_id','$owner_id','$date', 0, '$curriculum_id', 'FROM_START') ";

        if (!$db->query($SQL)) {
            die("Error. ProgramController::newSession: " . mysqli_error($db->con));
        }


        $SQL = "SELECT * FROM program WHERE curriculumId='$curriculum_id'";
        $res = $db->query($SQL);
        if (!$res) {
            die("Error. ProgramController::newSession: " . mysqli_error($db->con));
        }
        while ($fetch = mysqli_fetch_assoc($res)) {
            $prog_id = $fetch['id'];
            $prog_trial_id = $this->newProgramTrial(['student_id' => $student_id, 'program_id' => $prog_id, 'session_id' => $session_id]);
            $ret[$prog_id] = $prog_trial_id;
        }

        return $ret;
    }





    public function loadSession($params = [])
    {

        $session_id = $params['session_id'];


        $ret = [];
        $ret['session_id'] = $session_id;


        $db = new DBAccess();
        $SQL = "SELECT s.id as s_id, s.curriculum_id as s_curriculum_id, s.last_trial as s_last_trial, t.program_trial_id as t_program_trial_id, t.groupactivity_id as t_g_activity_id, pt.program_id as p_program_id " .
            "FROM session s JOIN trial t ON s.id=t.session_id JOIN program_trial pt ON t.program_trial_id=pt.id WHERE s.id='$session_id'";

        $res = $db->query($SQL);
        if (!$res) {
            die("Error. ProgramController::newSession: " . mysqli_error($db->con));
        }
        if (mysqli_num_rows($res) > 0) {
            $fetch = mysqli_fetch_assoc(($res));
            $ret['curriculum_id'] = $fetch['s_curriculum_id'];
            $ret['last_trial'] = $fetch['s_last_trial'];
            $ret['last_program_id'] = $fetch['p_program_id'];
            $ret['last_activity_group_id'] = $fetch['t_g_activity_id'];
        } else {
            $db = new DBAccess();
            $SQL = "SELECT * FROM session WHERE id='$session_id'";
            $res = $db->query($SQL);
            if (!$res) {
                die("Error. ProgramController::newSession: " . mysqli_error($db->con));
            }
            $fetch = mysqli_fetch_assoc(($res));
            $ret['curriculum_id'] = $fetch['curriculum_id'];
            $ret['last_trial'] = $fetch['last_trial'];
            $ret['last_program_id'] = "FROM_START";
            $ret['last_activity_group_id'] = "FROM_START";
        }


        $SQL = "SELECT * FROM program_trial WHERE session_id='$session_id'";

        $res = $db->query($SQL);
        if (!$res) {
            die("Error. ProgramController::newSession: " . mysqli_error($db->con));
        }

        while ($fetch = mysqli_fetch_assoc($res)) {
            $prog_id = $fetch['program_id'];
            $prog_trial_id = $fetch['id'];
            $ret[$prog_id] = $prog_trial_id;
        }

        return $ret;
    }
}
