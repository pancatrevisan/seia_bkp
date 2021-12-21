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
  <link rel="shortcut icon" href="<?php echo BASE_URL;?>/ui/favicon.ico" />
  <script src="<?php echo BASE_URL;?>/external/jquery.min.js"></script>
  <script src="<?php echo BASE_URL;?>/external/popper.min.js"></script>
  <script src="<?php echo BASE_URL;?>/external/bootstrap.min.js"></script>
  <script  src="<?php echo BASE_URL;?>/external/face-api.js"></script>
  
  <script type="text/javascript" id="www-widgetapi-script" src="https://s.ytimg.com/yts/jsbin/www-widgetapi-vfl2dBoXz/www-widgetapi.js" async=""></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" >
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <link href="<?php echo BASE_URL;?>/external/enjoyhint/enjoyhint.css" rel="stylesheet">
  <script src="<?php echo BASE_URL;?>/external/enjoyhint/enjoyhint.js"></script>
</head>

<body class="">
<div id="booody" class="container-fluid">
<?php require(ROOTPATH ."/ui/menu.php")?>