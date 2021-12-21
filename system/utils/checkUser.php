<?php



if(!defined('ROOTPATH')){
    require '../root.php';
}

function isGodMode(){
    return TRUE;
}

function checkUser($expected_roles, $redir_url){
    
    if(isset($_SESSION['username'])){
        
        if(in_array("athena",$expected_roles)){
            //echo "ATHENA!";
            //TODO: verificar se é god
            if(!$_SESSION['athena']==TRUE){
                header("location:$redir_url");
            }

        }
        else if(!in_array($_SESSION['role'],$expected_roles)){
            header("location:$redir_url");
        }
    }
    else{
        header("location:$redir_url");
    }
}

