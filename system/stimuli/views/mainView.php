<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';
require ROOTPATH . "/ui/modal.php";
checkUser(["professional","admin"], BASE_URL);
$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/mainView.php";
require_once ROOTPATH . '/utils/GetData.php';

$filter_value = "";

if(isset($data['query']) && strlen($data['query']) >0){
    $filter_value=$data['query'];
}


if(!isset($data['page'])){
    
    $data['page'] = 1;
}
?>
<script> 
  
    function showHelp(){
        var content = '<iframe width="560" height="315" src="https://www.youtube.com/embed/kqoS2eqSSjc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        showModal("Ajuda",content);
    }
  
</script>
<div class="row">
    <div class="col">
        <div class="container">

            <div class="row">
                <div class="col">
                    <div class="row mt-3">
                        <div class="col">

                            <h3> <a class="btn btn-primary btn-lg btn-block" href="<?php echo BASE_URL . "/stimuli?action=newStimuliForm" ?>"><?php echo $lang["new_stimuli"]; ?> </a></h3>

                        </div>
                        <div class="col">
                            <h3> <a class="btn btn-primary btn-lg btn-block disabled" href="#"><?php echo $lang["search_database"]; ?>   </a></h3>

                        </div>
                    </div>

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

                    <!-- Results appear here -->

                    <div class="row mt-4">
                        <div class="col">
                            
                            <?php
                            require_once ROOTPATH . '/utils/DBAccess.php';
                            
                            if(isset($data['query'])){  
                                $query = $data['query'];
                            }
                            
                            ///gets results.
                            $s_page = $data['page']-1;
                            if($s_page < 0){
                                $s_page = 0;
                            }
                            $results_per_page = 12;
                                
                            $offset = $s_page * $results_per_page;
                            $limit  = $results_per_page;


                            $sController = new StimuliController();
                            $s_data = ['query'=>$query, 'offset'=>$offset,'resultsAsArray'=>true];
                            $res = $sController->get_as_json($s_data);
                            $num_res = $res['total'];

                            
                            
                            $num_pages = intdiv($num_res , $results_per_page);
                            
                            if( ($num_pages * $results_per_page) < $num_res){
                                $num_pages = $num_pages + 1;
                            }


                            ?>
                            <div class="card-columns">
                                
                                <?php
                                    foreach($res['results'] as $fetch )
                                    {
                                        
                                        
                                        if($fetch['type'] == "image"){
                                            
                                            
                                ?>
                                <div class="card" id="card-<?php echo $fetch['id'];?>">
                                    <img class="card-img-top" src="<?php echo $fetch['data'];?>" alt="Card image cap">
                                    <div class="card-body">
                                     <h4 class="card-title"><?php echo $fetch['name'];?></h4>
                                        
                                        <p class="card-text"><?php echo $fetch['description'];?></p>
                                        <?php if($fetch['owner_id']!='pub'){ ?>
                                        <button class="btn btn-block btn-danger" onclick="askToRemoveStimuli('<?php echo $fetch['id'];?>')">Remover</button>
                                        <?php } ?>
                                        <cite class="card-text">
                                            <?php
                                            for($i=0; $i<count($fetch['labels']); $i++){
                                                ?>
                                                 <span class="badge badge-secondary">
                                                     <?php
                                                echo $fetch['labels'][$i];
                                                ?>
                                                </span>
                                                <?php
                                            }
                                             
                                             ?>
                                        </cite>

                                    </div>
                                </div>
                                <?php
                                        }
                                        elseif ($fetch['type']=='audio') {
                                            ?>
                                   <div class="card" id="card-<?php echo $fetch['id'];?>">
                                       <audio controls>
                                           <source src="<?php echo BASE_URL . $fetch['url'];?>">
                                       </audio>
                                        <div class="card-body">
                                            <h4 class="card-title"><?php echo $fetch['name'];?></h4>
                                        <p class="card-text"><?php echo $fetch['description'];?></p>
                                        
                                        <?php if($fetch['owner_id']!='pub'){ ?>
                                        <button class="btn btn-block btn-danger" onclick="askToRemoveStimuli('<?php echo $fetch['id'];?>')">Remover</button>
                                        <?php } ?>
                                        <cite class="card-text">
                                            <?php
                                            for($i=0; $i<count($fetch['labels']); $i++){
                                                ?>
                                                 <span class="badge badge-secondary">
                                                     <?php
                                                echo $fetch['labels'][$i];
                                                ?>
                                                </span>
                                                <?php
                                            }
                                             
                                             ?>
                                        </cite>

                                        </div>
                                    </div>
                                
                                <?php
                                        
                                    }
                                    }
                                ?>
                               

                            </div>
                        </div>


                    </div>
                </div>
            </div>  

            <!-- Results appear here -->


            <!-- pagination -->
            <div class="row mt-3">
                <div class="col">

                    
                    <ul class="pagination">
                        <?php
                            if($num_pages <=1)
                            {
                                ?>
                                <li class="page-item disabled"><a class="page-link" href="#"><?php echo $lang["previous_page"]; ?></a></li>
                                <li class="page-item  disabled"><a class="page-link" href="#" >1</a></li>
                                <li class="page-item disabled"><a class="page-link" href="#"><?php echo $lang["next_page"]; ?></a></li>
                                <?php
                            }
                            else
                            {

                                if(($data['page']-1) <=0)
                                {?>
                                    <li class="page-item disabled"><a class="page-link" href="#"><?php echo $lang["previous_page"]; ?></a></li>
                                    <?php
                                }
                                else
                                {?>
                                    <li class="page-item "><a class="page-link" href="index.php?query=<?php echo $query; ?>&page=<?php echo ($data['page']-1);?>"><?php echo $lang["previous_page"]; ?></a></li>
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
                                    <li class="page-item disabled"><a class="page-link" href="#"><?php echo $lang["next_page"]; ?></a></li>
                                    <?php
                                }
                                else
                                {?>
                                    <li class="page-item "><a class="page-link" href="index.php?query=<?php echo $query; ?>&page=<?php echo ($data['page']+1);?>"><?php echo $lang["next_page"]; ?></a></li>
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
<div id='help' style="position: absolute; top:5px; right: 30px;" >
    <button class='btn btn-block btn-lg btn-warning' onclick="showHelp()"><i class="fas fa-question"></i></button>

    </div>
</div>

<script>
function askToRemoveStimuli(id){
    showModal("Deseja Remover?", "Remover o estímulo? Ele ainda continuará aparecendo nas atividades em que foi utilizado, porém, não aparecerá na listagem de estímulos.",
    function(){
        
        var xhttp = new XMLHttpRequest();
        
        var activity = this;
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText == "OK"){
                    var card = document.getElementById('card-'+id);
                    console.log(card);
                    card.classList.add('d-none');//(card);
                }
            }
        };
        var url =  "<?php echo BASE_URL;?>/stimuli/index.php?action=removeStimuli";
        console.log(url);
        xhttp.open('POST', url, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send("&stimuli_id="+id);
        closeModal();
    })
}
</script>