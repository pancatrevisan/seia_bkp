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

class StimuliController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($data = []) {
        $page = 1;
        $filter = "";
        if(isset($_GET['page']))
        {
            $page = $_GET['page'];
        }
        if(isset($_GET['query']))
        {
            $filter = $_GET['query'];
        }
       
        $this->filter($filter, $page);
    }

    public function filter_form($data = []) {
        $query = "";
        if (isset($_POST['query'])) {
            $query = $_POST['query'];
        }
        $this->filter($query, 1);
    }

    public function filter($query, $page, $data = []) {
        global $sel_lang;
        
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $this->loadView(ROOTPATH . "/ui/header.php", ['page_title'=>$lang['filter_page_name']]);
        $this->loadView("views/mainView.php", ['query' => $query, 'page' => $page]);
        $this->loadView(ROOTPATH . "/ui/footer.php", null);
    }

    private function dataFromPost(){
        $data = [];
        if (isset($_POST['stimuli_name']))
            $data['stimuli_name'] = $_POST['stimuli_name'];


        if (isset($_POST['stimuli_description']))
            $data['stimuli_description'] = $_POST['stimuli_description'];

        if (isset($_POST['stimuli_category']))
            $data['stimuli_category'] = $_POST['stimuli_category'];


        if(isset($_POST['stimuli_type']))
            $data['stimuli_type'] =  $_POST['stimuli_type'];
        
        return $data;
    }
    public function newStimuli($data = []) {       
        
        $data = $this->dataFromPost();
        
        if($data['stimuli_type']=='image'){
            $this->newStimuliImage($data);
        }
        elseif($data['stimuli_type']=='audio'){
            $this->newStimuliAudio($data);
        }
        
        
    }

    public function newStimuliAudio($data = []) {
        if (!isset($_POST['stimuli_name'])) {
            header('location:index.php?action=newStimuli');
        }
        global $sel_lang;
        global $lang;
       
        $data['page_title'] = $lang['page_title'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/newStimuliAudio.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }


    public function removeStimuli($data=[]){
        $user_id = $_SESSION['username'];
        $stimuli_id = $_POST['stimuli_id'];
        
        $SQL = "UPDATE stimuli SET active=FALSE WHERE owner_id='$user_id' AND id='$stimuli_id'";

        $db = new DBAccess();
        if(!$db->query($SQL)){
            die('Error StimuliController::removeStimuli. ' . mysqli_error($db->con));
        }

        echo "OK";
    }
    

    public function getById_as_json_url($data=[]){
        $id = $_GET['id'];
        $SQL = "SELECT * FROM stimuli WHERE id='$id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            die(mysqli_error($db->con));
        }
        $fetch = mysqli_fetch_assoc($res);
        
        $s_id = $fetch['id'];
        $SQL = "SELECT * FROM label WHERE stimuli_id='$s_id'";
        $labels = $db->query($SQL);
        if($labels){
            while($l = mysqli_fetch_assoc($labels)){
                if(!isset($fetch['labels'])){
                    $fetch['labels'] = [];
                }
                array_push($fetch['labels'],$l['value']);
            }
        }
        echo json_encode($fetch);
    }


    public function getById_as_json($data=[]){
        $id = $_GET['id'];
        //$destination = $_GET['destination'];
        $SQL = "SELECT * FROM stimuli WHERE id='$id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            die(mysqli_error($db->con));
        }
        
        $fetch = mysqli_fetch_assoc($res);
        $fetch['SQL_S'] = $SQL;
        $s_id = $fetch['id'];
        $SQL = "SELECT * FROM label WHERE stimuli_id='$s_id'";
        $labels = $db->query($SQL);
        if($labels){
            while($l = mysqli_fetch_assoc($labels)){
                if(!isset($fetch['labels'])){
                    $fetch['labels'] = [];
                }
                array_push($fetch['labels'],$l['value']);
            }
        }

        if($fetch['type']=='image'){
            $fetch['data']  = getData($fetch['url']);
            unset($fetch['url']);       
        }
        //$fetch["DESTINATION"] = $destination;

        echo json_encode($fetch);
    }
    /**
     * 
     * @param type $data: 
     * type: image/audio
     * query: query filter
     * offset: start 
     * @return type
     */
    public function get_as_json($data = []){
        //TODO: USAR OFFSET
        $types   = isset($data['types'])?$types=$data['types']:$types="image,audio";

        $query  = $data['query'];
        $offset = isset($data['offset'])? $data['offset']: '0';
        $results_per_page = isset($data['results_per_page'])? $data['results_per_page']: 12;; 
        $local_data = false;
        $as_json = !isset($data['resultsAsArray']);

        if(isset($data['local']))
        {
            if( strcmp($data['local'],'yes') == 0){
                $local_data = true;
            }
        }
        
        if($local_data){
            return;
        }
        
        
        $user = $_SESSION['username']; // TODO
        $SQL = "SELECT COUNT(*) AS total FROM stimuli WHERE (owner_id='$user' OR owner_id='pub') AND active=TRUE";
        
        if(strlen($types) > 0){
            //break type into array
            $typeArr = explode(',', $types);
            if(count($typeArr) <= 1){
                $SQL = $SQL . " AND type='$typeArr[0]'";
            }
            else{
                $SQL = $SQL . " AND type in(";
                for($t = 0; $t<count($typeArr); $t=$t+1){
                    $_type = $typeArr[$t];
                    $SQL = $SQL ."'$_type'";
                    if($t<(count($typeArr)-1)){
                        $SQL = $SQL .",";
                    }
                }
                $SQL = $SQL . ")";
            }
        }
        
        if(strlen($query)>0){
            $SQL = $SQL . " AND (name LIKE '%$query%'  OR description LIKE '%$query%')";
        }
        $db = new DBAccess();
        $total = $db->query($SQL);
        $total = mysqli_fetch_assoc($total);
        $total = $total['total'];

        $SQL = $SQL . "ORDER BY (date) DESC LIMIT  $results_per_page OFFSET  $offset  ";
        
        
        
        
        $SQL = "SELECT * FROM stimuli WHERE (owner_id='$user' OR owner_id='pub') AND active=TRUE";
        
        if(strlen($types) > 0){
            //break type into array
            $typeArr = explode(',', $types);
            if(count($typeArr) <= 1){
                $SQL = $SQL . " AND type='$typeArr[0]'";
            }
            else{
                $SQL = $SQL . " AND type in(";
                for($t = 0; $t<count($typeArr); $t=$t+1){
                    $_type = $typeArr[$t];
                    $SQL = $SQL ."'$_type'";
                    if($t<(count($typeArr)-1)){
                        $SQL = $SQL .",";
                    }
                }
                $SQL = $SQL . ")";
            }
        }
        
        if(strlen($query)>0){
            $SQL = $SQL . " AND (name LIKE '%$query%'  OR description LIKE '%$query%') ";
        }
        
        $SQL = $SQL . "ORDER BY (date) DESC LIMIT  $results_per_page OFFSET  $offset";
                
        $res = $db->query($SQL);
        $j_son =[];
        $j_son['total'] = $total;
        $j_son['results'] = [];
        while($fetch = mysqli_fetch_assoc($res))
        {   
            $s_id = $fetch['id'];
            $SQL = "SELECT * FROM label WHERE stimuli_id='$s_id'";
            $labels = $db->query($SQL);
            if($labels){
                while($l = mysqli_fetch_assoc($labels)){
                    if(!isset($fetch['labels'])){
                        $fetch['labels'] = [];
                    }
                    array_push($fetch['labels'],$l['value']);
                }
            }

            if($fetch['type']=='image'){
                $fetch['data']  = getData($fetch['url']);
                unset($fetch['url']);       
            }
            array_push($j_son['results'],$fetch);
        }
        if($as_json) 
            echo json_encode($j_son);                            
        else
        return $j_son;
    }
    
    public function newStimuliForm($data = []) {
        global $sel_lang;
        global $lang;
        $data = $this->dataFromPost();
        $data['page_title'] = $lang['page_title'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        //$this->loadView("views/newStimuli.php", $data);
        $this->loadView("views/newStimuliAll.php", $data);
        //$this->loadView(ROOTPATH . "/activity/views/newStimuliModal.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }
    
    public function newStimuliModal($data=[]){
        global $sel_lang;
        global $lang;
       
        $data['page_title'] = $lang['page_title'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/newImageModal.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }


        public function newStimuliImage($data = []) {
        if (!isset($_POST['stimuli_name'])) {
            header('location:index.php?action=newStimuli');
        }
        global $sel_lang;
        global $lang;
       
        $data['page_title'] = $lang['page_title'];
        $this->loadView(ROOTPATH . "/ui/header.php", $data);
        $this->loadView("views/newStimuliImage.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", $data);
    }

    public function see($data = []) {
        $id = $_GET['id'];
    }


    public function newStimuliProccessForm($data = []) {
        ///add new stimuli in database.
        $userName = $_SESSION['username'];
        $date_time = microtime(true);
        $date_time = str_replace('.', '', $date_time); 
       
        $name = $_POST['stimuli_name'];
        $owner_id = $userName;
        $category = $_POST['stimuli_category'];
        $description = $_POST['stimuli_description'];
        $type = $_POST['stimuli_type'];
        $modal = isset($_POST['modal'])? true : false;
        $publicStimuli = isset($_POST['publicStimuli']);
        if($publicStimuli){
            $owner_id = "pub";//'public' user
        }

        $id = $userName . $date_time;
        
        if($_FILES["stimuli_file"]["error"] > 0){
            //TODO: file error
        }
        
        //echo 'error: ' . $_FILES["stimuli_file"]["error"] . "<br>";
        $file_name = $id;
        if ($type != "youtubeVideo") {
            $info = new SplFileInfo($_FILES['stimuli_file']['name']);
            $ext = $info->getExtension();
            $file_name = $file_name . '.' . $ext;
        }

        $url = "";
        if ($type != "youtubeVideo") {
            $url = '/data/user/' . $owner_id . '/stimuli/' . $file_name;
            if($owner_id == 'pub'){
                $url = '/data/' . $owner_id . '/stimuli/' . $file_name;
            }
        }
        $version = 1;

        

        $cats = explode(" ",$category);
        $db = new DBAccess();

        for($i=0; $i < count($cats); $i++){
            $date_time = microtime(true);
            $date_time = str_replace('.', '', $date_time); 
            $label_id = "cat_" . $date_time;
            $val = $cats[$i];
            $SQL = "SELECT COUNT(*) as total FROM label WHERE value='$val' AND stimuli_id='$id'";
            $has_label = $db->query($SQL);
            $has_label = mysqli_fetch_assoc($has_label);
            $has_label = $has_label['total'] > 0;
            
            if(!$has_label){
                $SQL = "INSERT INTO label(id,stimuli_id, value) VALUES ('$label_id', '$id','$val')";
                if (!$db->query($SQL)) {
                    die("Error. StimuliController::newStimuliProccessForm ".mysqli_error($db->con));
                }
            }
        }

        

        $SQL = "INSERT INTO stimuli(id, name, owner_id,  description, type, url, version) " .
                "VALUES('$id', '$name', '$owner_id',  '$description', '$type', '$url', $version)";

        
        
        if ($db->query($SQL)) {
            //echo "original file: " . $_FILES['stimuli_file']['tmp_name'] . "<br>";
            move_uploaded_file($_FILES['stimuli_file']['tmp_name'], ROOTPATH . $url);
            //echo "file: " . ROOTPATH . $url . "<br>";
            if(!$modal){
                global $sel_lang;
                global $lang;
                $data = [];
                $data['page_title'] = $lang['page_title'];
                $this->loadView(ROOTPATH . "/ui/header.php", $data);
                $this->loadView("views/addedStimuliOK.php", $data);
                $this->loadView(ROOTPATH . "/ui/footer.php", $data);
            }
            else{
                echo "INSERT_SIMULI_OK";
            }
        }
    }

}
