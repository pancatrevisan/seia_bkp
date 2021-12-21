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
        <div class="container">

            <div class="row">
                <div class="col">
                    <div class="row mt-3">
                        <div class="col">

                            <h3> <a class="btn btn-info btn-lg btn-block border-dark" href="javascript:newProgram()">Novo Programa de Ensino</a></h3>

                        </div>
                        <div class="col">
                            <h3> <a class="btn btn-info btn-lg btn-block border-dark disabled" href="#">Buscar no repositório</a></h3>

                        </div>
                    </div>

                    <!-- filtrar -->
                    <div class="row mt-4">
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
                    </div>
                    
                    
                    <!-- resultados -->
                    
                    <div class="card-columns">
                    <?php
                    require_once ROOTPATH . '/utils/DBAccess.php';
                    $SQL = "";
                    $db = new DBAccess();
                    $user_id = $_SESSION['username'];
                    $query = "";

                    $SQL = "SELECT COUNT(*) AS total  FROM program WHERE owner_id ='$user_id' AND active=1";


                    if(isset($data['query'])){

                        $query = $data['query'];

                        $SQL = $SQL. " AND ".
                            "(name LIKE '%$query%' OR category LIKE '%$query%')";
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

                    $SQL = "SELECT * FROM program WHERE owner_id ='$user_id' OR owner_id='pub' AND active=1 LIMIT  $limit OFFSET  $offset";

                 


                    if(isset($data['query'])){

                        $query = $data['query'];
                        $SQL = "SELECT * FROM program WHERE owner_id ='$user_id' AND active=1";
                        $SQL = $SQL. " AND ".
                        "(name LIKE '%$query%' OR category LIKE '%$query%') LIMIT  $limit OFFSET  $offset";
                    }
                    $res = $db->query($SQL);


                    ?>

                        <?php
                        while($fetch = mysqli_fetch_assoc($res))
                        { 
                        ?>
                            <div class="card text-white bg-info border-dark " id="<?php echo $fetch['id'];?>">
                                <h4 class="card-header border-dark"><?php echo $fetch['name'];?></h4>   
                                <div class="card-body ">
                                 
                                 
                                 <h4 class="card-text">Descrição</h4>
                                 <p class="card-text"><?php echo $fetch['description'];?></p>
                                 
                                    
                                    <cite class="card-text"><?php echo $fetch['category'];?></cite>
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-6"><a href="index.php?action=edit&id=<?php echo $fetch['id'];?>" class="btn btn-block btn-dark">Editar</a></div>
                                            <div class="col-6"><a href="#" class="btn btn-block btn-dark" onclick="removeProgram('<?php echo $fetch['id'];?>')">Excluir</a></div>
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

<form hidden autocomplete="off" id="formNewProgramTemplate">
    
     <div class="form-group">
        <label for="programName">Nome</label>
        <input  required type="text" class="form-control" id="programName" name="programName" placeholder="Nome do Programa">
    </div>
    
    
     <div class="form-group">
        <label for="description">Descrição</label>
        <input  required type="text" class="form-control" id="description" name="description" placeholder="Descrição">
    </div>
    
    
    
     <div class="form-group">
        <label for="category">Categorias (separadas por vírgula)</label>
        <input  required type="text" class="form-control" id="category" name="category" placeholder="Categorias">
    </div>
</form>


<script>
    
    function newProgram(){
        var form = document.getElementById("formNewProgramTemplate").cloneNode(true); 
        form.id="formNewProgram";
        form.hidden = false;
        
        
        showModal("Novo Programa de Ensino",form, function(){
            
            var nameInput = document.getElementById('programName');
            if(!nameInput.checkValidity()){
                nameInput.classList.add('border','border-danger');
                return;
            }else
            {
                nameInput.classList.remove('border','border-danger');
            }
            
            var catInput = document.getElementById('category');
            if(!catInput.checkValidity()){
                catInput.classList.add('border','border-danger');
                return;
            }else
            {
                catInput.classList.remove('border','border-danger');
            }
            
            console.log("send http");
            var xhr = new XMLHttpRequest();
            
            xhr.onreadystatechange = function () {
                if (this.readyState != 4) return;

                if (this.status == 200) {
                    var data = this.responseText;// JSON.parse(this.responseText);
                    console.log(data);
                    if(data == "ERROR"){
                        return;
                    }
                    
                    
                    window.location.href = "index.php?action=edit&id="+data;
                    //closeModal();
                    // we get the returned data
                }

               
            };
            
            xhr.open("POST", '<?php echo BASE_URL;?>/program/index.php?action=addNew', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            xhr.send("programName="+document.getElementById('programName').value+"&category="+document.getElementById('category').value+"&description="+document.getElementById('description').value);
            },true);   
        
            
        
            
    }
    
    function removeProgram(id){
        
        showModal("Remover o Programa?","Isso não poderá ser desfeito. O programa não aparecerá mais neste lista.", function(){
            
            console.log("send http");
            var xhr = new XMLHttpRequest();
            
            xhr.onreadystatechange = function () {
                if (this.readyState != 4) return;

                if (this.status == 200) {
                    var data = this.responseText;// JSON.parse(this.responseText);
                    console.log(data);
                    var el = document.getElementById(data);
                    el.parentNode.removeChild(el);
                    closeModal();
                    // we get the returned data
                }

               
            };
            
            xhr.open("POST", '<?php echo BASE_URL;?>/program/index.php?action=removeProgram', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            var url ='programId='+id+"&page="+<?php echo $data['page'];?>;
            
            var query = "<?php echo $query;?>";
            
            url = url + "&query="+ query;
            xhr.send(url);
        },true);   
    }
</script>