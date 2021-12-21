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
checkUser(["professional","admin"], BASE_URL);

$user_id = $_SESSION['username'];

?>



<div class="row mt-3 mb-3">
    <div class="col-2 p-3">
        <div class="row" >
            <div class="col">
            
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
                        <a  href="<?php echo BASE_URL;?>/dataexport/index.php?action=students" class="btn btn btn-block  btn-danger border border-dark">Estudantes</a>
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
