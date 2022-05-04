<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (!defined('ROOTPATH')) {
    require '../root.php';
}


require_once ROOTPATH . '/core/Controller.php';
//require_once ROOTPATH . '/utils/DBAccess.php';
require_once ROOTPATH . '/auth/AuthController.php';

class InstallController extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index($args){
        
        
        $data = [];
        $data["page_name"] = "Install SEIA";
        $this->loadView('views/main.php',$data);
    }

    public function install($args){
        $db_host = $_POST['db_host'];
        $db_usr = $_POST['sigin_username'];
        $db_email = $_POST['signup_email'];
        $db_pass = $_POST['sigin_pass'];
        $con = mysqli_connect($db_host,  $db_usr, $db_pass);
        if (mysqli_connect_errno()) {
            $url = BASE_URL . "/install?action=accessError";   
            header("location:$url");
            die();
        }
        
        echo "<h1> SQL</h1>";
        //tudo ok  com o bd. instalar. 
        $location = "./empty_db.sql";
        $commands = file_get_contents($location);   
        echo $commands;
        echo "<br> <br>";
        $con->query("CREATE DATABASE seiadatabase");
        $con->select_db("seiadatabase");
        echo $con->multi_query($commands);
        if(mysqli_error($con)){
            echo "<br> " . $con->error;
        }
        else {
            echo "sem erro? ";
            print_r($con);
        }

        $location = "./db_class_template";
        $template = file_get_contents($location);  

        //alterar o script de criação do objeto do DB.
        $template = str_replace("XXHOSTXX", $db_host,$template);
        $template = str_replace("XXUSERXX", $db_usr,$template);
        $template = str_replace("XXPASSXX", $db_pass,$template);
        $template = str_replace("XXDBXX", 'seiadatabase',$template);

        echo "<br> <br>" .$template;
        $params = ["first_admin"=> TRUE, 
        "signup_username" => $db_usr,
        "signup_email" => $db_email,
        "signup_pass"  => $db_pass
        ];
        $auth = new AuthController();
        $auth->newUser($params);


    }
    public function finishInstall($args){

    }

    public function accessError($args){
        $this->loadView('views/accesserror.php',null);

    }
}