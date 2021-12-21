<?php 
if(!defined('ROOTPATH')){
    require '../root.php';
}
//TODO: user login
$data['user_avatar']=BASE_URL . "/data/user/mu/avatar.jpg";
?>

<!DOCTYPE html>
<html lang="ptb">
<head>
  <title><?php echo $data['page_title']; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo BASE_URL;?>/activity/views/paper.css">
  <link rel="stylesheet" href="<?php echo BASE_URL;?>/external/bootstrap.min.css">
  <script src="<?php echo BASE_URL;?>/external/jquery.min.js"></script>
  <script src="<?php echo BASE_URL;?>/external/popper.min.js"></script>
  <script src="<?php echo BASE_URL;?>/external/bootstrap.min.js"></script>
  
  <script type="text/javascript" id="www-widgetapi-script" src="https://s.ytimg.com/yts/jsbin/www-widgetapi-vfl2dBoXz/www-widgetapi.js" async=""></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" >
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <link href="<?php echo BASE_URL;?>/external/enjoyhint/enjoyhint.css" rel="stylesheet">
  <script src="<?php echo BASE_URL;?>/external/enjoyhint/enjoyhint.js"></script>
</head>


<?php 
require_once ROOTPATH . '/utils/DBAccess.php';
$user_id = $_SESSION['username'];
$SQL = "";
$db = new DBAccess();
$user_id = $user_id;
$query = "";

$SQL = "SELECT *  FROM student LEFT JOIN student_tutorship  ON student.id=student_tutorship.student_id WHERE student_tutorship.professional_id ='$user_id'";
$res = $db->query($SQL);

?>

<body>
    <div class="row">
        <div class="col">
        <div class="alert">
                <a href="<?php echo BASE_URL;?>/auth/index.php?action=changePassword" type="button" class="mt-2 mn-2 btn btn btn-lg  btn-success  border border-dark"> Alterar senha </a>
            </div>
        </div>
    </div>
    <div class="row p-3">
        <div class="col">
            
            <div class="card-columns">
                <?php
                        while ($fetch = mysqli_fetch_assoc($res)) {
                ?>
                        <div class="card text-white bg-danger border-dark" id="<?php echo $fetch['id']; ?>">
                                <img class="img-fluid rounded img-thumbnail" width="100%" height="auto" src="<?php echo BASE_URL; ?>/data/student/<?php echo $fetch['student_id']; ?>/<?php echo $fetch['avatar']; ?>">
                                <h4 class="card-header border-dark"><?php echo $fetch['name']; ?></h4>
                                <div class="card-body">


                                    <div class="row">
                                        <div class="col">
                                            Nascimento:
                                        </div>
                                        <div class="col">
                                            <input class="form-control" type="date" disabled value="<?php echo $fetch['birthday']; ?>">
                                        </div>
                                    </div>

                                    <p class="card-text">Endereço: <?php echo $fetch['city'];
                                                                    echo " - " . $fetch['state']; ?></p>
                                    <p class="card-text">Medicação: <?php echo $fetch['medication']; ?></p>


                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col"><a href="<?php echo BASE_URL?>/sessionProgram/index.php?action=runCurriculum&studentId=<?php echo $fetch['student_id']; ?>" class="btn btn-block btn-success border border-dark">Iniciar Ensino</a></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

</body>