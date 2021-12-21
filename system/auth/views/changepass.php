<?php
if (!defined('ROOTPATH')) {
    require '../root.php';
}

$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/auth/login.php";

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
</head>
<body>
  

<div class="col">
    <div class="container">
        
            
        
        <h2>Nova senha</h2>
        
        <form action="index.php?action=setNewPass" method="post" onsubmit="return checkPass()"  >

        <div class="form-group">
            <label for="new_pass">Nova senha</label>
            <input type="password" required class="form-control" id="new_pass" name="new_pass">
        </div>

        <div class="form-group">
            <label for="confirm_pass">Confirme a nova senha</label>
            <input type="password"  required class="form-control" id="confirm_pass" name="confirm_pass">
        </div>
        

        <button type="submit" class="btn btn-primary btn-lg btn-block">Alterar Senha</button>
        </form>
    </div>
</div>

<script>
function checkPass(){
    console.log("Check");
    var pass1 = document.getElementById("new_pass").value;
    var pass2 = document.getElementById("confirm_pass").value;

    if(pass1==pass2)
        return true;
    else{ 
        alert("As senhas não são iguais!");
        return false;
    }
    
}
</script>

</body>
</html>