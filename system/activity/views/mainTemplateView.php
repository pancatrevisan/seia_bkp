<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

if (!defined('ROOTPATH')) {
    require '../root.php';
}

 
require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["admin","professional"], BASE_URL);

require ROOTPATH . "/ui/modal.php";

$filter_value = "";

if(isset($data['query']) && strlen($data['query']) >0){
    $filter_value=$data['query'];
}

?>
<div class="row">
    <div class="col">
        <div class="container">

            <div class="row">
                <div class="col">
                    <div class="row mt-3">
                        <div class="col">

                            <h3> <a class="btn btn-secondary btn-lg btn-block border-dark text-white" href="index.php?action=newUserTemplate">Novo template </a></h3>

                        </div>
                       <!-- <div class="col">
                            <h3> <a class="btn btn-warning btn-lg btn-block border-dark text-white" href="activity_pub.php">Buscar no repositório</a></h3>

                        </div>-->
                    </div>

                    <!-- filtrar -->
                    <!--<div class="row mt-4">
                        <div class="col">
                            <form autocomplete="off" class="form mt-1" action="index.php?action=filter_form" method="post">
                                
                                <div class="form-row">
                                    <div class="form-group col-md-11">
                                        <input class="form-control mr-sm-2" id="search" name="query" type="query" placeholder="Filtrar" aria-label="Search" value="">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <button type="submit" class="btn btn-outline-success form-control">
                                            <i class="fas fa-search"></i> 
                                        </button>

                                    </div>
                                </div> 
                                
                            </form>
                        </div>
                    </div>-->
                    
                    
                    <!-- resultados -->
                    
                    <div class="card-columns">
                    <?php
                    require_once ROOTPATH . '/utils/DBAccess.php';
                    $SQL = "";
                    $db = new DBAccess();
                    $user_id = $_SESSION['username'];
                    $query = "";

                    $SQL = "SELECT COUNT(*) AS total  FROM activity WHERE owner_id ='$user_id' AND active='1' AND auto='0' AND auto_guide='0' AND category LIKE '%template%'";


                    if(isset($data['query'])){

                        $query = $data['query'];

                        $SQL = $SQL. " AND ".
                            "(name LIKE '%$query%' OR antecedent LIKE '%$query%' OR behavior LIKE '%$query%' OR consequence LIKE '%$query%' OR category LIKE '%$query%')";
                    }

                    $num_res = $db->query($SQL);
                    $num_res = mysqli_fetch_assoc($num_res);
                    $num_res = $num_res['total'];
                    $results_per_page = 12;
                    $num_pages = intdiv($num_res , $results_per_page);
                    if( ($num_pages * $results_per_page) < $num_res)
                        $num_pages = $num_pages + 1;
                   

                    ///gets results.
                    $s_page = $data['page']-1;
                    if($s_page < 0){
                        $s_page = 0;
                    }

                    $offset = $s_page * $results_per_page;
                    $limit  = $results_per_page;

                    $SQL = "SELECT * FROM activity WHERE owner_id ='$user_id' AND active=1 AND auto=FALSE AND auto_guide=FALSE AND  LIKE '%template%'  LIMIT  $limit OFFSET  $offset";



                    if(isset($data['query'])){

                        $query = $data['query'];
                        $SQL = "SELECT * FROM activity WHERE owner_id ='$user_id' AND active=1 AND auto=FALSE AND auto_guide=FALSE AND  category LIKE '%template%'";
                        $SQL = $SQL. " AND ".
                        "(name LIKE '%$query%' OR antecedent LIKE '%$query%' OR behavior LIKE '%$query%' OR consequence LIKE '%$query%') LIMIT  $limit OFFSET  $offset";
                    }
                    $res = $db->query($SQL);


                    ?>

                        <?php
                        while($fetch = mysqli_fetch_assoc($res))
                        { 
                        ?>
                            <div class="card text-white bg-secondary border-dark" id="<?php echo $fetch['id'];?>">
                                <img class="card-img-top rounded  img-thumbnail" src="<?php 
                                    require_once ROOTPATH . '/activity/ActivityController.php';
                                    $ac = new ActivityController();
                                    echo $ac->getThumbnail(['id'=>$fetch['id']]);
                                ?>">
                                <h4 class="card-header border-dark"><?php echo $fetch['name'];?></h4>   
                                <div class="card-body">
                                 
                                 
                                 <!--<h4 class="card-text">Antecedente</h4>
                                 <p class="card-text"><?php echo $fetch['antecedent'];?></p>
                                 
                                 <h4 class="card-text">Comportamento Esperado</h4>
                                    <p class="card-text"><?php echo $fetch['behavior'];?></p>
                                 <h4 class="card-text">Consequência</h4>
                                    <p class="card-text"><?php echo $fetch['consequence'];?></p> 
                                    
                                    <cite class="card-text"><?php echo $fetch['category'];?></cite>-->
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-6"><a href="index.php?action=editTemplateWizzard&templateId=<?php echo $fetch['id'];?>" class="btn btn-block btn-dark">Editar</a></div>
                                            <div class="col-6"><a href="#" class="btn btn-block btn-dark" onclick="removeActivity('<?php echo $fetch['id'];?>')">Excluir</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>


                </div>
                    
                    <!--pagination -->
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
                                    <li class="page-item "><a class="page-link" href="index.php?query=<?php echo $query; ?>&page=<?php echo ($data['page']-1);?>">Anterior</a></li>
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
                                        <li class="page-item"><a class="page-link" href="index.php?query=<?php echo $query; ?>&page=<?php echo ($i +1);?>"><?php echo ($i +1);?></a></li>
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
                                    <li class="page-item "><a class="page-link" href="index.php?query=<?php echo $query; ?>&page=<?php echo ($data['page']+1);?>">Próxima</a></li>
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

<script>
    function removeActivity(id){
        
        showModal("Remover Atividade?","Isso não poderá ser desfeito. A atividade continuará a existir em programas de ensino existentes, mas não aparecerá mais neste lista.", function(){
            
            console.log("send http");
            var xhr = new XMLHttpRequest();
            
            xhr.onreadystatechange = function () {
                if (this.readyState != 4) return;

                if (this.status == 200) {
                    var data = this.responseText;// JSON.parse(this.responseText);
                    var el = document.getElementById(data);
                    el.parentNode.removeChild(el);
                    closeModal();
                }

               
            };
            
            xhr.open("POST", '<?php echo BASE_URL;?>/activity/index.php?action=removeActivity', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            var url ='activityId='+id+"&page="+<?php echo $data['page'];?>;
            
            var query = "<?php echo $query;?>";
            
            url = url + "&query="+ query;
            xhr.send(url);
        },true);   
    }
</script>