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

    <!-- filtrar -->
    <div class="row mt-4">
                        <div class="col">
                            <form autocomplete="off" class="form mt-1" action="<?php echo BASE_URL;?>/athena/index.php?action=viewUserStudents" method="get">
                               
                               <?php 
                                if ($data['page']> 1){
                                    ?>
                                        <input hidden name="page" id="page" value="1">
                                    <?php
                                }
                                else{
                                        ?>

                                        
                                        <input hidden name="page" id="page" value="<?php echo $data['page'];?>">
                                <?php
                                }
                                ?>
                               
                               <input hidden name="action" id="action" value="viewUserStudents">
                               <input hidden name="user" id="user" value="<?php echo $user_id;?>">
                                <div class="form-row">
                                    <div class="form-group col-md-11">
                                        <input class="form-control mr-sm-2" id="query" name="query" type="query" placeholder="Filtrar" aria-label="Search" value="">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <button type="submit" class="btn btn-outline-success form-control">
                                            <i class="fas fa-search"></i> 
                                        </button>

                                    </div>
                                </div> 
                                
                            </form>
                        </div>
                    </div>
        
        <div class="container">
            
            <div class="row">
                <div class="col">



                    <!-- resultados -->

                    <div class="card-columns mt-4">
                        <?php
                        require_once ROOTPATH . '/utils/DBAccess.php';
                        $SQL = "";
                        $db = new DBAccess();
                        $user_id = $user_id;
                        $query = "";
                        if($user_id == "ALL"){
                            $SQL = "SELECT COUNT(*) AS total  FROM student";
                        }
                        else{
                            $SQL = "SELECT COUNT(*) AS total  FROM student LEFT JOIN student_tutorship  ON student.id=student_tutorship.student_id WHERE student_tutorship.professional_id ='$user_id' ";
                        }
                        

                        if (isset($data['query'])) {

                            if(strlen($data['query'])>1){
                                $query = $data['query'];
                                if($user_id != "ALL"){
                                    $SQL = $SQL . " AND " ;
                                }
                                $SQL = $SQL . " WHERE " .
                                    "name LIKE '%$query%'";
                            }
                        }

                      
                        $num_res = $db->query($SQL);
                        $num_res = mysqli_fetch_assoc($num_res);
                        $num_res = $num_res['total'];
                        $results_per_page = 12;
                        $num_pages = intdiv($num_res, $results_per_page);
                        if (($num_pages * $results_per_page) < $num_res)
                            $num_pages = $num_pages + 1;


                        ///gets results.

                        $s_page = $data['page'] - 1;
                        if ($s_page < 0) {
                            $s_page = 0;
                        }

                        $offset = $s_page * $results_per_page;
                        $limit  = $results_per_page;
                        if($user_id == "ALL"){
                            $SQL = "SELECT *  FROM student  LIMIT $limit OFFSET  $offset";
                        }
                        else{
                            $SQL = "SELECT *  FROM student LEFT JOIN student_tutorship  ON student.id=student_tutorship.student_id WHERE student_tutorship.professional_id ='$user_id' LIMIT $limit OFFSET  $offset";
                        }
                        

                        //echo $SQL;

                        if (isset($data['query'])) {

                            $query = $data['query'];
                            if(strlen($data['query'])>1){
                                if($user_id == "ALL"){
                                    $SQL = "SELECT *  FROM student";
                                }
                                else{
                                    $SQL = "SELECT *  FROM student LEFT JOIN student_tutorship  ON student.id=student_tutorship.student_id WHERE student_tutorship.professional_id ='$user_id' ";
                                }
                                
                                $query = $data['query'];
                                if($user_id != "ALL"){
                                    $SQL = $SQL . " AND " ;
                                }
                                $SQL = $SQL . " WHERE " .
                                    "name LIKE '%$query%'  LIMIT  $limit OFFSET  $offset";
                                }
                        }
                        $res = $db->query($SQL);


                        ?>

                        <?php
                        while ($fetch = mysqli_fetch_assoc($res)) {
                        
                        
                            if($user_id == "ALL"){ ?>
                                <div class="card text-white bg-danger border-dark" id="<?php echo $fetch['id']; ?>">
                                <img class="img-fluid rounded img-thumbnail" width="100%" height="auto" src="<?php echo BASE_URL; ?>/data/student/<?php echo $fetch['id']; ?>/<?php echo $fetch['avatar']; ?>">
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


                                    <div class="dropdown container-fluid">
                                        <button class="btn btn-secondary dropdown-toggle btn-lg btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Opções
                                        </button>
                                        <div class="dropdown-menu container-fluid" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/professional/index.php?action=editStudent&athena=true&studentId=<?php echo $fetch['id']; ?>">Perfil</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=index&athena=true&studentId=<?php echo $fetch['id']; ?>">Programações de Ensino</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=editFullSession&athena=true&id=<?php echo $fetch['curriculum_id']; ?>" &studentId=<?php echo $fetch['id']; ?>">Configurar/Aplicar Currículo</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/professional/index.php?action=studentReport&athena=true&studentId=<?php echo $fetch['id']; ?>">Relatórios</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/athena/index.php?action=studentTotalReport&athena=true&studentId=<?php echo $fetch['id']; ?>">Relatório Geral do Estudante</a>
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <?php }
                            else{?>

                            
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


                                    <div class="dropdown container-fluid">
                                        <button class="btn btn-secondary dropdown-toggle btn-lg btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Opções
                                        </button>
                                        <div class="dropdown-menu container-fluid" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/professional/index.php?action=editStudent&athena=true&studentId=<?php echo $fetch['student_id']; ?>">Perfil</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=index&athena=true&studentId=<?php echo $fetch['student_id']; ?>">Programações de Ensino</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=editFullSession&athena=true&id=<?php echo $fetch['curriculum_id']; ?>" &studentId=<?php echo $fetch['student_id']; ?>">Configurar/Aplicar Currículo</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/professional/index.php?action=studentReport&athena=true&studentId=<?php echo $fetch['student_id']; ?>">Relatórios</a>
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php
                            }
                        }
                        ?>


                    </div>
                    <!-- pagination -->
                    <div class="row mt-3">
                        <div class="col">


                            <ul class="pagination">
                                <?php
                                if ($num_pages <= 1) {
                                ?>
                                    <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                                    <li class="page-item  disabled"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item disabled"><a class="page-link" href="#">Próxima</a></li>
                                    <?php
                                } else {

                                    if (($data['page'] - 1) <= 0) { ?>
                                        <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                                    <?php
                                    } else { ?>
                                        <li class="page-item "><a class="page-link" href="<?php echo BASE_URL;?>/athena/index.php?action=viewUserStudents&user=<?php echo $user_id; ?>&page=<?php echo ($data['page'] - 1); ?>">Anterior</a></li>
                                        <?php

                                    }
                                    $i = 0;
                                    for ($i = 0; $i < $num_pages; $i++) {
                                        if (($data['page'] - 1) == $i) {
                                            //curr page
                                        ?>
                                            <li class="page-item disabled"><a class="page-link" href="#"><?php echo ($i + 1); ?></a></li>
                                        <?php
                                        } else {
                                        ?>
                                            <li class="page-item"><a class="page-link" href="<?php echo BASE_URL;?>/athena/index.php?action=viewUserStudents&user=<?php echo $user_id; ?>&page=<?php echo ($i + 1); ?>"><?php echo ($i + 1); ?></a></li>
                                        <?php
                                        }
                                    }
                                    if (($data['page']) >= $num_pages) { ?>
                                        <li class="page-item disabled"><a class="page-link" href="#">Próxima</a></li>
                                    <?php
                                    } else { ?>
                                        <li class="page-item "><a class="page-link" href="<?php echo BASE_URL;?>/athena/index.php?action=viewUserStudents&user=<?php echo $user_id; ?>&page=<?php echo ($data['page'] + 1); ?>">Próxima</a></li>
                                    <?php

                                    }
                                    ?>


                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>





            </div>
        </div>
    </div>
</div>

