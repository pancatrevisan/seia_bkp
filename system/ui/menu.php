<?php 
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
$sel_lang = "ptb";
if(!defined('ROOTPATH')){
    require '../root.php';
}
require ROOTPATH . '/lang/' . $sel_lang . "/menu.php";
require_once ROOTPATH . '/utils/GetData.php';


require_once ROOTPATH . '/utils/checkUser.php';
checkUser(["professional","admin","student"], BASE_URL);


$user_id = $_SESSION['username'];
?>


<!-- Menu -->
<div class="row" id="menu">
<div class="col p-0">
    <div class="pos-f-t ">

        <nav class="navbar navbar-dark bg-dark">
          <button id="UIMenu" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>Menu
          </button>
        </nav>
    </div>
    <div class="collapse" id="navbarToggleExternalContent">
            <div class="bg-dark p-4">
            <div class="row">
                <div class="col-sm-2">
                    <a class="" href="<?php echo BASE_URL. "/professional"; ?> "><img class="img-fluid rounded" src="<?php echo BASE_URL;?>/data/user/<?php echo $user_id;?>/avatar.png"> <br></a>
                </div>
                <div class="col-sm-10">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
                                    
                                    <!-- Links -->
                                    <ul class="navbar-nav">
                                        <li class="nav-item">
                                            <a id="UI-stimuli" class=" btn btn-outline-primary ml-2" href="<?php echo BASE_URL . "/stimuli";?>"><h3><?php echo $lang["stimuli"];?></h3></a>
                                        </li>
                                        <li id="UI-activity"class="nav-item">
                                            <a class=" btn btn-outline-warning ml-2" href="<?php echo BASE_URL . "/activity";?>"> <h3><?php echo $lang["activities"];?></h3></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class=" btn btn-outline-danger ml-2" href="<?php echo BASE_URL . "/professional?action=student";?>"><h3><?php echo $lang["students"];?></h3></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class=" btn btn-outline-success ml-2" href="<?php echo BASE_URL . "/activity/index.php?action=reinforcementIndex";?>"><h3>Reforços</h3></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class=" btn btn-outline-secondary ml-2" href="<?php echo BASE_URL . "/activity/index.php?action=userTemplateIndex";?>"><h3>Templates</h3></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            </div>
      </div>
    <!-- Menu -->
</div>
</div>