<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["professional", "admin"], BASE_URL);

require_once ROOTPATH . '/core/Controller.php';
require_once ROOTPATH . '/utils/DBAccess.php';
require_once ROOTPATH . '/utils/GetData.php';
$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/newStimuli.php";

class DataExportController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [];
        isset($param['error']) ? $data['error'] = true : $data['error'] = false;
        $data["page_name"] = "Select ..";
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/main.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }


    public function exportStudentData($params = [])
    {

        $student = $params['studentId'];
        $data = [];
        $data['student'] = $student;
        isset($params['page']) ? $data['page'] = $params['page'] : $data['page'] = 1;
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/viewData.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function students($params = [])
    {

        isset($params['page']) ? $data['page'] = $params['page'] : $data['page'] = 1;
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/userStudents.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function getStudentData_json($params = [])
    {

        $student_id = $_POST['student'];
        $SQL = //"SELECT * FROM sessionprogram_activity_trial spat INNER JOIN session_program_activity spa ON spat.sessionactivity_id=spa.id INNER JOIN activity a ON spa.activity_id=a.id WHERE spat.student_id='$student_id'";


            "SELECT spat.id as spat_id,  spat.sessionprogramTrial_id as spat_sessionprogramTrial_id, spat.student_id as spat_student_id, " .
            "spat.professional_id as spat_professional_id, spat.sessionactivity_id as spat_sessionactivity_id, spat.result as spat_result, spat.result_data as spat_result_data, spat.start_date as spat_start_date , " .
            "spat.end_date as spat_end_date, spa.id as spa_id, spa.sessionProgram_id as spa_sessionProgram_id, spa.correction_type as spa_correction_type, " .
            "spa.correction_value as spa_correction_value, spa.reinforcer_type as spa_reinforcer_type, spa.reinforcer_value as spa_reinforcer_value, spa.next_on_correct as spa_next_on_correct," .
            "spa.next_on_wrong as spa_next_on_wrong, spa.position as spa_position, spa.next_on_correct_id as spa_next_on_correct_id, spa.next_on_wrong_id as spa_next_on_wrong_id, " .
            "spa.next_after_correction as spa_next_after_correction, spa.next_after_correction_id as spa_next_after_correction_id, a.id as a_id, a.name as a_name, a.owner_id as a_owner_id, " .
            "a.difficulty as a_difficulty " .
            "FROM sessionprogram_activity_trial spat JOIN session_program_activity spa ON spat.sessionactivity_id=spa.id JOIN activity a ON spa.activity_id=a.id WHERE spat.student_id='$student_id' " .
            " ORDER BY spat.end_date ASC";


        $db = new DBAccess();


        $BUNCH_OF_DATA = [];
        $BUNCH_OF_DATA['ACTIVITY_DATA'] = [];
        $BUNCH_OF_DATA['STIMULI_DATA'] = [];
        $BUNCH_OF_DATA['TRIAL_DATA'] = [];


        $res = $db->query($SQL);
        if (!$res) {
            die(mysqli_error($db->con));
        }
        while ($fetch = mysqli_fetch_assoc($res)) {

            $activity_id = $fetch['a_id'];
            //activity data...
            if (!array_key_exists($activity_id, $BUNCH_OF_DATA['ACTIVITY_DATA'])) {
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]  = [];
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['id'] =  $activity_id;
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['difficulty'] =  $fetch['a_difficulty'];
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['name'] = $fetch['a_name'];
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['owner_id'] = $fetch['a_owner_id'];

                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli'] = [];
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli']['image'] = [];
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli']['audio'] = [];
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli']['video'] = [];
                $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli']['text'] = [];
                //gather code and extract stimuli information.
                $xml_file_path = BASE_URL . "/data/user/" . $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['owner_id'] . "/activity/" . $BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['id'] . "/main.xml";
                $xml_data = file_get_contents($xml_file_path);
                $xml = simplexml_load_string($xml_data);


                foreach ($xml->resources->image as $img) {
                    $str = "" . $img[0];
                    array_push($BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli']['image'], $str);
                    if (!array_key_exists($str, $BUNCH_OF_DATA['STIMULI_DATA'])) {
                        $BUNCH_OF_DATA['STIMULI_DATA'][$str] = [];
                        $BUNCH_OF_DATA['STIMULI_DATA'][$str]['key'] = $str;
                    }
                }
                foreach ($xml->resources->audio as $audio) {
                    $str = "" . $audio[0];
                    array_push($BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli']['audio'], $str);
                    if (!array_key_exists($str, $BUNCH_OF_DATA['STIMULI_DATA'])) {
                        $BUNCH_OF_DATA['STIMULI_DATA'][$str] = [];
                        $BUNCH_OF_DATA['STIMULI_DATA'][$str]['key'] = $str;
                    }
                }

                $texts = $xml->xpath("//instruction/data/text/text");
                foreach ($texts as $t) {

                    $str = "" . $t[0];
                    array_push($BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli']['text'], $str);
                }

                $videos = $xml->xpath("//instruction/data/video/url");
                foreach ($videos as $v) {
                    $str = "" . $v[0];
                    array_push($BUNCH_OF_DATA['ACTIVITY_DATA'][$activity_id]['stimuli']['video'], $str);
                }
            }

            //trial data..
            $trial_id = $fetch['spat_id'];
            $BUNCH_OF_DATA['TRIAL_DATA'][$trial_id] = [];
            $BUNCH_OF_DATA['TRIAL_DATA'][$trial_id]['result'] = $fetch['spat_result'];
            $BUNCH_OF_DATA['TRIAL_DATA'][$trial_id]['start_date'] = $fetch['spat_start_date'];
            $BUNCH_OF_DATA['TRIAL_DATA'][$trial_id]['end_date'] = $fetch['spat_end_date'];
            $BUNCH_OF_DATA['TRIAL_DATA'][$trial_id]['result_data'] = json_decode($fetch['spat_result_data'], true);
            $BUNCH_OF_DATA['TRIAL_DATA'][$trial_id]['activity_id'] = $activity_id;
        }

        foreach ($BUNCH_OF_DATA['STIMULI_DATA'] as $s) {
            $key = $s['key'];
            $SQL = "SELECT * FROM stimuli INNER JOIN label ON stimuli.id=label.stimuli_id WHERE stimuli.id='$key'";
            $search = $db->query($SQL);
            if (!$search) {
                die("ERROR " . mysqli_error($db->con));
            }

            while ($fetch = mysqli_fetch_assoc($search)) {
                $BUNCH_OF_DATA['STIMULI_DATA'][$key]["id"] = $fetch['id'];
                if (!isset($BUNCH_OF_DATA['STIMULI_DATA'][$key]["labels"])) {
                    $BUNCH_OF_DATA['STIMULI_DATA'][$key]["labels"] = [];
                }
                if(!isset($BUNCH_OF_DATA['STIMULI_DATA'][$key]["description"])){
                    $BUNCH_OF_DATA['STIMULI_DATA'][$key]["description"] = $fetch['description'];

                }
                if(!isset($BUNCH_OF_DATA['STIMULI_DATA'][$key]["name"])){
                    $BUNCH_OF_DATA['STIMULI_DATA'][$key]["name"] = $fetch['name'];

                }
                array_push($BUNCH_OF_DATA['STIMULI_DATA'][$key]["labels"], $fetch['value']);
            }
        }

      
        echo json_encode($BUNCH_OF_DATA);
      
        
    }
}
