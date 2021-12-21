<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require '../root.php';
}


require_once ROOTPATH . '/utils/checkUser.php';




require_once ROOTPATH . '/utils/DBAccess.php';

if(isset($_GET['type']))
{
    checkUser(["professional","admin","tutor"], BASE_URL);
    if( ($_GET['type']=='image') || ($_GET['type']=='audio'))
    {
        if(isset($_GET['id']))
            getImage($_GET['id']);
    }
    elseif($_GET['type']=='stimuli')
    {
        if(isset($_GET['id']))
            getStimuli($_GET['id']);
    }
}

function getVideo($params=[]){
    checkUser(["professional","admin","student"], BASE_URL);
}

function getAudio($params=[]){
    checkUser(["professional","admin","student"], BASE_URL);
}

function getStimuli($id){
    $user = $_SESSION['username']; //TODO:
    $db = new DBAccess();
    $SQL = "SELECT * FROM stimuli WHERE id='$id'";
    $res = $db->query($SQL);
    if(mysqli_num_rows($res) > 0){
        //stimuli found
        
        $res =  mysqli_fetch_assoc($res);
        
        if(!isset($_GET['only_db'])){

            
            if($res['type']=='image'){
                $res['data'] = getData($res['url']);

                unset($res['url']);
            }
        }
        
        echo json_encode($res);
    }
    else
    {
        echo 'STIMULI_NOT_FOUND';
    }
    
}



function getData($path){
    $img = ROOTPATH . $path;//'/data/user/mu/stimuli/mu200719030053.jpg';
        
    if(!is_file($img))
    {
        
        return 'FILE_DOES_NOT_EXISTS';
    }
    $info = new SplFileInfo($img);

    $imageData = base64_encode(file_get_contents($img));

    // Format the image SRC:  data:{mime};base64,{data};
    $src = 'data: '.mime_content_type($img).';base64,'.$imageData;

    // Echo out a sample image
    return  $src;

}

/**
 * 
 * @param type $params put the id with key = id :D
 * @return string
 */
function getImage($id){
        
        
    
        $img = ROOTPATH . '/data/user/mu/stimuli/mu200719030053.jpg';
        
        if(!is_file($img))
        {
            echo 'FILE_DOES_NOT_EXISTS';
            return;
        }
        $info = new SplFileInfo($img);
   
        $imageData = base64_encode(file_get_contents($img));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data: '.mime_content_type($img).';base64,'.$imageData;

        // Echo out a sample image
        echo  $src;
    }

?>