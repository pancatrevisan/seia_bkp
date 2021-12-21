<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

//supress mail warning...
error_reporting(E_ALL ^ E_WARNING); 

if(!defined('ROOTPATH')){
    require '../root.php';
}



if(!defined('ROOTPATH')){
    require '../root.php';
}
require ROOTPATH . '/core/Controller.php';
require_once ROOTPATH . '/professional/ProfessionalController.php';
require_once ROOTPATH . '/utils/DBAccess.php';
$sel_lang = 'ptb';

class AuthController extends Controller
{
    public function setNewPass($params=[]){
        
        

        $username = $_SESSION['username'];
        $new_pass = $_POST['new_pass'];
        $hash =password_hash($new_pass,PASSWORD_BCRYPT );
        $SQL= "UPDATE user SET pass='$hash' WHERE username='$username'";
        $db = new DBAccess();
        if(!$db->query($SQL)){
            die("error: AuthController::sendRecoveryEmail " . mysqli_error($db->con));
        }

        session_destroy();
        $_SESSION['username'] = "";
        $_SESSION['role'] = "";

        $data = [];
        $data["page_name"] = "Senha alterada";
        $this->loadView('views/newpassok.php',$data);

    }
    public function __construct(){
        
    }
    private function randomPassword($pass_size) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $pass_size; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function changePassword($params=[]){
        global $sel_lang;
        require_once ROOTPATH . '/lang/' . $sel_lang . "/auth/login.php";
        $data = [];
        isset($param['error'])?$data['error']=true:$data['error']=false;
        $data["page_name"] = $lang['page_name'];
        $this->loadView('views/changepass.php',$data);   
    }

    public function findProfessional_json($pramas=[]){
        $id_or_email = $_POST['nameOrEmail'];
        $db = new DBAccess();

        $SQL = "SELECT name, username, email, role FROM user WHERE role='professional' AND (name LIKE '%$id_or_email%' OR email='$id_or_email')";
        $res = $db->query($SQL);
        if(!$res){
            die("error. " . mysqli_error($db->con));
        }
        $ret = [];
        while($fetch = mysqli_fetch_assoc($res)){
            array_push($ret, $fetch);
        }
        
        echo json_encode($ret);

    }

    public function findTutor_json($pramas=[]){
        $id_or_email = $_POST['nameOrEmail'];
        $db = new DBAccess();

        $SQL = "SELECT name, username, email, role FROM user WHERE role='tutor' AND (name LIKE '%$id_or_email%' OR email='$id_or_email')";
        $res = $db->query($SQL);
        if(!$res){
            die("error. " . mysqli_error($db->con));
        }
        $ret = [];
        while($fetch = mysqli_fetch_assoc($res)){
            array_push($ret, $fetch);
        }
        
        echo json_encode($ret);

    }
    public function sendRecoveryEmail($params=[]){
         isset($_POST['sigin_username'])? $name_or_email = $_POST['sigin_username']:$name_or_email = $params['sigin_username'] ;
        $json = isset($params['json']);
        $SQL = "SELECT * FROM user WHERE username='$name_or_email' OR email='$name_or_email'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        
        $res = mysqli_fetch_assoc($res);
        if(!$res){
            if(!$json)
                echo "<br>usuário nao existente";
            return;
        }
        else{
            $username = $res['username'];
            $new_pass = $this->randomPassword(10);
            $hash =password_hash($new_pass,PASSWORD_BCRYPT );
            $SQL= "UPDATE user SET pass='$hash' WHERE username='$username'";
            $db = new DBAccess();
            if(!$db->query($SQL)){
                die("error: AuthController::sendRecoveryEmail " . mysqli_error($db->con));
            }
            $to_email_address = strtolower($res['email']);
            $subject = "Nova senha de acesso ao SEIA";
            $message = "Seu login no SEIA é '$username' e sua nova senha é '$new_pass'. Você pode utilizá-la para fazer login. Recomenda-se alterar a senha.";
            $headers = "From: webmaster@seia.com.br\n";
            $emailsender = "webmaster@seia.com.br";
            $quebra_linha = "\n";

            if(!mail($to_email_address, $subject, $message, $headers ,"-r".$emailsender)){ // Se for Postfix
                $headers .= "Return-Path: " . $emailsender . $quebra_linha; // Se "não for Postfix"
                mail($to_email_address, $subject, $message, $headers );
            }


///            REMOVER
/*
            $myfile = fopen("PASS.txt", "w") or die("Unable to open file!");
            $txt = $new_pass;
            fwrite($myfile, $txt);
            fclose($myfile);
*/

/////REMOVER

            //if(!mail($to_email_address,$subject,$message)){
                
            //else{
                if(!$json){
                    $data['user_email'] = $res['email'];
                $this->loadView('views/new_pass_set.php',$data);
                }else{
                    return "OK";
                }
                
            //}
            

        }
        
    }

    
    public function passRecovery($param=[]){
        global $sel_lang;
        require_once ROOTPATH . '/lang/' . $sel_lang . "/auth/login.php";
        $data = [];
        isset($param['error'])?$data['error']=true:$data['error']=false;
        $data["page_name"] = $lang['page_name'];
        $this->loadView('views/recovery.php',$data);
    }


    public function loginForm($param=[]){
        global $sel_lang;
        require_once ROOTPATH . '/lang/' . $sel_lang . "/auth/login.php";
        $data = [];
        isset($param['error'])?$data['error']=true:$data['error']=false;
        $data["page_name"] = $lang['page_name'];
        $this->loadView('views/login.php',$data);
    }
  
    
    public function login($param=[]){
        global $sel_lang;
        require_once ROOTPATH . '/lang/' . $sel_lang . "/auth/login.php";
        $data = [];
        $data["page_name"] = $lang['page_name'];
        $username = $_POST['sigin_username'];
        $pass   = $_POST['sigin_pass'];
        $SQL = "SELECT * FROM user WHERE username='$username'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        $res = mysqli_fetch_assoc($res);
        
        if(password_verify($pass,$res['pass']))
        {
            
            $_SESSION['username'] =  strtolower($username);
            $_SESSION['role'] = $res['role'];
            $_SESSION['tuto_finished'] = $res['tuto_finished'];
            $_SESSION['athena'] = $res['athena'];
            $this->saveLogin();
            if(isset($_SESSION['username'])){
                if($_SESSION['role']=="professional"){
                    $url = BASE_URL . "/professional";   
                    header("location:$url"); 
                }
                else if($_SESSION['role']=="student"){
                    $url = BASE_URL . "/student";   
                    header("location:$url");
                }
                else if($_SESSION['role']=="admin"){
                    $url = BASE_URL . "/admin";   
                    header("location:$url");
                }
                else if($_SESSION['role']=="tutor"){
                    $url = BASE_URL . "/tutor";   
                    header("location:$url");
                }
            }
        }
        else{
            $url = BASE_URL . "/auth?action=loginForm&error=true";   
            header("location:$url");
        } 
       echo "SQL: $SQL <br>";
        print_r($res);
        //$this->loadView("views/login.php',$data);
    }
    
    protected function saveLogin($params=[]){
        $user_id = $_SESSION['username'];
        $SQL = "SELECT count(*) as total FROM user_login where username='$user_id'";
        $db = new DBAccess();
        $res = $db->query($SQL);
        
        if(!$res){
            die('error checking user logins.' . mysqli_error($db->con));

        }
        $res = mysqli_fetch_assoc($res);
        $total = (int) $res['total'];
        if($total <= 0){
            $_SESSION['first_login']= true;
        }
        $SQL = "INSERT INTO user_login(username) VALUES ('$user_id')";
        if(!$db->query($SQL)){
            die("error adding user login");
        }    
    }

    public function showTherms($params=[])
    {
        global $sel_lang;
        require_once ROOTPATH . '/lang/' . $sel_lang . "/auth/login.php";
        $data = [];
        $data["page_name"] = "Termos de Uso";
        $this->loadView('views/therms.php',$data);
    }
    
    public function newUserForm($param=[]) {
        global $sel_lang;
        require_once ROOTPATH . '/lang/' . $sel_lang . "/auth/login.php";
        $data = [];
        $data["page_name"] = $lang['page_name'];
        $this->loadView('views/new_user.php',$data);
    }
    public function  logout($param=[]){
        session_destroy();
        $_SESSION['username'] = "";
        $_SESSION['role'] = "";
        echo "era para destrui sessao";
        $url = BASE_URL . "/professional";   
        header("location:$url");
    }


    public function newTutor_json($params=[]){

        

        
        $data = json_decode($_POST['metadata'], true);
        
        $username   = $data['signup_username'];
        $name       = $data['signup_name'];
        $email      = $data['signup_email'];
        $pass       = "teste";
        $pass = password_hash($pass,PASSWORD_BCRYPT );
        $city       = $data['signup_city'];
        $comment    = "";
        $role       = 'tutor';
        $student_id = $data['student_id'];

        //checks if user exists
        $db = new DBAccess();

        $SQL = "SELECT COUNT(*) AS total FROM user WHERE email='$email' OR username='$username'";
        $res = $db->query($SQL);
        if($res){
            $fetch = mysqli_fetch_assoc($res);
            if($fetch['total']>0){
                //user already exists.
                


                $pc = new ProfessionalController(['newUser'=>true]);
                $pc->addStudentTutorship(['professional_id'=>$username,'student_id'=>$student_id]);

                echo "ALREADY_EXISTS_OK";
                return;
            }
        }
        else{
            die("error: AuthController::newUser " . mysqli_error($db->con));
            return;
        }




        $SQL = "INSERT INTO user(username, name, email, pass, city, comment,role, active) " .
                "VALUES ('$username', '$name','$email','$pass','$city','$comment','$role', FALSE)";
        
        
        if($db->query($SQL)){
            $username   =strtolower( $data['signup_username']);
            $professionalDir = ROOTPATH . "/data/user/$username";
            if(!mkdir($professionalDir)){
                die('Error creating directory');
            }
            
            //stimuli dir
            $professionalDir = ROOTPATH . "/data/user/$username/stimuli";
            if(!mkdir($professionalDir)){
                die('Error creating directory');
            }
            
            //activity dir
            $professionalDir = ROOTPATH . "/data/user/$username/activity";
            if(!mkdir($professionalDir)){
                die('Error creating directory');
            }
            
            $src = ROOTPATH . "/data/pub/avatars/professional.png";
            $dst = ROOTPATH . "/data/user/$username/avatar.png";
            copy($src,$dst);

            $pc = new ProfessionalController(['newUser'=>true]);
            $pc->addStudentTutorship(['professional_id'=>$username,'student_id'=>$student_id]);

            
            if($this->sendRecoveryEmail(['sigin_username'=>$username,'json'=>true]) != "OK"){
                die( "ERROR_SENDING_PASS");
                
            }
                
                
            echo("NEW_USER_OK");
            return;

        }
        else{
            die("ERROR");
            
        }
        echo "USER_INSERT_OK";
    }

    public function newUser($param=[]){
        $data = [];
        if(!isset($_POST['signup_username']))
        {
            header('location:index.php?action=newUserForm');
        }
        $username   = $_POST['signup_username'];
        $name       = $_POST['signup_name'];
        $email      = $_POST['signup_email'];
        $pass       = $_POST['signup_pass'];
        $pass = password_hash($pass,PASSWORD_BCRYPT );
        $city       = $_POST['signup_city'];
        $comment    = $_POST['signup_comment'];
        $role       = 'professional';

        //checks if user exists
        $db = new DBAccess();

        $SQL = "SELECT COUNT(*) AS total FROM user WHERE email='$email' OR username='$username'";
        $res = $db->query($SQL);
        if($res){
            $fetch = mysqli_fetch_assoc($res);
            if($fetch['total']>0){
                //user already exists.
                $message['message'] = "O nome de usuário ou email já está cadastrado! Se esqueceu a senha, " .
                
                '<a class="text-danger" href="'.  BASE_URL  .'/auth/index.php?action=passRecovery>" recupera-a</a>';
                $this->loadView('views/new_user_fail.php',$message);
                return;
            }
        }
        else{
            die("error: AuthController::newUser " . mysqli_error($db->con));
            return;
        }




        $SQL = "INSERT INTO user(username, name, email, pass, city, comment,role, active) " .
                "VALUES ('$username', '$name','$email','$pass','$city','$comment','$role', FALSE)";
        
        
        if($db->query($SQL)){
            $username   =strtolower( $_POST['signup_username']);
            $professionalDir = ROOTPATH . "/data/user/$username";
            if(!mkdir($professionalDir)){
                die('Error creating directory');
            }
            
            //stimuli dir
            $professionalDir = ROOTPATH . "/data/user/$username/stimuli";
            if(!mkdir($professionalDir)){
                die('Error creating directory');
            }
            
            //activity dir
            $professionalDir = ROOTPATH . "/data/user/$username/activity";
            if(!mkdir($professionalDir)){
                die('Error creating directory');
            }
            
            $src = ROOTPATH . "/data/pub/avatars/professional.png";
            $dst = ROOTPATH . "/data/user/$username/avatar.png";
            copy($src,$dst);
            $pc = new ProfessionalController(['newUser'=>true]);

            $data = ['studentName'=>'Estudante Exemplo', 'birthday'=>'2012-02-02','city'=>'Endereço','state'=>'MS','sex'=>'male',
            'medication'=>'Nenhum','username'=>$username];
            $pc->newStudentFromData($data);
            $this->loadView('views/new_user_success.php',$data);
        }
        else{
            $this->loadView('views/new_user_fail.php',$data);
            
        }
        
    }
    
    public function index($param=[]){
        
        
        $data = [];
        
        if(!isset($_SESSION['username'])){
            $this->loadView('views/main.php',$data);
        }
        else if(isset($_SESSION['username'])){
            if($_SESSION['role']=="professional"){
                $url = BASE_URL . "/professional";   
                header("location:$url"); 
            }
            else if($_SESSION['role']=="student"){
                $url = BASE_URL . "/student";   
                header("location:$url");
            }
            else if($_SESSION['role']=="admin"){
                $url = BASE_URL . "/admin";   
                header("location:$url");
            }
        }
        
     
        
        
    }
    
}