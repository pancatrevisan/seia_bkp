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
        $admin_user = $_POST['admin_username'];
        $admin_pass = $_POST['admin_pass'];
        $con = mysqli_connect($db_host,  $db_usr, $db_pass);
        if (mysqli_connect_errno()) {
            $url = BASE_URL . "/install?action=accessError";   
            header("location:$url");
            die();
        }
        //tudo ok  com o bd. instalar. 
        $location = "./empty_db.sql";
        $commands = file_get_contents($location);   
        
        $con->query("CREATE DATABASE seiadatabase");
        $con->select_db("seiadatabase");
        //criação das tabelas.
        $res = $con->multi_query($commands);
        if(mysqli_error($con)){
            die($con->error);
        }
        //percorrer o resultado...
        do {
            /* store first result set */
            if ($result = $con->store_result()) {
                while ($row = $result->fetch_row()) {
                    printf("%s\n", $row[0]);
                }
                $result->free();
            }
            /* print divider */
            if ($con->more_results()) {
                printf("-----------------\n");
            }
        } while ($con->next_result());

        $con->close();

        //cria o script de conexão com o BD
        $location = "./db_class_template";
        $template = file_get_contents($location);  
        echo "<br><BR> <h2> " . ROOTPATH . "</h2>";
        //alterar o script de criação do objeto do DB.
        $template = str_replace("XXHOSTXX", $db_host,$template);
        $template = str_replace("XXUSERXX", $db_usr,$template);
        $template = str_replace("XXPASSXX", $db_pass,$template);
        $template = str_replace("XXDBXX", 'seiadatabase',$template);

        $out_file_name = ROOTPATH . "/utils/DBAccess.php";
        file_put_contents($out_file_name , $template);

        //cria o usuario admin.
        $params = ["first_admin"=> TRUE, 
        "signup_username" => $admin_user,
        "signup_email" => $db_email,
        "signup_pass"  => $admin_pass
        ];
        
        require_once ROOTPATH . '/auth/AuthController.php';
        $auth = new AuthController();
        $auth->newUser($params);

        $url = BASE_URL . "/install?action=finishInstall";   
        header("location:$url");
        die();


    }
    public function finishInstall($args){
        $oldfile =  ROOTPATH . '/install';
        $newfile = ROOTPATH . '/install_bkp';
        //so funciona no linux?
        rename($oldfile,$newfile);

        $this->loadView('views/installComplete.php',null);
    }

    public function accessError($args){
        $this->loadView('views/accesserror.php',null);

    }
}