<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';
require_once ROOTPATH . '/ui/modal.php';
checkUser(["professional", "admin", "athena"], BASE_URL);

$user_id = $_SESSION['username'];

if (isset($data['query']) && strlen($data['query']) > 0) {
    $filter_value = $data['query'];
}
?>

<script>
    
</script>


<div class="row mt-3 mb-3">
    
    <div class="col">
        <div class="container">
                  <!-- filtrar -->
                  <div class="row mt-4">
                        <div class="col">
                            <form autocomplete="off" class="form mt-1" action="<?php echo BASE_URL;?>/athena/index.php?action=users" method="get">
                               
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
                               
                               <input hidden name="action" id="action" value="users">
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

            <div class="card-columns">
                <?php
                require_once ROOTPATH . '/utils/DBAccess.php';
                $SQL = "";
                $db = new DBAccess();
                $user_id = $user_id;
                $query = "";

                $SQL = "SELECT COUNT(*) AS total  FROM user " ;

                if (isset($data['query'])) {

                    $query = $data['query'];

                    $SQL = $SQL . " WHERE " .
                        "name LIKE '%$query%'";
                }
                $SQL = $SQL . " ORDER BY username";

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

                $SQL = "SELECT *  FROM user ";

                

                if (isset($data['query'])) {
                    if(strlen($data['query'])>0){
                        
                    $query = $data['query'];
                    //$SQL = "SELECT *  FROM user  ORDER BY 'username' ASC";

                    $SQL = $SQL . 
                        " WHERE name LIKE '%$query%' ";//  LIMIT  $limit OFFSET  $offset";                        
                    }

                }
                $SQL = $SQL . " ORDER BY 'username' ASC LIMIT $limit OFFSET  $offset";
                
                $res = $db->query($SQL);

                

                ?>

                <?php
                while ($fetch = mysqli_fetch_assoc($res)) {

                ?>
                    <?php if($fetch['role']=="tutor"){

                    ?>
                        <div class="card text-white bg-info border-dark" id="<?php echo $fetch['id']; ?>">
                    <?php 
                    }
                    else if($fetch['role']=='professional'){ ?>
                        <div class="card text-white bg-success border-dark" id="<?php echo $fetch['id']; ?>">
                        
                    <?php 
                    }
                    ?>
                    
                        <img class="img-fluid rounded img-thumbnail" width="100%" height="auto" src="<?php echo BASE_URL; ?>/data/user/<?php echo $fetch['username']; ?>/<?php echo $fetch['avatar']; ?>">
                        <h4 class="card-header border-dark"><?php echo $fetch['name']; ?></h4>
                        <div class="card-body">


                            

                           


                            <div class="dropdown container-fluid">
                                <button class="btn btn-secondary dropdown-toggle btn-lg btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Opções
                                </button>
                                <div class="dropdown-menu container-fluid" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/athena/index.php?action=viewUserStudents&user=<?php echo $fetch['username']; ?>">Estudantes</a>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/athena/index.php?action=viewUserActivities&user=<?php echo $fetch['username']; ?>">Atividades e Recompensas</a>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/athena/index.php?action=viewUserAccessLog&user=<?php echo $fetch['username']; ?>">Logs de acesso</a>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

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
                                    <li class="page-item"><a class="page-link" href="index.php?action=users&query=<?php echo $query; ?>&page=<?php echo ($i + 1); ?>"><?php echo ($i + 1); ?></a></li>
                                <?php
                                }
                            }
                            if (($data['page']) >= $num_pages) { ?>
                                <li class="page-item disabled"><a class="page-link" href="#">Próxima</a></li>
                            <?php
                            } else { ?>
                                <li class="page-item "><a class="page-link" href="index.php?action=users&query=<?php echo $query; ?>&page=<?php echo ($data['page'] + 1); ?>">Próxima</a></li>
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