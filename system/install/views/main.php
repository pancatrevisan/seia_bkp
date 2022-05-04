<?php
if (!defined('ROOTPATH')) {
    require '../root.php';
}
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
        <h2>Instalação do SEIA</h2>
        
        <form action="index.php?action=install" method="post">
        <div class="form-group">
            <label for="db_host">Host do Banco</label>
            <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="sigin_username">Usuário do BD</label>
            <input type="text" class="form-control" id="sigin_username" name="sigin_username" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="sigin_pass">Senha</label>
            <input type="password" class="form-control" id="sigin_pass" name="sigin_pass" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="admin_username">Usuário do Administrador</label>
            <input type="text" class="form-control" id="admin_username" name="admin_username" autocomplete="off">
        </div>
        
        <div class="form-group">
            <label for="signup_email">E-mail do Administrador</label>
            <input required type="email" class="form-control" id="signup_email" name="signup_email">
        </div>

        <div class="form-group">
            <label for="admin_pass">Senha do administrador</label>
            <input type="password" class="form-control" id="admin_pass" name="admin_pass" autocomplete="off">
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Instalar</button>
        </form>
    </div>
</div>



</body>
</html>