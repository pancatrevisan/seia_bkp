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



?>



<div class="row">




    <div class="col">

        <!-- resultados -->

        <div class="container mt-3">

            <?php
            require_once ROOTPATH . '/utils/DBAccess.php';
            $SQL = "";
            $db = new DBAccess();
            

            
            $SQL = "SELECT COUNT(*) as total FROM user WHERE role='professional'";
            $res = $db->query($SQL);
            $res = mysqli_fetch_assoc($res);
            ?>
            <div class="alert alert-primary" role="alert">
                <?php
                echo "Numero de profissionais cadastrados: " . $res['total'];
                ?>
            </div>


            <?php
            $SQL = "SELECT COUNT(*) as total FROM user WHERE role='tutor'";
            $res = $db->query($SQL);
            $res = mysqli_fetch_assoc($res);
            ?>
            <div class="alert alert-primary" role="alert">
                <?php
                echo "Numero de tutores cadastrados: " . $res['total'];
                ?>
            </div>
            

            <?php
            $SQL = "SELECT COUNT(*) as total FROM student WHERE name!='Estudante Exemplo'";
            $res = $db->query($SQL);
            $res = mysqli_fetch_assoc($res);
            ?>
            <div class="alert alert-primary" role="alert">
                <?php
                echo "Numero de estudantes (descontando exemplo): " . $res['total'];
                ?>
            </div>

            <?php
            $SQL = "SELECT COUNT(*) as total FROM student ";
            $res = $db->query($SQL);
            $res = mysqli_fetch_assoc($res);
            ?>
            <div class="alert alert-primary" role="alert">
                <?php
                echo "Numero de estudantes (contando exemplo): " . $res['total'];
                ?>
            </div>

            

            <?php
            $SQL = "SELECT COUNT(*) as total FROM activity WHERE active='TRUE' AND category!='reinforcement' AND category!='template'";
            $res = $db->query($SQL);
            $res = mysqli_fetch_assoc($res);
            ?>
            <div class="alert alert-primary" role="alert">
                <?php
                echo "Numero de atividades: " . $res['total'];
                ?>
            </div>


            <?php
            $SQL = "SELECT COUNT(*) as total FROM activity WHERE active='TRUE' AND category='reinforcement'";
            $res = $db->query($SQL);
            $res = mysqli_fetch_assoc($res);
            ?>
            <div class="alert alert-primary" role="alert">
                <?php
                echo "Numero de reforçadores: " . $res['total'];
                ?>
            </div>


            <?php
            $SQL = "SELECT COUNT(*) as total FROM session_program WHERE active='TRUE'";
            $res = $db->query($SQL);
            $res = mysqli_fetch_assoc($res);
            ?>
            <div class="alert alert-primary" role="alert">
                <?php
                echo "Numero de programas de ensino: " . $res['total'];
                ?>
            </div>

        </div>
    </div>
</div>