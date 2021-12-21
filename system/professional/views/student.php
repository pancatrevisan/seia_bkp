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

checkUser(["professional", "admin"], BASE_URL);

$user_id = $_SESSION['username'];

?>
<div class="row">
    <div class="col">
        <div class="container">

            <div class="row">
                <div class="col">
                    <div class="row mt-3">
                        <div class="col">

                            <h3> <a class="btn btn-danger btn-lg btn-block border-dark text-white" href="index.php?action=showNewStudentForm">Cadastrar Estudante</a></h3>

                        </div>

                    </div>

                    <!-- filtrar -->
                    <div class="row mt-4">
                        <div class="col">
                            <form autocomplete="off" class="form mt-1" action="index.php?action=student" method="post">

                                <div class="form-row">
                                    <div class="form-group col-md-11">
                                        <input class="form-control mr-sm-2" id="search" name="query" type="query" placeholder="Filtrar" aria-label="Search" value="<?php echo $filter_value; ?>">
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


                    <!-- resultados -->

                    <div class="card-columns">
                        <?php
                        require_once ROOTPATH . '/utils/DBAccess.php';
                        $SQL = "";
                        $db = new DBAccess();
                        $user_id = $user_id;
                        $query = "";

                        $SQL = "SELECT COUNT(*) AS total  FROM student LEFT JOIN student_tutorship  ON student.id=student_tutorship.student_id WHERE student_tutorship.professional_id ='$user_id' ";

                        if (isset($data['query'])) {

                            $query = $data['query'];

                            $SQL = $SQL . " AND " .
                                "name LIKE '%$query%'";
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

                        $SQL = "SELECT *  FROM student LEFT JOIN student_tutorship  ON student.id=student_tutorship.student_id WHERE student_tutorship.professional_id ='$user_id' LIMIT $limit OFFSET  $offset";

                        //echo $SQL;

                        if (isset($data['query'])) {

                            $query = $data['query'];
                            $SQL = "SELECT *  FROM student LEFT JOIN student_tutorship  ON student.id=student_tutorship.student_id WHERE student_tutorship.professional_id ='$user_id' ";

                            $SQL = $SQL . " AND " .
                                "name LIKE '%$query%'  LIMIT  $limit OFFSET  $offset";
                        }
                        $res = $db->query($SQL);


                        ?>

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


                                    <div class="dropdown container-fluid">
                                        <button class="btn btn-secondary dropdown-toggle btn-lg btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Opções
                                        </button>
                                        <div class="dropdown-menu container-fluid" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="index.php?action=editStudent&studentId=<?php echo $fetch['student_id']; ?>">Perfil</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=index&studentId=<?php echo $fetch['student_id']; ?>">Programações de Ensino</a>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=editFullSession&id=<?php echo $fetch['curriculum_id']; ?>" &studentId=<?php echo $fetch['student_id']; ?>">Configurar/Aplicar Currículo</a>
                                            <a class="dropdown-item" href="index.php?action=editStudentData&studentId=<?php echo $fetch['student_id']; ?>">Editar dados</a>
                                            <a class="dropdown-item" href="index.php?action=studentReport&studentId=<?php echo $fetch['student_id']; ?>">Relatórios</a>
                                            <a class="dropdown-item" href="javascript:askToRemoveStudent('<?php echo $fetch['student_id']; ?>')">Remover estudante</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php
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
                                        <li class="page-item "><a class="page-link" href="index.php?action=student&query=<?php echo $query; ?>&page=<?php echo ($data['page'] - 1); ?>">Anterior</a></li>
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
                                            <li class="page-item"><a class="page-link" href="index.php?action=student&query=<?php echo $query; ?>&page=<?php echo ($i + 1); ?>"><?php echo ($i + 1); ?></a></li>
                                        <?php
                                        }
                                    }
                                    if (($data['page']) >= $num_pages) { ?>
                                        <li class="page-item disabled"><a class="page-link" href="#">Próxima</a></li>
                                    <?php
                                    } else { ?>
                                        <li class="page-item "><a class="page-link" href="index.php?action=student&query=<?php echo $query; ?>&page=<?php echo ($data['page'] + 1); ?>">Próxima</a></li>
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
</div>

<script>
    function askToRemoveStudent(id) {
        showModal("Remover?", "Remover o estudante? Isto não pode ser desfeito!", function() {
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                if (this.readyState != 4) return;

                if (this.status == 200) {
                    console.log(this.responseText);
                    window.location.reload();

                }


            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/professional/index.php?action=removeStudent', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

            xhr.send('student_id=' + id);
        });
    }

    function removeActivity(id) {

        showModal("Remover Atividade?", "Isso não poderá ser desfeito. A atividade continuará a existir em programas de ensino existentes, mas não aparecerá mais neste lista.", function() {

            console.log("send http");
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                if (this.readyState != 4) return;

                if (this.status == 200) {
                    var data = this.responseText; // JSON.parse(this.responseText);
                    var el = document.getElementById(data);
                    el.parentNode.removeChild(el);
                    closeModal();
                }


            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/activity/index.php?action=removeActivity', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            var url = 'activityId=' + id + "&page=" + <?php echo $data['page']; ?>;

            var query = "<?php echo $query; ?>";

            url = url + "&query=" + query;
            xhr.send(url);
        }, true);
    }
</script>