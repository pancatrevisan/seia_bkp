<?php
if (!defined('ROOTPATH')) {
    require '../root.php';
}

$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/auth/new_user_success.php";

?>
<!DOCTYPE html>
<html lang="ptb">
<head>
  <title><?php echo $lang['page_name'];?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
  

<div class="col">
    <div class="container">
        <h2 class='jumbotron'><?php echo $lang['user_created_fail'];?></h2>
        
        
    </div>
</div>



</body>
</html>