<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!defined('ROOTPATH')) {
    require '../root.php';
}
require ROOTPATH . "/ui/modal.php";

$filter_value = "";

if (isset($data['query']) && strlen($data['query']) > 0) {
    $filter_value = $data['query'];
}


require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["athena"], BASE_URL);

$user_id = $data['user'];

?>



<div class="row">

<div class="col-2 p-3">
        <img class="img-fluid rounded " width="100%" height="auto" src="<?php echo BASE_URL; ?>/data/user/<?php echo $user_id; ?>/avatar.png">
        <p class="alert alert-primary "> Usuário: <?php echo $data['user'];?></p>
    </div>


    <div class="col">

        <!-- resultados -->

        <div class="container mt-3">

            <?php
            require_once ROOTPATH . '/utils/DBAccess.php';
            $SQL = "";
            $db = new DBAccess();
            $user_id = $user_id;
            $query = "";

            $SQL = "SELECT * FROM user_login WHERE username='$user_id' ORDER BY time DESC ";
            $res = $db->query($SQL);
            while ($fetch = mysqli_fetch_assoc($res)) {

            ?>
                <div class="alert alert-primary" role="alert">
                    <?php
                    $date = new DateTime($fetch['time']);
                    echo "Data de acesso: " . $date->format('d-m-Y H:i');
                    //echo "Data de acesso: " . $fetch['time'];
                    ?>
                </div>

            <?php
            }
            ?>


        </div>
    </div>
</div>