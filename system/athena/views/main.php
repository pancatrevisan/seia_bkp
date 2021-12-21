<?php 
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';
require_once ROOTPATH . '/ui/modal.php';
checkUser(["professional","admin", "athena"], BASE_URL);

$user_id = $_SESSION['username'];

?>

<div class="row mt-3 mb-3">
    <div class="col-2 bg-secondary p-3">
        <div class="row" >
            <div class="col">
            <img class="img-fluid rounded " width="100%" height="auto" src="<?php echo BASE_URL;?>/data/user/<?php echo $user_id;?>/avatar.png">
            </div>
        </div>
    </div>
    <div class="col-10">
        <div class="container">
            
         
            <div class="card-columns mt-3">
                
                
                
                <div class="card  bg-secondary text-light" id="tutoEstudantes">
                    <div class="card-header">Usuários</div>
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar Todos Usuários</p>
                        <a  href="<?php echo BASE_URL;?>/athena/index.php?action=users" class="btn btn btn-block  btn-danger border border-dark">Usuários</a>
                    </div>
                </div>

                <div class="card  bg-secondary text-light" id="tutoEstimulos">
                    <div class="card-header">Estímulos</div>
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar estímulos cadastrados</p>
                        <a  href="<?php echo BASE_URL; ?>/athena/index.php?action=stimuli" class="btn btn btn-block  btn-primary border border-dark">Estímulos</a>
                    </div>
                </div>
                
                
                <div class="card  bg-secondary text-light" id="tutoAtividades">
                    <div class="card-header ">Atividades</div>
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar Atividades cadstradas</p>
                        <a href="<?php echo BASE_URL ;?>/athena/index.php?action=viewUserActivities&user=ALL" class="btn btn btn-block  btn-warning border border-dark">Atividades</a>
                    </div>
                </div>

                <div class="card  bg-secondary text-light" id="tutoAtividades">
                    <div class="card-header ">Estudantes</div>
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar Estudantes Cadastrados</p>
                        <a href="<?php echo BASE_URL ;?>/athena/index.php?action=viewUserStudents&user=ALL" class="btn btn btn-block  btn-success border border-dark">Estudantes</a>
                    </div>
                </div>
                <div class="card  bg-secondary text-light" id="tutoAtividades">
                    <div class="card-header ">Estatísticas gerais</div>
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar Estatísticas </p>
                        <a href="<?php echo BASE_URL ;?>/athena/index.php?action=generalStats" class="btn btn btn-block  btn-info border border-dark">Visualizar</a>
                    </div>
                </div>
               
            </div>
        </div>
    </div>

    </div>

    

    <div id='help' style="position: absolute; top:5px; right: 30px;" >
    <button class='btn btn-block btn-lg btn-warning' onclick="showHelp()"><i class="fas fa-question"></i></button>

    </div>
</div>