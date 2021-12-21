<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (!defined('ROOTPATH')) {
    require '../root.php';
}
require ROOTPATH . "/ui/modal.php";
require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["professional","admin"], BASE_URL);

$filter_value = "";

if(isset($data['query']) && strlen($data['query']) >0){
    $filter_value=$data['query'];
}

?>
<div class="row">
    <div class="col">
        <div class="container mt-3">
            <!-- filtrar -->
                    <div class="row mt-4">
                        <div class="col">
                            <form autocomplete="off" class="form mt-1" action="index.php?action=selectReinforcement" method="post">
                                
                                <div class="form-row">
                                    <div class="form-group col-md-11">
                                        <input class="form-control mr-sm-2" id="search" name="query" type="query" placeholder="Filtrar" aria-label="Search" value="<?php echo $filter_value;?>">
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
                $user_id = $_SESSION['username'];
                $query = $data['query'];
                
                $SQL = "SELECT COUNT(*) AS total  FROM activity WHERE (owner_id ='$user_id' OR owner_id='pub') AND (category LIKE '%reinforcement%' AND category LIKE '%template%')";
                
                
                if(isset($data['query'])){

                    $query = $data['query'];

                    $SQL = $SQL. " AND ".
                        "(name LIKE '%$query%' OR antecedent LIKE '%$query%' OR behavior LIKE '%$query%' OR consequence LIKE '%$query%')";
                }

                $num_res = $db->query($SQL);
                $num_res = mysqli_fetch_assoc($num_res);
                $num_res = $num_res['total'];
                $results_per_page = 12;
                $num_pages = intdiv($num_res , $results_per_page);
                if( ($num_pages * $results_per_page) < $num_res)
                    $num_pages = $num_pages + 1;
                //echo "num res: $num_res num pages? $num_pages <br>";

                ///gets results.
                $s_page = $data['page']-1;
                if($s_page < 0){
                    $s_page = 0;
                }

                $offset = $s_page * $results_per_page;
                $limit  = $results_per_page;
                
                $SQL = "SELECT * FROM activity WHERE (owner_id ='$user_id' OR owner_id='pub') AND (category LIKE '%reinforcement%' AND category LIKE '%template%') LIMIT  $limit OFFSET  $offset";
                
                


                if(isset($data['query'])){

                    $query = $data['query'];
                    $SQL = "SELECT * FROM activity WHERE (owner_id ='$user_id' OR owner_id='pub') AND (category LIKE '%reinforcement%' AND category LIKE '%template%')";
                    $SQL = $SQL. " AND ".
                    "(name LIKE '%$query%' OR antecedent LIKE '%$query%' OR behavior LIKE '%$query%' OR consequence LIKE '%$query%') LIMIT  $limit OFFSET  $offset";
                }
                $res = $db->query($SQL);


                ?>
                
                    <?php
                    while($fetch = mysqli_fetch_assoc($res))
                    { 
                    ?>
                        <div class="card border-dark">
                            <h4 class="card-header"><?php echo $fetch['name'];?></h4>
                            <div class="card-body">
                             
                             <p class="card-title"><b>Antecedente</b>: <?php echo $fetch['antecedent'];?></p>
                                <p class="card-text"><b>Comportamento esperado</b>: <?php echo $fetch['behavior'];?></p>
                                <p class="card-text"><b>Consequência</b>: <?php echo $fetch['consequence'];?></p>
                                
                                <a href="index.php?action=newFromTemplate&reinforcement=true&templateId=<?php echo $fetch['id'];?>" class="btn btn-primary">Novo reforço</a>
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
                            if($num_pages <=1)
                            {
                                ?>
                                <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                                <li class="page-item  disabled"><a class="page-link" href="#" >1</a></li>
                                <li class="page-item disabled"><a class="page-link" href="#">Próxima</a></li>
                                <?php
                            }
                            else
                            {
                                if(($data['page']-1) <=0)
                                {?>
                                    <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                                    <?php
                                }
                                else
                                {?>
                                    <li class="page-item "><a class="page-link" href="index.php?action=selectTemplate&query=<?php echo $query; ?>&page=<?php echo ($data['page']-1);?>">Anterior</a></li>
                                    <?php
                                    
                                }
                                $i = 0;
                                for($i=0; $i < $num_pages; $i++)
                                {
                                    if( ($data['page']-1) == $i)
                                    {
                                        //curr page
                                        ?>
                                        <li class="page-item disabled"><a class="page-link" href="#"><?php echo ($i +1);?></a></li>
                                        <?php
                                    }
                                    else
                                    {
                                         ?>
                                        <li class="page-item"><a class="page-link" href="index.php?action=selectTemplate&query=<?php echo $query; ?>&page=<?php echo ($i +1);?>"><?php echo ($i +1);?></a></li>
                                        <?php
                                    }
                                }
                                if(($data['page']) >= $num_pages)
                                {?>
                                    <li class="page-item disabled"><a class="page-link" href="#">Próxima</a></li>
                                    <?php
                                }
                                else
                                {?>
                                    <li class="page-item "><a class="page-link" href="index.php?action=selectTemplate&query=<?php echo $query; ?>&page=<?php echo ($data['page']+1);?>">Próxima</a></li>
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
