<?php
if (!defined('ROOTPATH')) {
    require '../root.php';
}

$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/auth/new_user.php";

?>
<!DOCTYPE html>
<html lang="ptb">
<head>
  <title>Cadastro</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  
</head>
<body>
  

<div class="col">
    <div class="container">
        <h2><?php echo $lang['new_user'];?> </h2>
        <p> <?php echo $lang['info'];?></p>
        <p><?php echo $lang['info2'];?></p>
        <form action="index.php?action=newUser" method="post" onsubmit="return validate(this);" >
            
        <div class="form-group">
            <label for="signup_username"><?php echo $lang['username'];?></label>
            <input required type="text" class="form-control" id="signup_username" name="signup_username">
        </div>
        <div class="form-group">
            <label for="signup_name"><?php echo $lang['name'];?></label>
            <input required type="text" class="form-control" id="signup_name" name="signup_name">
        </div>

        <div class="form-group">
            <label for="signup_email"><?php echo $lang['email'];?></label>
            <input required type="email" class="form-control" id="signup_email" name="signup_email">
        </div>

        <div class="form-group">
            <label for="signup_pass"><?php echo $lang['pass'];?></label>
            <div class="input-group" id="show_hide_password">
                <input required type="password" class="form-control" id="signup_pass" name="signup_pass">
                <div class="input-group-append">
                    <span class="input-group-text"><a href="" style="color:black"><i class="fa fa-eye-slash" aria-hidden="true"></i></a></span>
                </div>
            </div>
        </div>

         <div class="form-group">
            <label for="signup_city"><?php echo $lang['city'];?></label>
            <input required type="text" class="form-control" id="signup_city" name="signup_city">
        </div>
            
        <div class="form-group">
            <label for="signup_comment"><?php echo $lang['presentation'];?></label>
            <textarea required class="form-control" rows="5" id="signup_comment" name="signup_comment"></textarea>
        </div>
            
        

        <button type="submit" class="btn btn-primary btn-lg btn-block"><?php echo $lang['button_signup'];?></button>
        </form>
    </div>
</div>





<script>
    function validate(){
        console.log("validade...");
        var format = /[áàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        var username = document.getElementById('signup_username');
       
       console.log("user name> " + username.value);
       console.log("user name test: " + format.test(username.value));
       if(format.test(username.value)){
           alert("O nome de usuário só pode conter letras e números (sem acentuação)");
            return false;
       }
        
       return true;
        
    }
$(document).ready(function() {
    
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );        
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
});
</script>

</body>
</html>