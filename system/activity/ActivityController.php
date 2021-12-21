<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require '../root.php';
}


 
require_once ROOTPATH . '/utils/checkUser.php';
require_once ROOTPATH . '/program/ProgramController.php';




require_once ROOTPATH . '/core/Controller.php';
require_once ROOTPATH . '/utils/DBAccess.php';
$sel_lang = 'ptb';

class ActivityController extends Controller
{

    public function copyActivity($params=[]){
        
        $source_user = $_POST['source_user'];
        $dest_user = $_POST['dest_user'];
        $xml_source = "";
        $xml_file = "";
        $asNew = true;
        $repository = false;
        $act_id = $_POST['id'];

     
        echo "source user: $source_user";
        if($source_user=='_REPOSITORY'){
            //copy xml
            $source     = ROOTPATH . "/data/pub/activities/$act_id/main.xml";
            $repository = true;
        }
        else{
            $source     = ROOTPATH . "/data/user/$source_user/activity/$act_id/main.xml";
        }

        //echo "source> $source";

        ////copy xml file
        $id = $act_id;
        $SQL = "SELECT * from activity WHERE  id='$id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        $fetch = mysqli_fetch_assoc($res);
        if(mysqli_num_rows($res) <=0 && strcmp($id, 'preview')!=0){    
            die("error");    
        }
        if(is_file($source)){
            $xml_file = file_get_contents($source);   
        }
        else{
            die( "NAO_E_ARQUIVO" );
        }
        ////copy xml file

        
        ///Save as new :) 

        $p =[];
        $p['id'] = $act_id;
        $p['asNew'] = true;
        $p['xml'] = $xml_file;
        $p['img'] = "";
        $image = "";
        if($dest_user == "_REPOSITORY"){
            $p['repository'] = true;
            $image =  ROOTPATH . "/data/user/$source_user/activity/$act_id/thumbnail.png";
        }
        else{
            $p['repository'] = false;
            $image =  ROOTPATH . "/data/pub/activities/$act_id/thumbnail.png";
        }
        
        $imageData = base64_encode(file_get_contents($image));
        // Format the image SRC:  data:{mime};base64,{data};
        $src = $imageData;
        $p['image'] = $src;

        print_r($p);
        echo "save...";
        echo $this->save($p);
        


    }
    public function  repository($params=[]){
        
        global $sel_lang;
        $data=[];
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $data['page_title'] = $lang['activities'];
        isset($params['query'])?$data['query']=$params['query']:$data['query']="";
        isset($params['page'])?$data['page']=$params['page']:$data['page']=0;
        $data['rep'] = 'true';
        
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/mainView.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
    }

    public function newUserTemplate($params=[]){

        $newTemplateId = $this->addNewUserTemplate([]);
        $this->editTemplateWizzard(['templateId'=>$newTemplateId]);
    }
    public function editTemplateWizzard($params=[]){

        $user = $_SESSION['username'];
        $data=[];
        //$newTemplateId = $this->newUserTemplate([]);
        if(isset($params['templateId'])){
            $newTemplateId = $params['templateId'];
        }
        else{
            die("error loading user template");
        }


        $SQL = "SELECT * FROM activity WHERE owner_id='$user' AND id='$newTemplateId'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if($res){
            $res = mysqli_fetch_assoc($res);
            
            $data['activity_name'] = $res['name'];
            $data['activity_antecedent'] = $res['antecedent'];
            $data['activity_behavior'] = $res['behavior'];
            $data['activity_consequence'] = $res['consequence'];
            
        }
        else
        {
            die('error');
        }
        
        global $sel_lang;
        
        $data['instructions'] = [];
        $data['pagetitle'] = "Atividade";
        $data['templateId'] = $newTemplateId;
        //$data['xml'] = $this->getActivity(['id'=>$id]);
        $arr = scandir("./instructions");
          
        foreach($arr as $el){
            if(strcmp($el, '.')!=0 && strcmp($el, '..')!=0){
                array_push($data['instructions'],"instructions/".$el);
            }
        }
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $data['page_title'] = $lang['activities'];
        isset($params['query'])?$data['query']=$params['query']:$data['query']="";
        isset($params['page'])?$data['page']=$params['page']:$data['page']=0;
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/newTemplateWizzard.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
    }

    public function userTemplateIndex($params=[]){
        //shows user templates..
       
        global $sel_lang;
        $data=[];
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $data['page_title'] = $lang['activities'];
        isset($params['query'])?$data['query']=$params['query']:$data['query']="";
        isset($params['page'])?$data['page']=$params['page']:$data['page']=0;
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/mainTemplateView.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
    }
    
    public function addNewUserTemplate($params=[]){
        $userName = $_SESSION['username'];
        $date_time = date("dmyhis");
        $id = $userName . $date_time . "template";

        $name = $userName . $date_time . "template";
        $directory_name = preg_replace("/[^A-Za-z0-9]/", "", $id);
        
        
        $antecedent = "";//$_POST['antecedent'];
        $behavior   = "";//$_POST['behavior'];
        $consequence = "";//$_POST['consequence'];
        $category = "template";//$_POST['category'];
        //$name = "";//$_POST['name'];
        
        
        
        $SQL = "INSERT INTO activity(id, owner_id,name, antecedent, behavior, consequence, category,active) " 
                . "VALUES ('$id', '$userName','$name', '$antecedent', '$behavior', '$consequence', '$category',1)";
        
        $db = new DBAccess();
        
        $source = ROOTPATH . "/data/pub/templates/empty_activity/main.xml";
        $dest_dir   = ROOTPATH . "/data/user/$userName/activity/$id/";
        $dest       = ROOTPATH . "/data/user/$userName/activity/$id/main.xml";
        
        
        
        if($db->query($SQL)){
            //copy file
            if(!mkdir($dest_dir, 0700)){
                die('Error creating directory');
            }
            if(!copy($source, $dest)){
                
                die('Error copying template');
            }
            
            return $id;
        }else
        {
             die('Error insertind in database');
        }
    }
    /* For admin only.
     */

    public function addNewTemplate_submit($params=[]){
        $userName = "pub";
        $date_time = date("dmyhis");
        
        
        $directory_name = preg_replace("/[^A-Za-z0-9]/", "", $_POST['name']);
        $id = $userName . $directory_name;
        
        $antecedent = $_POST['antecedent'];
        $behavior   = $_POST['behavior'];
        $consequence = $_POST['consequence'];
        $category = $_POST['category'];
        $name = $_POST['name'];
        
        
        
        $SQL = "INSERT INTO activity(id, owner_id,name, antecedent, behavior, consequence, category,active) " 
                . "VALUES ('$id', '$userName','$name', '$antecedent', '$behavior', '$consequence', '$category',1)";
        
        $db = new DBAccess();
        
        $source = ROOTPATH . "/data/pub/templates/empty_activity/main.xml";
        $dest_dir   = ROOTPATH . "/data/pub/templates/$id/";
        $dest       = ROOTPATH . "/data/pub/templates/$id/main.xml";
        
        
        
        if($db->query($SQL)){
            //copy file
            if(!mkdir($dest_dir, 0700)){
                die('Error creating directory');
            }
            if(!copy($source, $dest)){
                
                die('Error copying template');
            }
            
            //header("location:index.php?action=edit&id=$id");
        }else
        {
             die('Error insertind in database');
        }
        
        header("location:index.php");
    }
    
    public function addNewTemplate($params=[]){
        global $sel_lang;
        $data=[];
        
        $data['page_title'] = "Add new Template";
        
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/addNewTemplate.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
    }
    
    
    public function getThumbnail($params=[]){
        
        
        
        $actId = $params['id'];
        isset($params['rep'])? $rep = $params['rep'] : $rep = false;
        $SQL = "SELECT * FROM activity WHERE id='$actId'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if(!$res){
            echo 'ERROR';
        }
        $fetch = mysqli_fetch_assoc($res);
        $user_id = $fetch['owner_id'];
        $img = ROOTPATH . "/data/user/$user_id/activity/$actId/thumbnail.png";
        if($rep){
            $img = ROOTPATH . "/data/pub/activities/$actId/thumbnail.png";
        }
        //echo $img;
        if(!is_file($img))
        {
            $non_ecxiste = ROOTPATH. '/ui/Image-Not-Found1.png'; //FILE_DOES_NOT_EXISTS';
            $info = new SplFileInfo($non_ecxiste);
   
            $imageData = base64_encode(file_get_contents($non_ecxiste));

            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data: '.mime_content_type($non_ecxiste).';base64,'.$imageData;
            return  $src;
        }
        
        $info = new SplFileInfo($img);
   
        $imageData = base64_encode(file_get_contents($img));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data: '.mime_content_type($img).';base64,'.$imageData;

        // Echo out a sample image
        return  $src;
    }
    
    public function save($params=[]){
        $user_id = $_SESSION['username']; // TODO
        //print_r($params['xml']);
        $id = $params['id'];
        $xml = $params['xml'];
        isset($params['asNew'])?$asNew = true: $asNew = false;
        isset($params['asTemplate'])?$asTemplate = true: $asTemplate = false;
        isset($params['auto'])?$auto=1:$auto=0;
        $img = $params['image'];

        isset($params['repository'])? $repository = $params['repository']: $repository = false;
        
        isset($params['metadata'])? $metadata = json_decode($params['metadata'],true) : $metadata = null;


        if($asNew){
            if(!$repository){ //if not adding to repository
                $userName = $user_id;
            }
            else{
                $userName = '_REPOSITORY';

            }
                

            $date_time = date("dmyhis");
            $newId = $userName . $date_time;
            

            
            
            
            $db = new DBAccess();
            
            $SQL = "SELECT COUNT(*) as total FROM activity WHERE id LIKE '$newId%'";
            $res = $db->query($SQL);
            if(!$res){
                die('error 1');
            }
            $fetch = mysqli_fetch_assoc($res);
            $newId=$newId . $fetch['total'];
            
            //get metadata 
            $SQL = "SELECT * FROM activity WHERE id='$id'";
            $res = $db->query($SQL);
            if(!$res){
                die('error 2');
            }
            $fetch = mysqli_fetch_assoc($res);
            
            $name = $fetch['name'];
            if($metadata!=null){
                if(isset($metadata['name']))
                $name = $metadata['name'];
            }
            $antecedent  = $fetch['antecedent'];
            $behavior = $fetch['behavior'];
            $consequence  = $fetch['consequence'];
            $category  = $fetch['category'];
            
            $SQL = "INSERT INTO activity(id, owner_id,name, antecedent, behavior, consequence, category,active,auto) " 
                . "VALUES ('$newId', '$userName','$name', '$antecedent', '$behavior', '$consequence', '$category',1,$auto)";
            
            if(!$db->query($SQL)){
                die('error 3');
            }
            $dir_path = ROOTPATH . "/data/user/$user_id/activity/$newId/";
            if($userName == "_REPOSITORY"){
                $dir_path = ROOTPATH . "/data/pub/activities/$newId/";
            }
            if(!mkdir($dir_path, 0700)){
                die('Error creating directory');
            }
            
           
            
            $file_path = ROOTPATH . "/data/user/$user_id/activity/$newId/main.xml";
            if($userName == "_REPOSITORY"){
                $file_path = ROOTPATH . "/data/pub/activities/$newId/main.xml";
            }
            file_put_contents($file_path, $xml);
            
            //save image
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            //saving
            $fileName = $dir_path .'/thumbnail.png';
            file_put_contents($fileName, $data);
            
            isset($params['programId'])?$programId=$params['programId']:$programId=null;
            if($programId){
                $pc = new ProgramController();
                
                if($pc->addActivityToGroup_noEcho(['groupId'=>$programId,'activityId'=>$newId]) =="ERROR"){
                        echo "FILE_SAVE_ERROR";
                        return;
                }
            }
            if($auto){
                echo "FILE_SAVE_AUTO_OK";
            }
            else
                echo "FILE_SAVE_AS_NEW_OK";
                    
        }
        else{
            
            $json = json_decode($_POST['metadata'],true);
            $name = $json['name'];
            $antecedent = "";//$json['antecedent'];
            $behavior = '';//$json['behavior'];
            $consequence = "";//$json['consequence'];
            //$id = $json['id'];
            $difficult = $json['difficulty'];
            
            $SQL = "UPDATE activity SET name='$name', difficulty='$difficult'  WHERE id='$id'";
            $db = new DBAccess();
            if(!$db->query($SQL)){
                    die("error " . mysqli_error($db->con));
            }   

            $dir_path = ROOTPATH . "/data/user/$user_id/activity/preview/";
            if(!is_dir ($dir_path)){
                if(!mkdir($dir_path, 0700)){
                    die('Error creating directory');
                }
            }
            
            $dir_path = ROOTPATH . "/data/user/$user_id/activity/$id/";
            $file_path = ROOTPATH . "/data/user/$user_id/activity/$id/main.xml";
            file_put_contents($file_path, $xml);
            
            
            //save image
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            //saving
            $fileName = $dir_path .'/thumbnail.png';
            file_put_contents($fileName, $data);
            
            if($id=="preview"){
                
                echo "FILE_SAVE_PREVIEW_OK";
            }
            else{
                echo "FILE_SAVE_OK";
            }
        }
        
        
        
        
    }
    
    public function getReinforcers_json($params=[]){
        
        isset($params['limit'])?$limit = $params['limit']:$limit=10;
        isset($params['query'])?$query =$params['query']:$query = "";
        $offset = $params['offset'];
        $user = $_SESSION['username'];
        
        $SQL = "SELECT * FROM activity WHERE (owner_id='$user' OR owner_id='pub') AND active=1 AND category LIKE '%reinforcement%' AND NOT category LIKE '%template%' AND  (name LIKE '%$query%' OR antecedent LIKE '%$query%' OR behavior LIKE '%$query%' OR consequence LIKE '%$query%') LIMIT $limit OFFSET $offset";
        
        $db = new DBAccess();
        $res = $db->query($SQL);
        $activities = array();
        if(!$res)
        {
            echo "ERROR";
           // header("location:index.php");
        }
        while($fetch = mysqli_fetch_assoc($res))
        {
            if($fetch['owner_id']=='_REPOSITORY'){
                $fetch['thumb'] = $this->getThumbnail(['id'=>$fetch['id'],'rep'=>true]);    
            }
            else
                $fetch['thumb'] = $this->getThumbnail(['id'=>$fetch['id']]);
            array_push($activities, ($fetch));
        }
        echo json_encode($activities);
    }
    
   
    public function __construct($params=[]){
        parent::__construct();
        checkUser(["admin","professional","tutor"], BASE_URL);
    }
    
    public function getActivitieById_json($params=[]){
        
        isset($params['limit'])?$limit = $params['limit']:$limit=10;
        isset($params['query'])?$query =$params['query']:$query = "";
        isset($params['id'])?$act_id =$params['id']:$act_id = "";
        
        $offset = $params['offset'];
        $user = $_SESSION['username'];
        
        $SQL = "SELECT * FROM activity WHERE owner_id='$user' AND active=1 AND NOT category LIKE '%reinforcement%' AND  id='$act_id'";
        
        $db = new DBAccess();
        $res = $db->query($SQL);
        $activities = array();
        if(!$res)
        {
            echo "ERROR";
           // header("location:index.php");
        }
        while($fetch = mysqli_fetch_assoc($res))
        {
            array_push($activities, ($fetch));
        }
        echo json_encode($activities);
    }
    
    public function getActivities_json($params=[]){
        
        isset($params['limit'])?$limit = $params['limit']:$limit=10;
        isset($params['query'])?$query =$params['query']:$query = "";
        $offset = $params['offset'];
        $user = $_SESSION['username'];
        
        $SQL = "SELECT * FROM activity WHERE owner_id='$user' AND auto_guide= 0 AND auto= 0  AND active=1 AND NOT category LIKE '%reinforcement%' AND NOT category LIKE '%template%' AND  (name LIKE '%$query%' OR antecedent LIKE '%$query%' OR behavior LIKE '%$query%' OR consequence LIKE '%$query%' OR category LIKE '%$query%') LIMIT $limit OFFSET $offset";
        
        $db = new DBAccess();
        $res = $db->query($SQL);
        $activities = array();
        if(!$res)
        {
            echo "ERROR";
           // header("location:index.php");
        }
        while($fetch = mysqli_fetch_assoc($res))
        {
            if($fetch['owner_id']=='_REPOSITORY'){
                $fetch['thumb'] = $this->getThumbnail(['id'=>$fetch['id'],'rep'=>true]);    
            }
            else
            $fetch['thumb'] = $this->getThumbnail(['id'=>$fetch['id']]);
            array_push($activities, ($fetch));
        }
        echo json_encode($activities);
    }
    
    public function  index($params=[]){
        
        global $sel_lang;
        $data=[];
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $data['page_title'] = $lang['activities'];
        isset($params['query'])?$data['query']=$params['query']:$data['query']="";
        isset($params['page'])?$data['page']=$params['page']:$data['page']=0;
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/mainView.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
    }
    
    public function reinforcementIndex($params=[]){
        global $sel_lang;
        $data=[];
        
        isset($_POST['query'])?$data['query']=$_POST['query']:$data['query']="";
        isset($params['page'])?$data['page']=$params['page']:$data['page']=1;
        
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $data['page_title'] = $lang['activities'];
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/reinforcementMain.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
    }


    public function removeActivity($params=[]){
        $id = $_POST['activityId'];
        $page = $_POST['page'];
        $query = $_POST['query'];
        $user = $_SESSION['username'];
        $SQL = "UPDATE activity SET active=0 WHERE id='$id' AND owner_id='$user'";
        $db = new DBAccess();
        
        if(!$db->query($SQL))
        {
            die('error in sql: ' . mysqli_error($db->con));
        }
        echo $id;
        
    }
    
    public function filter_form($params=[]){
        global $sel_lang;
        $data=[];
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $data['page_title'] = $lang['activities'];
        isset($_POST['query'])?$data['query']=$_POST['query']:$data['query']="";
        isset($_POST['page'])?$_POST['page']=$_POST['page']:$data['page']=0;
        isset($_POST['rep'])? $rep = $_POST['rep']:$rep = 'false';
        $data['rep'] = $rep;
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/mainView.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
    }
    
    public function selectReinforcementTemplate($params=[]){
        global $sel_lang;
        $data=[];
        
        isset($_POST['query'])?$data['query']=$_POST['query']:$data['query']="";
        isset($params['page'])?$data['page']=$params['page']:$data['page']=1;
        
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $data['page_title'] = $lang['activities'];
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/selectReinforcement.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
        
    }
    
    public function selectTemplate($params=[]){
        global $sel_lang;
        $data=[];
        isset($params['groupId'])?$data['groupId']=$params['groupId']:$data['groupId']="";
        isset($_POST['query'])?$data['query']=$_POST['query']:$data['query']="";
        isset($params['page'])?$data['page']=$params['page']:$data['page']=1;
        
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $data['page_title'] = $lang['activities'];
        $this->loadView(ROOTPATH . "/ui/header.php",$data);
        $this->loadView("views/selectTemplate.php",$data);
        $this->loadView(ROOTPATH . "/ui/footer.php",$data);
        
    }
    
    
    public function newFromTemplate_submit($params=[]){
        $userName = $_SESSION['username'];
        $date_time = date("dmyhis");
        $id = $userName . $date_time;
        $templateId = $_POST['templateId'];
        $antecedent = $_POST['antecedent'];
        $behavior   = $_POST['behavior'];
        $consequence = $_POST['consequence'];
        $category = $_POST['category'];
        $name = $_POST['name'];
        $SQL = "INSERT INTO activity(id, owner_id,name, antecedent, behavior, consequence, category,active) " 
                . "VALUES ('$id', '$userName','$name', '$antecedent', '$behavior', '$consequence', '$category',1)";
        
        $db = new DBAccess();
        
        $source     = ROOTPATH . "/data/pub/templates/$templateId/main.xml";
        $dest_dir   = ROOTPATH . "/data/user/$userName/activity/$id/";
        $dest       = ROOTPATH . "/data/user/$userName/activity/$id/main.xml";
        
        
        
        if($db->query($SQL)){
            //copy file
            if(!mkdir($dest_dir, 0700)){
                die('Error creating directory');
            }
            if(!copy($source, $dest)){
                
                die('Error copying template');
            }
            
            header("location:index.php?action=edit&id=$id");
        }else
        {
             die('Error insertind in database');
        }
        
        
    }
    
    public function newFromTemplate($params=[]){
         
        $programId = "";
        if(isset($params['groupId'])){
            if(strlen($params['groupId'])>0){
                $programId = $params['groupId'];
            }
        }
        
        isset($params['auto'])?$auto='TRUE':$auto='FALSE';
        isset($params['auto_guide'])?$auto_guide='TRUE':$auto_guide="FALSE";
        $user = $_SESSION['username'];
        //copy template to user's directory.
        $reinforcement = false;
        isset($params['reinforcement'])?$reinforcement=true:$reinforcement=false;
         
        $userName = $_SESSION['username'];
        $date_time = date("dmyhis");
        $id = $userName . $date_time;
        $templateId = $params['templateId'];
        $antecedent = "";
        $behavior   = "";
        $consequence = "";
        $category = "";
        if($reinforcement){
            $category="reinforcement";
        }

        
        $owner_id = 'pub';

        $db = new DBAccess();
        $SQL="SELECT * FROM activity WHERE id='$templateId'";
        $res = $db->query($SQL);
        if($res){
            $fetch = mysqli_fetch_assoc($res);
            $owner_id = $fetch['owner_id'];

        }else{
            die("error > ActivityController::newFromTemplate: ".mysqli_error($db->con));
        }

        $name = "";
        $SQL = "INSERT INTO activity(id, owner_id,name, antecedent, behavior, consequence, category,active,auto,auto_guide) " 
                . "VALUES ('$id', '$userName','$name', '$antecedent', '$behavior', '$consequence', '$category',1,$auto,$auto_guide)";
        
        
        echo "owner id: ".$owner_id . "<br>";
        $source     = ROOTPATH . "/data/pub/templates/$templateId/main.xml";
        if($owner_id!='pub'){
            
            $source     = ROOTPATH . "/data/user/$owner_id/activity/$templateId/main.xml";
            echo "source path> " . $source . "<br>";
        }
        $dest_dir   = ROOTPATH . "/data/user/$userName/activity/$id/";
        $dest       = ROOTPATH . "/data/user/$userName/activity/$id/main.xml";
        
        
        
        if($db->query($SQL)){
            //copy file
            if(!mkdir($dest_dir)){
                die('Error creating directory ' . $dest_dir);
            }
            if(!copy($source, $dest)){
                
                die('Error copying template');
            }
            if(strlen($programId)>0){
                $progControl = new ProgramController();
                $p = [];
                $p['groupId'] = $programId;
                $p['activityId'] = $id;
                if($progControl->addActivityToGroup_noEcho($p)=="ERROR")
                {
                    die("error adding to program. Contact admin");
                }
            }
            
            header("location:". BASE_URL . "/activity/index.php?action=edit&id=$id&programId=$programId");
        }else
        {
             die('Error insertind in database');
        }
    }
    
    
    public function newFromTemplate_json($params=[]){
         
        $programId = "";
        if(isset($params['groupId'])){
            if(strlen($params['groupId'])>0){
                $programId = $params['groupId'];
            }
        }
        
        isset($params['auto'])?$auto='TRUE':$auto='FALSE';
        isset($params['auto_guide'])?$auto_guide='TRUE':$auto_guide="FALSE";
        $user = $_SESSION['username'];
        //copy template to user's directory.
        $reinforcement = false;
        isset($params['reinforcement'])?$reinforcement=true:$reinforcement=false;
         
        $userName = $_SESSION['username'];
        $date_time = date("dmyhis");
        $id = $userName . $date_time;
        $templateId = $params['templateId'];
        $antecedent = "";
        $behavior   = "";
        $consequence = "";
        $category = "";
        $name = "";
        $SQL = "INSERT INTO activity(id, owner_id,name, antecedent, behavior, consequence, category,active,auto,auto_guide) " 
                . "VALUES ('$id', '$userName','$name', '$antecedent', '$behavior', '$consequence', '$category',1,$auto,$auto_guide)";
        
        $db = new DBAccess();
        
        $source     = ROOTPATH . "/data/pub/templates/$templateId/main.xml";
        $dest_dir   = ROOTPATH . "/data/user/$userName/activity/$id/";
        $dest       = ROOTPATH . "/data/user/$userName/activity/$id/main.xml";
        
        
        
        if($db->query($SQL)){
            //copy file
            if(!mkdir($dest_dir, 0700)){
                die('Error creating directory');
            }
            if(!copy($source, $dest)){
                
                die('Error copying template');
            }
            if(strlen($programId)>0){
                $progControl = new ProgramController();
                $p = [];
                $p['groupId'] = $programId;
                $p['activityId'] = $id;
                if($progControl->addActivityToGroup_noEcho($p)=="ERROR")
                {
                    echo "ERROR";
                    //die("error adding to program. Contact admin");
                }
            }
            echo $id;
            //header("location:". BASE_URL . "/activity/index.php?action=edit&id=$id&programId=$programId");
        }else
        {
             echo "ERROR";
        }
    }
    
    public function updateMetadata($params=[]){
        
        $json = json_decode($_POST['metadata'],true);
        $name = $json['name'];
        $antecedent = "";//$json['antecedent'];
        $behavior = '';//$json['behavior'];
        $consequence = "";//$json['consequence'];
        $id = $json['id'];
        $difficult = $json['difficulty'];
        
        $SQL = "UPDATE activity SET name='$name', difficulty='$difficult'  WHERE id='$id'";
        $db = new DBAccess();
        if($db->query($SQL)){
            echo "OK";
        } 
        echo "Update...". $SQL . "<br>";
    }
    
    public function new($params=[]){
         global $sel_lang;
        $data=[];
        $data['groupId'] = '';
        if(isset($params['groupId'])){
            $data['groupId'] = $params['groupId'];
        }
        $data['page_title'] = "Activivites";
        require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
        $this->loadView(ROOTPATH . "/ui/header.php", ['page_title'=>$lang['filter_page_name']]);
        $this->loadView("views/newActivity.php", $data);
        $this->loadView(ROOTPATH . "/ui/footer.php", null);
    }
    
   
    public function runProgram($params=[]){
        
    }

    public function getAutoReinforcer_json($params=[]){
        
        $student_id = $params['student_id'];
        $SQL = "SELECT * FROM sessionprogram_activity_trial  WHERE result_data LIKE '%preferenceSelection%' AND student_id='$student_id' ORDER BY end_date LIMIT 1";
        $db = new DBAccess();
        $spa_id = $params['spa_id'];
        $ret = [];
        $ret['spa_id'] = $spa_id;
        $ret['has_ret'] = '0';


        $res = $db->query($SQL);
        
        if(!$res){
            die(mysqli_error($db->con));
        }

        if(mysqli_num_rows($res) <=0){  
            $ret['has_ret'] = '1';  
            //die("error");
            echo json_encode($ret);
            return;
        }

        $res = mysqli_fetch_assoc($res);
        
        $json_sess_res = json_decode($res['result_data'],true);


        $id = $json_sess_res['activity_id'];

        ///////////////////////////////////////////////////////
        
        $ret['prefs'] = $json_sess_res;


        $SQL = "SELECT * from activity WHERE  id='$id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        
        $fetch = mysqli_fetch_assoc($res);
        if(mysqli_num_rows($res) <=0 && strcmp($id, 'preview')!=0){  
            $ret['has_ret'] = '0';  
            //die("error");
            echo json_encode($ret);
            return;
        }
        $ret['activity_id'] = $fetch['id'];
        
        isset($fetch['owner_id'])? $user  = $fetch['owner_id']:$user ="pub"; 
        if($id=="preview"){
            $user=$_SESSION['username'];
        }
        ///return file or NULL;
        $file_path = ROOTPATH . "/data/user/$user/activity/$id/main.xml";
        if(is_file($file_path)){
            $file = file_get_contents($file_path);
            $ret['xml'] = $file;
        }
        else{
            $ret['xml'] = "NAO_E_ARQUIVO";
        }

        echo json_encode($ret);
    }

    public function saveScreenshot($params=[]){
        
        $img = $_POST['image'];
        echo "<br> ss recebida <br> $img";


        $student_id = $_POST['student'];

        $img_name = $_POST['key'].'.png';
        $dir_path = ROOTPATH . "/data/student/$student_id";

        $img = str_replace('data:image/png;base64,', '', $img);

        $img = str_replace(' ', '+', $img);
        echo $img;
        $data = base64_decode($img);
        //saving
        $fileName = $dir_path .'/' . $img_name;

        file_put_contents($fileName, $data);

        echo "SALVOU?";
    }

    public function getAutoReinforcer($params=[]){
        
        $student_id = $params['student_id'];
        $SQL = "SELECT * FROM sessionprogram_activity_trial  WHERE result_data LIKE '%preferenceSelection%' AND student_id='$student_id' ORDER BY end_date LIMIT 1";
        $db = new DBAccess();
        $res = $db->query($SQL);

        if(!$res){
            die(mysqli_error($db->con));
        }

        $res = mysqli_fetch_assoc($res);
        
        $json_sess_res = json_decode($res['result_data'],true);


        $id = $json_sess_res['activity_id'];

        ///////////////////////////////////////////////////////
        $ret = [];
        $ret['prefs'] = $json_sess_res;


        $SQL = "SELECT * from activity WHERE  id='$id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        $fetch = mysqli_fetch_assoc($res);
        if(mysqli_num_rows($res) <=0 && strcmp($id, 'preview')!=0){    
            die("error");
            
            
        }
        $ret['activity_id'] = $fetch['id'];
        
        isset($fetch['owner_id'])? $user  = $fetch['owner_id']:$user ="pub"; 
        if($id=="preview"){
            $user=$_SESSION['username'];
        }
        ///return file or NULL;
        $file_path = ROOTPATH . "/data/user/$user/activity/$id/main.xml";
        if(is_file($file_path)){
            $file = file_get_contents($file_path);
            $ret['xml'] = $file;
        }
        else{
            $ret['xml'] = "NAO_E_ARQUIVO";
        }

        echo json_encode($ret);
    }


    public function getActivity_json($params=[]){
        $id = $params['id'];
        
        //check if this activity belongs to current user or is pub
        $spa_id = $params['spa_id'];
       // if($spa_id==$id)
       //     $spa_id="REINFORCER";
        $SQL = "SELECT * from activity WHERE  id='$id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        $fetch = mysqli_fetch_assoc($res);
        if(mysqli_num_rows($res) <=0 && strcmp($id, 'preview')!=0){    
            die("getActivity_json. Atividade: $id Erro:".mysqli_error($db->con));
            
            
        }
        
        isset($fetch['owner_id'])? $user  = $fetch['owner_id']:$user ="pub"; 
        if($id=="preview"){
            $user=$_SESSION['username'];
        }
        ///return file or NULL;
        $file_path = ROOTPATH . "/data/user/$user/activity/$id/main.xml";
        if($fetch['owner_id']=='_REPOSITORY'){
            $file_path = ROOTPATH . "/data/pub/activities/$id/main.xml";

        }
        $json = [];
        if(is_file($file_path)){
            $file = file_get_contents($file_path);
            $json['xml'] = $file;
            $json['id'] = $id;
            $json['spa_id'] = $spa_id;
            echo json_encode($json);
        }
        else{
            echo "NAO_E_ARQUIVO";
        }
    }

    public function getActivity($params=[]){
        $id = $params['id'];
        
        //check if this activity belongs to current user or is pub
        
        $SQL = "SELECT * from activity WHERE  id='$id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        $fetch = mysqli_fetch_assoc($res);
        if(mysqli_num_rows($res) <=0 && strcmp($id, 'preview')!=0){    
            die("error");
            
            
        }
        
        isset($fetch['owner_id'])? $user  = $fetch['owner_id']:$user ="pub"; 
        if($id=="preview"){
            $user=$_SESSION['username'];
        }
        ///return file or NULL;
        $file_path = ROOTPATH . "/data/user/$user/activity/$id/main.xml";
        if(isset($fetch['owner_id'])){
            if($fetch['owner_id']=='_REPOSITORY'){
                $file_path = ROOTPATH . "/data/pub/activities/$id/main.xml";
            }

        }
        if(is_file($file_path)){
            $file = file_get_contents($file_path);
            echo $file;
        }
        else{
            echo "NAO_E_ARQUIVO";
        }
    }
    
    public function run($params=[]){
        $id = 0;
        if(isset($params['id']))
        {
            $id = $params['id'];
        }
        $data=[];
        $data["activity_id"] = $id;
        $data['instructions'] = [];
        $data['pagetitle'] = "Atividade";
        //$data['xml'] = $this->getActivity(['id'=>$id]);
        $arr = scandir("./instructions");
        $user = $_SESSION['username'];  
        foreach($arr as $el){
            if(strcmp($el, '.')!=0 && strcmp($el, '..')!=0){
                array_push($data['instructions'],"instructions/".$el);
            }
        }
        
        $this->loadView("views/runActivity.php",$data);
    }
    
    public function edit($params=[]){
        global $sel_lang;
        $data=[];
        
         $id = 0;
        if(isset($params['id']))
        {
            $id = $params['id'];
        }
      
        $data["activity_id"] = $id;
        $data['instructions'] = [];
        $data['pagetitle'] = "Editar Atividade";
        isset($params['programId'])?$data['programId'] = $params['programId']:$data['programId']="";
        //$data['xml'] = $this->getActivity(['id'=>$id]);
        $arr = scandir("./instructions");
        $user = $_SESSION['username'];  
        foreach($arr as $el){
            if(strcmp($el, '.')!=0 && strcmp($el, '..')!=0){
                array_push($data['instructions'],"instructions/".$el);
            }
        }
        
        $SQL = "SELECT * FROM activity WHERE  id='$id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        if($res){
            $res = mysqli_fetch_assoc($res);
            
            $data['activity_name'] = $res['name'];
            $data['activity_antecedent'] = $res['antecedent'];
            $data['activity_behavior'] = $res['behavior'];
            $data['activity_consequence'] = $res['consequence'];
            $data['activity_difficulty'] = $res['difficulty'];
        }
        else
        {
            die('error');
        }
        
        $data['activity_id'] =$id;
        
        if($res['auto_guide']){
            
            
            require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
            $pc=new ProgramController();
            
            //$pc->resetProgram(['programId'=>$data['programId']]);
            
            $pc->setAutoGuide(['programId'=>$data['programId'], 'guide'=>$data["activity_id"]]);
            
            $this->loadView(ROOTPATH . "/ui/header.php", ['page_title'=>$lang['filter_page_name']]);
            $this->loadView("views/editActivityAuto.php", $data);
            $this->loadView(ROOTPATH . "/ui/footer.php", null);
        }
        else{
            
            require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
            $this->loadView(ROOTPATH . "/ui/header.php", ['page_title'=>$lang['filter_page_name']]);
            $this->loadView("views/editActivity.php", $data);
            $this->loadView(ROOTPATH . "/ui/footer.php", null);
        }
    }
    
    
    public function listUserActivities($params=[]){
        
    }
    
    public function listPublicActivities($params=[]){
        
    }
}
