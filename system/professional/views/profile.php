<?php 
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["professional","admin"], BASE_URL);

$user_id = $_SESSION['username'];
?>
<div class="row mt-3 mb-3">
    <div class="col-2 bg-secondary p-3">
        <div class="row" >
            <div class="col">
            <img class="img-fluid rounded " width="100%" height="auto" src="<?php echo BASE_URL;?>/data/user/<?php echo $user_id;?>/avatar.png">
            <a href="<?php echo BASE_URL;?>/professional/index.php?action=profile" type="button" class="mt-2 mn-2 btn btn btn-block  btn-danger border border-dark"> Meu perfil </a>
            <a href="<?php echo BASE_URL;?>/auth/index.php?action=logout" type="button" class="mt-2 mn-2 btn btn btn-block  btn-danger border border-dark"> Sair </a>
            </div>
        </div>
    </div>
    <div class="col-10">
        <div class="container">
            
         
            <div class="card-deck mt-3">
                <div class="card  bg-light">
                    <div class="card-header">Estudantes</div>
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar/cadastrar estudantes</p>
                        <a href="index.php?action=student" class="btn btn btn-block  btn-danger border border-dark">Estudantes</a>
                    </div>
                </div>
                
                <div class="card  bg-light">
                    <div class="card-header ">Atividades</div>
                    <div class="card-body">
                        
                        <p class="card-text">Crie ou Edite Atividades.</p>
                        <a href="<?php echo BASE_URL . "/activity";?>" class="btn btn btn-block  btn-warning border border-dark">Atividades</a>
                    </div>
                </div>
                
                <div class="card  bg-light">
                    <div class="card-header ">Reforços</div>
                    <div class="card-body">
                        
                        <p class="card-text">Crie ou Edite Reforços.</p>
                        <a href="<?php echo BASE_URL . "/activity/index.php?action=reinforcementIndex";?>" class="btn btn-success  btn-block border-dark">Reforços</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>