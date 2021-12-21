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
        <?php if(isset($data['error']) && $data['error']){ ?>
            <div class="alert alert-danger" role="alert">
            Nome de usuário ou senha errados.
          </div>
        <?php } ?>
            
        
        <h2>Recuperação de senha</h2>
        
        <form action="index.php?action=sendRecoveryEmail" method="post">

        <div class="form-group">
            <label for="sigin_username">Nome de usuário ou e-mail</label>
            <input type="text" class="form-control" id="sigin_username" name="sigin_username">
        </div>

        

        <button type="submit" class="btn btn-primary btn-lg btn-block">Recuperar Senha</button>
        </form>
    </div>
</div>



</body>
</html>