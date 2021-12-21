<?php
$name = $data['name'];
$description = $data['description'];
$programId = $data['programId'];
//print_r($data['groups']);
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require ROOTPATH . "/ui/modal.php";
?>
<script>
    var globalActId = 1;
    window.onload = function () {
        loadFromDatabase();

    };
    function showHelp(){
        var content = 
       '<iframe width="560" height="315" src="https://www.youtube.com/embed/lHzMbF6vv7E" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        
        showModal("Ajuda",content);
    }
</script>

<script>
    
    function getABC(id){
        
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;
            if (this.status == 200) {
                
            }
        }
        
        var url = "<?php echo BASE_URL; ?>/program/index.php?action=getProgram_json";
        var programId = "<?php echo $programId; ?>";
        xhr.open("POST", url, true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        
        xhr.send("programId=" + programId + "&groupId=" + id);
        
    }

    function configProgramOrGroup(id, type) {
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;
            if (this.status == 200) {
                var ret = this.responseText;// JSON.parse(this.responseText);
                if (ret == "ERROR") {
                    return;
                }

                if (ret.length <= 0) {

                    return;
                }
                
                var obj = JSON.parse(this.responseText, true);
                var data = [];
                data['order_type'] = obj['order_type'];



                data['reinforcement_type'] = obj['reinforcement_type'];
                data['reinforcement_value'] = obj['reinforcement_value'];


                data['frequency_type'] = obj['frequency_type'];
                data['frequency_value'] = obj['frequency_value'];

                data['error_type'] = obj['error_type'];
                data['error_value'] = obj['error_value'];

                data['antecedent'] = obj['antecedent'];
                data['behavior'] = obj['behavior'];
                showGroupOrProgramConfig(id, data, type == "program");
            }
        }
        var url = "";
        var programId = "<?php echo $programId; ?>";
        if (type == "program")
            url = "<?php echo BASE_URL; ?>/program/index.php?action=getGroup_json&$programId=id";
        else
            url = "<?php echo BASE_URL; ?>/program/index.php?action=getProgram_json";
        
        xhr.open("POST", url, true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        if (type == "program")
            xhr.send("programId=" + programId);
        else
            xhr.send("programId=" + programId + "&groupId=" + id);

    }

    function showGroupOrProgramConfig(id, data = null, isProgram = false) {
        var type = "group";
        if (isProgram)
            type = "program";
        

        var form = document.getElementById('configGroupOrProgramTemplate').cloneNode(true);
        if (isProgram) {
            form.querySelector('#activity_order').classList.remove('d-none');
            
            form.querySelector('#correctFrequency').classList.remove('d-none');            
            form.querySelector('#abc_data').classList.add('d-none');
        } else
        {
            form.querySelector('#abc_data').classList.remove('d-none');
            form.querySelector('#groupAntecedent').value = data['antecedent'];
            form.querySelector('#groupBehavior').value = data['behavior'];
            
            
            form.querySelector('#activity_order').classList.add('d-none');
            form.querySelector('#correctFrequency').classList.add('d-none');
            
            
            
            
        }


        form.id = "configGroup";

        form.hidden = false;
        var selectReinforcementButton = form.querySelector('#selectGroupReinforcement');
        selectReinforcementButton.onclick = function () {
            selectReinforcement(id, "", "groupOrProgram", groupOrProgramBackupData());
        };
        if (!isModalVisible())
            showModal("Configurações do currículo ", form, function () {
                saveGroupOrProgramConfig(id, type);
            }, true);
        else
            swapModalContent(form, function () {
                saveGroupOrProgramConfig(id, type);
            }, function () {
                showGroupOrProgramConfig(id, data,isProgram);
            });
        if (isProgram) {
            
            var elems = form.querySelectorAll(".teachingProgramConstant_");
            
            var i;
            for (i = 0; i < elems.length; i++) {
                var el = elems[i];
                el.parentNode.remove(el);
            }
        }
        //set values
        if (data == null)
            return;
        
        document.getElementById('groupActivityOrder').value = data['order_type'];

        document.getElementById('groupReinforcement').value = data['reinforcement_type'];
        document.getElementById('groupSelectedReinforcement').value = data['reinforcement_value'];
        if (data['reinforcement_type'] == 'groupChooseCorrect') {
            document.getElementById('groupChooseCorrect').classList.remove('d-none');
        }

        document.getElementById('groupReinforcementFrequency').value = data['frequency_type'];
        if (data['frequency_type'] == "groupReinforcementFrequencyValue") {
            document.getElementById('groupNumRepeatCorrection').value = data['frequency_value'];
            document.getElementById('groupReinforcementFrequencyValue').classList.remove('d-none');
        }

        document.getElementById('groupWrong').value = data['error_type'];
        if (data['error_type'] != 'none' && data['error_type'] != 'groupWrongNextGroup' && data['error_type'] != 'definenInTeachingProgram') {
            document.getElementById(data['error_type'] + "Value").value = data['error_value'];
            document.getElementById(data['error_type']).classList.remove('d-none');
    }





    }

    function saveGroupOrProgramConfig(id, type) {
        
        var data = groupOrProgramBackupData();
        
        if (type == "group") {
            delete data['order_type'];
            delete data['frequency_type'];
            delete data['frequency_value'];
        }
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                closeModal();
            }
        };
        var str_json = JSON.stringify(Object.assign({}, data));
        var url = "";
        if (type == 'group') {
            
            url = '<?php echo BASE_URL; ?>/program/index.php?action=activityGroup_set';
        } else {
            
            url = '<?php echo BASE_URL; ?>/program/index.php?action=program_set';
        }


        xhr.open("POST", url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send("id=" + id + "&keys=" + str_json);

    }


    function groupOrProgramBackupData() {
        var data = [];
        data['order_type'] = document.getElementById('groupActivityOrder').value;
        
        
        data['behavior'] = document.getElementById('groupBehavior').value;
        data['antecedent'] = document.getElementById('groupAntecedent').value;
        
        data['reinforcement_type'] = document.getElementById('groupReinforcement').value;
        if (data['reinforcement_type'] == 'groupChooseCorrect') {
            data['reinforcement_value'] = document.getElementById('groupSelectedReinforcement').value;
        }

        data['frequency_type'] = document.getElementById('groupReinforcementFrequency').value;
        if (data['frequency_type'] != 'none') {
            data['frequency_value'] = document.getElementById('groupNumRepeatCorrection').value;
        }

        data['error_type'] = document.getElementById('groupWrong').value;
        if (data['error_type'] != 'none' && data['error_type'] != 'groupWrongNextGroup' && data['error_type'] != 'definenInTeachingProgram') {
            data['error_value'] = document.getElementById(data['error_type'] + "Value").value;
        }
        
        return data;
    }


    function groupOrProgramCombo(combo) {
        var options = ["groupWrongRepeat", 'groupWrongTip', 'groupWrongRepeatAndCorrect', 'groupChooseCorrect',
            'groupReinforcementFrequencyValue'];
        var i;
        /*for (i=0; i<options.length; i++){
         var el = document.getElementById(options[i]);
         el.classList.add('d-none');
         }*/
        if (combo.id == "groupReinforcement") {
            var op = document.getElementById(combo.value);
            document.getElementById('groupChooseCorrect').classList.add('d-none');
            if (op != null && op.value != 'none') {
                op.classList.remove('d-none');
            }

        } else if (combo.id == "groupReinforcementFrequency") {
            document.getElementById('groupReinforcementFrequencyValue').classList.add('d-none');

            var op = document.getElementById(combo.value);

            if (op != null && op.value != 'none') {
                op.classList.remove('d-none');
            }
        } else if (combo.id == "groupWrong") {
            document.getElementById('groupWrongRepeat').classList.add('d-none');
            document.getElementById('groupWrongTip').classList.add('d-none');
            document.getElementById('groupWrongRepeatAndCorrect').classList.add('d-none');
            var op = document.getElementById(combo.value);

            if (op != null && op.value != 'none') {
                op.classList.remove('d-none');
            }
        }
    }
    function selectGroupReinforcement(type) {

    }


</script>

<!-- configuracao dos gruposs ou programa de ensino -->
<form  id='configGroupOrProgramTemplate' class="container" hidden>

    
    <div id="abc_data" class="d-none">
        <div class="row" >
            <div class="col"> <h2>Antecedente</h2> <input id='groupAntecedent' type="text" class="form-control" value=""></div>
        </div>
        <div class="row">
            <div class="col"> <h2>Resposta Esperada</h2> <input id='groupBehavior' type="text" class="form-control" value=""></div>
        </div>
        
    </div>
    
    <div id="activity_order">
        <div class="row" >
            <div class="col"> <h2>Ordem das atividades</h2> <h6>Define a ordem em que as atividades do programa de ensino serão apresentadas para o estudante</h6></div>
        </div>
        <div class="row">
            <div class="col">
                <select class="custom-select mr-sm-2" id="groupActivityOrder">
                      
                    <option class ="teachingProgramConstant_" selected value="followOrder">Seguir cada programa, em ordem</option>
                    <option value="random">Aleatória</option>
                </select>
            </div>
        </div>
    </div>

    <div>
        <div class="row mt-4">
            <div class="col"> <h2>Consequência (acerto) </h2> <h6> Apresentar alguma consequência em caso de acerto?</h6></div>
        </div>

        <div class="row mt-2">
            <div class="col">
                <select onchange="groupOrProgramCombo(this)" class="custom-select mr-sm-2" id="groupReinforcement">
                    
                    <option class ="teachingProgramConstant_" selected value="none">Nenhuma</option>
                    <option value="groupChooseCorrect">Recompensa</option>
                </select>
            </div>
        </div>
        <div class="mt-2 row d-none" id="groupChooseCorrect" >
            <div class="col-9">
                <input disabled type="text" class="form-control" id="groupSelectedReinforcement" placeholder="Selecionar">
            </div>
            <div class="col-3">
                <button id="selectGroupReinforcement" type="button" class="btn btn-primary  btn-block">Selecionar</button>
            </div>
        </div>
    </div>
    <div>
        <div id="correctFrequency">
            <div class="row mt-4">
                <div class="col"> <h2>Frequência de exibição da Consequência (acerto)</h2> </div>
            </div>

            <div class="row mt-2">
                <div class="col">
                    <select onchange="groupOrProgramCombo(this)" class="custom-select mr-sm-2" id="groupReinforcementFrequency">
                        <option class ="teachingProgramConstant_" selectConsequÃªncia ed value="definenInTeachingProgram">Definido pelo programa de ensino</option>       
                        <option value="atEveryCorrect">A cada acerto</option>
                        <option value="groupReinforcementFrequencyValue">A cada X acertos</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mt-4 row d-none" id="groupReinforcementFrequencyValue" >
            <div class="col">
                <labeL>Intervalo de apresentação do reforço:</labeL>
            </div>
            <div class="col">
                <input id="groupNumRepeatCorrection" type="number" min="1" value="1">
            </div>
            <div class="col">
                <labeL>acertos</labeL>
            </div>
        </div>
    </div>
    <div>
        <div class="row mt-4">
            <div class="col"> <h2>Consequência (erro) </h2> <h6>Alguma consequência em caso de erro?</h6> </div>
        </div>
        <div class="row">
            <div class="col">
                <select onchange="groupOrProgramCombo(this)"  class="custom-select mr-sm-2" id="groupWrong">
                    
                    <option class ="teachingProgramConstant_" selected value="none">Nenhuma</option>
                    <option  value="groupWrongNextGroup">Próximo Programa</option>
                    <option value="groupWrongRepeat">Repetir Atividade...</option>
                    <option value="groupWrongTip">Apresentar dica...</option>
                    <option value="groupWrongRepeatAndCorrect">Acertar X vezes consecutivas...</option>
                </select>
            </div>
        </div>
        <div class="mt-4 row d-none" id="groupWrongRepeat" >
            <div class="col">
                <labeL>Número de repetições:</labeL>
            </div>
            <div class="col">
                <input id="groupWrongRepeatValue" type="number" min="1" value="1">
            </div>

        </div>

        <div class="mt-4 row d-none" id="groupWrongTip" >
            <div class="col">
                <select class="custom-select mr-sm-2" id="groupWrongTipValue">
                    <option value="fadeIn">Fade-in</option>
                    <option value="shrink">Diminuir o tamanho do estí­mulo</option>
                    <option value="enlarge">Aumentar o tamanho do estí­mulo</option>
                    <option value="blink">Piscar borda</option>
                </select>
            </div>
        </div>
        <div class="mt-4 row d-none" id="groupWrongRepeatAndCorrect" >
            <div class="col">
                <labeL>Número de acertos consecutivos:</labeL>
            </div>
            <div class="col">
                <input id="groupWrongRepeatAndCorrectValue" type="number" min="1" value="1">
            </div>

        </div>
    </div>

</form>






<div class="row mt-3">
    <div class="col-3">
        <div class="card text-white bg-secondary ">
            <div class="card-header font-weight-bold text-uppercase">
                <div class="row">
                    <div class="col-9">
                        <?php
                        if ($data['curriculum_type'] == "curriculum")
                            echo "Currí­culo";
                        else
                            echo "Avaliação";
                        ?>

                    </div>
                    
                    <div class="col-3 d-none">
                        <button data-toggle="tooltip" data-placement="right" title="Pré-visualizar o currículo" onclick="previewGroup('', '<?php echo $programId; ?>')" id='runProgram' class="btn btn-lg btn-outline-light btn-success" type="button">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                </div>

            </div>
            <?php
            $student_id = $data['student_id'];
            $SQL = "SELECT *  FROM student WHERE id='$student_id'";

            $db = new DBAccess();
            $res = $db->query($SQL);
            if (!$res) {
                die("ERROR LOADING STUDENT $student_id. CONTACT ADMIN.");
            }

            $fetch = mysqli_fetch_assoc($res);
            ?>
            <div class="card text-white bg-danger border-dark" id="<?php echo $fetch['id']; ?>">
                <img class="img-fluid rounded img-thumbnail" width="100%" height="auto" src="<?php echo BASE_URL; ?>/data/student/<?php echo $student_id; ?>/avatar.png">
                <h4 class="card-header border-dark"><?php echo $fetch['name']; ?></h4>   
                <div class="card-body">



                    <p class="card-text">Nascimento: <?php $date=date_create($fetch['birthday']); echo date_format($date,"d/m/Y"); ?></p>
                    <p class="card-text">Endereço: <?php echo $fetch['city'];
            echo " - " . $fetch['state'];
            ?></p>
                    <p class="card-text">Medicação: <?php echo $fetch['medication']; ?></p>



                </div>
            </div>
        </div>
        <button class="mt-2 btn btn-block btn-lg btn-primary" type="button" onclick="javascript:askToAddGroup()">
            Adicionar Programa
        </button>
        
        
        
        
        <button class="mt-2 btn btn-block btn-lg btn-warning" type="button" onclick="javascript:askToAddAutoProgram()">
            Adicionar Programa MTS automaticamente
        </button>
        
        <!--<a href="<?php echo BASE_URL;?>/program/index.php?action=newAutoProgram&programId=<?php echo $programId;?>&type=MTS" class="mt-2 btn btn-block btn-lg btn-warning border border-dark" type="button" >
            Gerar Programa de MTS automaticamente (beta)
        </a>-->
    </div>
    <div class="col-9" id="groups"> 



    </div>
    <div id='help' style="position: absolute; top:5px; right: 30px;" >
    <button class='btn btn-block btn-lg btn-warning' onclick="showHelp()"><i class="fas fa-question"></i></button>

    </div>
</div>

<script>
    function askToAddAutoProgram(){
        var content = document.getElementById('newAutoTemplate').cloneNode(true);
        content.id="pirulito";
        content.classList.remove('d-none');
        showModal("Programa automático",content,function(){
            var name = document.getElementById('autoName').value;
            var cat  =document.getElementById('autoCategory').value;
            window.location.href="<?php echo BASE_URL;?>/program/index.php?action=newAutoProgram&programId=<?php echo $programId;?>&type=MTS&groupName="+name+"&autoCategory="+cat;
            
        });
    }
</script>

<div id="newAutoTemplate"  class="d-none">
     <div class="form-group row">
    <label for="autoName" class="col-sm-4 col-form-label">Nome do Programa</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="autoName" name="autoName" value="auto">
    </div>
  </div>

  <div class="form-group row">
    <label for="autoCategory" class="col-sm-4 col-form-label">Categoria (matemática, leitura, cor,...)</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="autoCategory" name="autoCategory" value="auto">
    </div>
  </div>
</div>


<!-- config group activity template -->
<form  id='configGroupActivityTemplate' class="container" hidden>
    <div class="row">
        <div class="col"> <h2>Consequência (acerto)</h2> </div>
    </div>

    <div class="row">
        <div class="col">
            <select class="custom-select mr-sm-2" id="correctSelect">
                
                <option selected value="none">Nenhuma</option>
                <option value="definedByGroup">Definido Pelo Programa</option>
                <option value="reinforcement">Recompensa</option>
            </select>
        </div>
    </div>
    <div class="mt-4 row d-none" id="chooseCorrect" >
        <div class="col-9">
            <input disabled type="text" class="form-control" id="selectedReinforcement" placeholder="Selecionar">
        </div>
        <div class="col-3">
            <button id="selectReinforcement" type="button" class="btn btn-primary  btn-block">Selecionar</button>
        </div>
    </div>



    <div class="row mt-4">
        <div class="col"> <h2>Consequência (erro) </h2> </div>
    </div>
    <div class="row">
        <div class="col">
            <select class="custom-select mr-sm-2" id="wrongSelect">
                
                <option selected value="definedByGroup">Definido pelo Programa</option>
                <option value="none">Nenhuma</option>

                <option value="repeat">Repetir...</option>
                <option value="tip">Apresentar dica...</option>
                <option value="repeatAndCorrect">Acertar X vezes consecutivas...</option>
            </select>
        </div>
    </div>
    <div class="mt-4 row d-none" id="wrongRepeat" >
        <div class="col">
            <labeL>Número de repetições:</labeL>
        </div>
        <div class="col">
            <input id="numRepeatCorrection" type="number" min="1" value="1">
        </div>

    </div>

    <div class="mt-4 row d-none" id="wrongTip" >
        <div class="col">
            <select class="custom-select mr-sm-2" id="wrongSelectTipType">
                <option value="fadeIn">Fade-in</option>
                <option value="shrink">Diminuir o tamanho do estí­mulo</option>
                <option value="enlarge">Aumentar o tamanho do estí­mulo</option>
                <option value="blink">Piscar borda</option>
            </select>
        </div>
    </div>
    <div class="mt-4 row d-none" id="wrongRepeatAndCorrect" >
        <div class="col">
            <labeL>Número de acertos consecutivos:</labeL>
        </div>
        <div class="col">
            <input id="numCorrectRepeat" type="number" min="1" value="1">
        </div>

    </div>

</form>


<!-- group template -->

<div class="row p-3 border mb-2" id="groupTemplate" hidden >
    <div class="col">
        <div class="row">
            <div class="col-8">
                <div class="row">
                    <div class="col-8">
                        
                        <div class="row">
                            <div class="col"><h1 id='descriptionTemplate'>Descrição do Programa de Atividades</h1></div>
                            
                        </div>
                    </div>
                    
                    <div class="col-2">
                        <button id='showContentTemplate' class="btn btn-block btn-lg btn-secondary plus_" type="button" data-toggle="collapse" 
                                data-target="#collapseTemplate" aria-expanded="false" aria-controls="collapseTemplate">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>

                    <div class="col-2">
                        <button id='configureGroupTemplate' class="d-none btn btn-block btn-lg btn-secondary" data-toggle="tooltip" data-placement="right" title="Configurar o programa" type="button">
                            <i class="fa fa-cogs"></i>
                        </button>
                    </div>

                </div>


            </div>
            <div class="col-2">
                <button id='previewTemplate' class="btn btn-block btn-lg btn-success" type="button" data-toggle="tooltip" data-placement="right" title="Pré-visualizar o programa"> <i class="fas fa-play"></i></button>
            </div>

            <div class="col-2">
                <button id='removeTemplate' class="btn btn-block btn-lg btn-danger" type="button" data-toggle="tooltip" data-placement="right" title="Remove o programa"> <i class="fas fa-trash-alt"></i></button>
            </div>
        </div>
        <div class="row " >
            <div  class="col collapse mt-3 mb-3" id="collapseTemplate">  
                <div class="row p-0">
                    <div class="col">
                        <div class="row">
                            <div class="col p-2">
                                <button  id='addActivityTemplate' class=" btn btn-block btn-lg  btn-outline-success" type="button" >
                                    Adicionar Atividade do Repositório
                                </button>        
                            </div>

                            <div class="col p-2">
                                <button  id='addNewActivityTemplate' class=" btn btn-block btn-lg  btn-outline-success" type="button" >
                                    Adicionar Nova Atividade ao Programa de Ensino
                                </button>        
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>   
            
            
        </div>
    </div>
</div>

<form class="form-row" id='newGroupTemplate' hidden="">

    <div class="form-group col-md-3">
        <label for='groupName' >Nome do Programa</label>
    </div>
    <div class="form-group col-md-9">
        <input required class="form-control mr-sm-2" id="groupName" name="groupName" type="text">
    </div>
    
    
    <div class="form-group col-md-3">
        <label for='groupAntecedent' >Antecedente</label>
    </div>
    <div class="form-group col-md-9">
        <input required class="form-control mr-sm-2" id="groupAntecedent" name="groupAntecedent" type="text">
    </div>

    
    
    <div class="form-group col-md-3">
        <label for='groupBehavior' >Resposta Esperada</label>
    </div>
    <div class="form-group col-md-9">
        <input required class="form-control mr-sm-2" id="groupBehavior" name="groupBehavior" type="text">
    </div>
    
    <div class="form-group col-md-3">
        <label for='groupConsequence' >Consequência</label>
    </div>
    <div class="form-group col-md-9">
        <input required class="form-control mr-sm-2" id="groupConsequence" name="groupConsequence" type="text">
    </div>

    <div class="form-group col-md-3">
        <label for='groupCategory' >Categoria (matemática, leitura, cor, ...)</label>
    </div>
    <div class="form-group col-md-9">
        <input required class="form-control mr-sm-2" id="groupCategory" name="groupCategory" type="text">
    </div>

</form>


<div  id='activityContainerTemplate' hidden>
    <div class="row mt-4">
        <div class="col-12">

            <div class="form-row">
                <div class="form-group col-lg-11">
                    <input   class="form-control mr-sm-2" id="search" name="query" type="query" placeholder="Filtrar " aria-label="Search" value="">
                </div>
                <div class="form-group col-lg-1">
                    <button type="button" id='searchButton' class="btn btn-outline-success form-control" >
                        <i class="fas fa-search"></i> 
                    </button>
                </div>
            </div> 
        </div>
    </div>
    <div class='row'>
        <div class='col-12'>
            <div id='activityContainerContent' class='card-columns'>

            </div>
        </div>

    </div>

    <div class='row'>
        <div class='col-12'>
            <button id="buttonShowMoreTemplate" type="button"  class="btn btn-block btn-primary">
                Mostrar mais
            </button>
        </div>
    </div>
</div>







<script>

    /* groupActivity Configuration. */

    function selectReinforcement(groupActivity, query = "", type = "", data) {
        var xhr = new XMLHttpRequest();
        
        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var ret = this.responseText;// JSON.parse(this.responseText);

                if (ret == "ERROR") {
                    return;
                }

                if (ret.length <= 0) {

                    return;
                }

                var obj = JSON.parse(this.responseText, true);


                var i;
                var container = document.getElementById('activityContainer');
                if (container == null)
                    container = document.getElementById('activityContainerTemplate').cloneNode(true);

                var searchInput = container.querySelector('#search');
                searchInput.onkeyup = function (e) {
                    var keynum;
                    if (window.event) { // IE                    
                        keynum = e.keyCode;
                    } else if (e.which) { // Netscape/Firefox/Opera                   
                        keynum = e.which;
                    }
                    if (keynum == 13)
                    {
                        var container = document.getElementById('activityContainerContent');
                        var query = document.getElementById('search').value;
                        
                        var group = container.getAttribute('data-group');

                        container.innerHTML = "";

                        selectReinforcement(group, query, type, data);

                    }
                };

                var searchButton = container.querySelector('#searchButton');
                searchButton.onclick = function () {

                    var container = document.getElementById('activityContainerContent');
                    var query = document.getElementById('search').value;
                    
                    var group = container.getAttribute('data-group');

                    container.innerHTML = "";


                    selectReinforcement(group, query, type, data);

                };


                var showMoreButton = container.querySelector("#buttonShowMoreTemplate");
                if (showMoreButton == null)
                    showMoreButton = container.querySelector("#buttonShowMore");
                showMoreButton.id = "buttonShowMore";
                showMoreButton.disabled = false;

                showMoreButton.onclick = function () {
                    var container = document.getElementById('activityContainer');
                    var query = document.getElementById('search').value;
                    var group = container.getAttribute('data-group');

                    selectActivity(groupActivity, query);
                }
                if (obj.length <= 0) {

                    showMoreButton.disabled = true;
                }
                container.hidden = false;
                container.id = "activityContainer";
                container.setAttribute('data-group', groupActivity);


                var content = container.querySelector("#activityContainerContent");
                for (i = 0; i < obj.length; i++) {
                    var card = //'<div class="card bg-light" id="' + obj[i]['id']+ '">' +
                            '<h4 class="card-header">' + obj[i]['name'] + '</h4>' +
                            '<img class="card-img-top rounded  img-thumbnail" src="' + obj[i]['thumb'] + '">' +
                            '<div class="card-body">' +
                            //'<h4 class="card-text">Antecedente</h4>' +
                            //'<p class="card-text">' + obj[i]['antecedent'] + '</p>' +
                            //'<h4 class="card-text">Comportamento Esperado</h4>' +
                            //'<p class="card-text">' + obj[i]['behavior'] + '</p>' +
                            //'<h4 class="card-text">Consequência</h4>' +
                            //'<p class="card-text">' + obj[i]['consequence'] + '</p>' +
                            //'<cite class="card-text">' + obj[i]['category'] + '</cite>' +
                            '<div class="container-fluid">' +
                            '<div class="row">' +
                            '<div class="col-6"><button data-actId="' + obj['id'] + '" type="button"  id="selButton" class="btn btn-block btn-primary">Selecionar</button></div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';//+
                    //'</div>';

                    var card_div = document.createElement('div');
                    card_div.classList.add('card', 'bg-light');
                    card_div.id = obj[i]['id'];


                    card_div.innerHTML = card;

                    var selectButton = card_div.querySelector('#selButton');
                    selectButton.setAttribute('data-actId', obj[i]['id']);
                    selectButton.onclick = function () {
                        var id = this.getAttribute('data-actId');
                        

                        selectReward(groupActivity, id, type, data);

                    };
                    content.appendChild(card_div);

                }
                if (type == "activity")
                    swapModalContent(container, function () {
                        saveGroupActivityConfig(id);
                    }, function () {
                        showActivityConfig(groupActivity, data);
                    });
                else if (type == "groupOrProgram") {
                    swapModalContent(container, function () {
                        saveGroupOrProgramConfig(id, "");
                    }, function () {
                        showGroupOrProgramConfig(groupActivity, data);
                    });
                }

            }

        };

        document.getElementById('search').value = query;

        var d = document.getElementById('activityContainerContent');
        var offset = 0;
        if (d != null)
            offset = countSons(d);

        xhr.open("GET", '<?php echo BASE_URL; ?>/activity/index.php?action=getReinforcers_json&offset=' + offset + "&query=" + query, true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

        xhr.send("");
    }
    function countSons(element){
    
    var activities = element.childNodes;
    var num = 0;
    for (var i = 0; i < activities.length; i++) {
        if (activities[i].nodeType != Node.TEXT_NODE) {
            num++;
        }
    }
    return num;
}

    function backupFormData() {

        var data = [];
        data['reinforcement_type'] = document.getElementById('correctSelect').value;
        data['reinforcement_value'] = "";
        if (data['reinforcement_type'] == 'reinforcement') {
            var inp = document.getElementById('selectedReinforcement');
            data['reinforcement_value'] = inp.value;
        }


        data['correction_type'] = document.getElementById('wrongSelect').value;
        data['correction_value'] = "";
        if (data['correction_type'] == 'repeat') {
            var inp = document.getElementById('numRepeatCorrection');
            data['correction_value'] = inp.value;
        } else if (data['correction_type'] == 'tip') {
            var inp = document.getElementById('wrongSelectTipType');
            data['correction_value'] = inp.value;
        } else if (data['correction_type'] == 'repeatAndCorrect') {
            var inp = document.getElementById('numCorrectRepeat');
            data['correction_value'] = inp.value;
        }


        return data;
    }

    function saveGroupActivityConfig(groupActivity) {
        
        var data = backupFormData();

        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                closeModal();
            }
        };
        var str_json = JSON.stringify(Object.assign({}, data));

        xhr.open("POST", '<?php echo BASE_URL; ?>/program/index.php?action=groupActivity_set', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

        xhr.send("id=" + groupActivity + "&keys=" + str_json);

    }


    function selectReward(groupActivity, reward_id, type = "", data) {
        if (type.length <= 0) {
            throw new Error("which type?");
        } else
            
        if (type == "activity") {
            
            data['reinforcement_type'] = 'reinforcement';
            data['reinforcement_value'] = reward_id;

            showActivityConfig(groupActivity, data);
        } else if (type == "groupOrProgram") {
            
            data['reinforcement_type'] = 'groupChooseCorrect';
            data['reinforcement_value'] = reward_id;

            showGroupOrProgramConfig(groupActivity, data);
    }
    }

    function correctComboBoxSelect(element, groupActivityId, type = "") {

        var form = document.getElementById('configActivity');
        var correctChooseLine = form.querySelector('#chooseCorrect');
        correctChooseLine.classList.add('d-none');
        if (element.value == "reinforcement") {
            correctChooseLine.classList.remove('d-none');
    }

    }
    function wrongComboBoxSelect(element, groupActivityId, type = "") {
        

        var form = document.getElementById('configActivity');
        var wrongChooseLine = form.querySelector('#wrongRepeat');
        var wrongTipLine = form.querySelector('#wrongTip');
        var wrongRepeatLine = form.querySelector('#wrongRepeatAndCorrect');

        wrongTipLine.classList.add('d-none');
        wrongChooseLine.classList.add('d-none');
        wrongRepeatLine.classList.add('d-none');

        if (element.value == "repeat") {
            wrongChooseLine.classList.remove('d-none');
        } else if (element.value == "tip") {
            wrongTipLine.classList.remove('d-none');
        } else if (element.value == "repeatAndCorrect") {
            wrongRepeatLine.classList.remove('d-none');
    }


    }


    function showActivityConfig(id, data = null) {
        
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var ret = this.responseText;// JSON.parse(this.responseText);
                var obj = JSON.parse(ret)[0];
                
                var form = document.getElementById('configGroupActivityTemplate').cloneNode(true);
                form.id = "configActivity";


                form.hidden = false;
                var correctCombo = form.querySelector('#correctSelect');
                
                if (data != null) {
                    correctCombo.value = data['reinforcement_type'];
                } else {
                    correctCombo.value = obj['reinforcement_type'];
                }

                if (correctCombo.value == "reinforcement") {
                    var inp = form.querySelector('#selectedReinforcement');

                    var comboLine = form.querySelector('#chooseCorrect');
                    comboLine.classList.remove('d-none');

                    if (data == null) {
                        inp.value = obj['reinforcement_value'];
                    } else {
                        inp.value = data['reinforcement_value'];
                    }
                }

                correctCombo.onchange = function () {
                    correctComboBoxSelect(correctCombo, id);
                };

                var selectReinforcementButton = form.querySelector('#selectReinforcement');
                selectReinforcementButton.onclick = function () {
                    var data = backupFormData();
                    selectReinforcement(id, "", 'activity', data);
                };
                var wrongCombo = form.querySelector('#wrongSelect');


                form.querySelector('#wrongRepeat').classList.add('d-none');
                form.querySelector('#wrongTip').classList.add('d-none');
                form.querySelector('#wrongRepeatAndCorrect').classList.add('d-none');


                if (data == null) {
                    wrongCombo.value = obj['correction_type'];
                } else {
                    wrongCombo.value = data['correction_type'];
                }
                if (wrongCombo.value == 'repeat') {
                    var inp = form.querySelector('#numRepeatCorrection');
                    form.querySelector('#wrongRepeat').classList.remove('d-none');
                    if (data == null) {
                        inp.value = obj['correction_value'];
                    } else {
                        inp.value = data['correction_value'];
                    }
                } else if (wrongCombo.value == 'tip') {
                    var inp = form.querySelector('#wrongSelectTipType');
                    form.querySelector('#wrongTip').classList.remove('d-none');
                    if (data == null) {
                        inp.value = obj['correction_value'];
                    } else {
                        inp.value = data['correction_value'];
                    }
                } else if (wrongCombo.value == 'repeatAndCorrect') {
                    var inp = form.querySelector('#numCorrectRepeat');
                    form.querySelector('#wrongRepeatAndCorrect').classList.remove('d-none');
                    if (data == null) {
                        inp.value = obj['correction_value'];
                    } else {
                        inp.value = data['correction_value'];
                    }
                }

                wrongCombo.onchange = function () {
                    wrongComboBoxSelect(wrongCombo, id);
                };

                if (data == null) {
                    
                    showModal("ConfiguraÃ§Ãµes de consequÃªncia da Atividade", form, function () {
                        saveGroupActivityConfig(id);
                    });
                } else
                    swapModalContent(form, function () {
                        saveGroupActivityConfig(id);
                    });
            }
        };

        xhr.open("GET", '<?php echo BASE_URL; ?>/program/index.php?action=getGroupActivity_json&id=' + id, true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

        xhr.send("");

    }

</script>


<script>
    function addActivityToGroupHtml(obj, group, activity) {
        var row = document.createElement('div');
        var htmlId = 'act' + globalActId;
        globalActId++;
        row.id = htmlId;
        row.setAttribute('data-groupActivity-id', obj['group_activity_id']);
        row.setAttribute('data-group', group);
        row.setAttribute('data-activity', activity);
        row.setAttribute('data-type', 'activity');
        row.setAttribute('data-position', obj['position']);

        /*row.setAttribute('data-reinforcementType', obj['reinforcement_type']);
         row.setAttribute('data-reinforcementValue', obj['reinforcement_value']);
         
         row.setAttribute('data-consequenceType', obj['correction_type']);
         row.setAttribute('data-consequenceValue', obj['correction_value']);
         */
        row.classList.add('row', 'alert', 'alert-primary');


        var name = document.createElement('div');
        name.classList.add('col-7');
        name.innerHTML = obj['name'];

        var antecedent = document.createElement('div');
        antecedent.classList.add('col-2');
        antecedent.innerHTML = obj['antecedent'];

        var behavior = document.createElement('div');
        behavior.classList.add('col-2');
        behavior.innerHTML = obj['behavior'];

        var consequence = document.createElement('div');
        consequence.classList.add('col-1');
        consequence.innerHTML = obj['consequence'];

        var config = document.createElement('div');
        config.classList.add('col-1');
        config.onclick = function () {
            showActivityConfig(obj['group_activity_id'], null);
        };



        var up = document.createElement('div');
        up.classList.add('col-1');
        up.onclick = function () {
            moveUp(group, htmlId);
        };

        var down = document.createElement('div');
        down.classList.add('col-1');
        down.onclick = function () {
            moveDown(group, htmlId);
        }

        var remove = document.createElement('div');
        remove.classList.add('col-1');
        remove.onclick = function () {
            askToRemoveActivity(group, htmlId);
        };
        down.setAttribute('data-toggle',"tooltip");
        down.setAttribute('data-placement',"right");
        down.title="Mover atividade para baixo";
        
        var viewEdit = document.createElement('div');
        viewEdit.classList.add('col-1');
        viewEdit.innerHTML = '<button class="btn btn-block btn-lg btn-info"><i class="far fa-edit"></i></button>';
        viewEdit.onclick = function () {
            window.open("<?php echo BASE_URL; ?>/activity/index.php?action=edit&id="+obj['id']);
        };
        viewEdit.setAttribute('data-toggle',"tooltip");
        viewEdit.setAttribute('data-placement',"right");
        viewEdit.title="Editar atividade";

        remove.innerHTML = '<button class="btn btn-block btn-lg btn-danger"><i class="fas fa-trash-alt"></i></button>';
        remove.setAttribute('data-toggle',"tooltip");
        remove.setAttribute('data-placement',"right");
        remove.title="Remover atividade";
        
        up.innerHTML = '<button class="btn btn-block btn-lg btn-info"><i class="fas fa-arrow-circle-up"></i></button>';
        up.setAttribute('data-toggle',"tooltip");
        up.setAttribute('data-placement',"right");
        up.title="Mover atividade para cima";
        
        config.innerHTML = '<button class="btn btn-block btn-lg btn-info"><i class="fas fa-cogs"></i></button>';
        config.setAttribute('data-toggle',"tooltip");
        config.setAttribute('data-placement',"right");
        config.title="Configurar consequências";
        
        
        down.innerHTML = '<button class="btn btn-block btn-lg btn-info"><i class="fas fa-arrow-circle-down"></i></button>';


        row.appendChild(name);
        //row.appendChild(antecedent);
        //row.appendChild(behavior);
        //row.appendChild(consequence);
        row.appendChild(viewEdit);
        //row.appendChild(config);
        row.appendChild(up);
        row.appendChild(down);
        row.appendChild(remove);

        var container = document.getElementById('content' + group);
        var button = document.getElementById('add' + group);
        //container.parentNode.insertBefore(row,container);
        button.parentNode.parentNode.parentNode.insertBefore(row, button.parentNode.parentNode);
    }


    function selectActivity(group, query = "") {
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var data = this.responseText;// JSON.parse(this.responseText);

                if (data == "ERROR") {
                    return;
                }

                if (data.length <= 0) {

                    return;
                }

                var obj = JSON.parse(this.responseText, true);


                var i;
                var container = document.getElementById('activityContainer');
                if (container == null)
                    container = document.getElementById('activityContainerTemplate').cloneNode(true);
                var searchInput = container.querySelector('#search');
                searchInput.onkeyup = function (e) {
                    var keynum;
                    if (window.event) { // IE                    
                        keynum = e.keyCode;
                    } else if (e.which) { // Netscape/Firefox/Opera                   
                        keynum = e.which;
                    }
                    if (keynum == 13)
                    {
                        var container = document.getElementById('activityContainerContent');
                        var query = document.getElementById('search').value;

                        var group = container.getAttribute('data-group');

                        container.innerHTML = "";

                        selectActivity(group, query);
                    }
                };

                var searchButton = container.querySelector('#searchButton');
                searchButton.onclick = function () {

                    var container = document.getElementById('activityContainerContent');
                    var query = document.getElementById('search').value;

                    var group = container.getAttribute('data-group');

                    container.innerHTML = "";

                    selectActivity(group, query);
                };
                var showMoreButton = container.querySelector("#buttonShowMoreTemplate");
                if (showMoreButton == null)
                    showMoreButton = container.querySelector("#buttonShowMore");
                showMoreButton.id = "buttonShowMore";
                showMoreButton.disabled = false;
                showMoreButton.onclick = function () {
                    var container = document.getElementById('activityContainer');
                    var query = document.getElementById('search').value;
                    var group = container.getAttribute('data-group');

                    selectActivity(group, query);
                }
                if (obj.length <= 0) {

                    showMoreButton.disabled = true;
                }
                container.hidden = false;
                container.id = "activityContainer";
                container.setAttribute('data-group', group);
                var content = container.querySelector("#activityContainerContent");
                for (i = 0; i < obj.length; i++) {
                    var card = '<div class="card bg-light" id="' + obj[i]['id'] + '">' +
                            '<img class="card-img-top rounded  img-thumbnail" src="' + obj[i]['thumb'] + '">' +
                            '<h4 class="card-header">' + obj[i]['name'] + '</h4>' +
                            '<div class="card-body">' +
                            //'<h4 class="card-text">Antecedente</h4>' +
                            //'<p class="card-text">' + obj[i]['antecedent'] + '</p>' +
                            //'<h4 class="card-text">Comportamento Esperado</h4>' +
                            //'<p class="card-text">' + obj[i]['behavior'] + '</p>' +
                            //'<h4 class="card-text">ConsequÃªncia</h4>' +
                            //'<p class="card-text">' + obj[i]['consequence'] + '</p>' +
                            //'<cite class="card-text">' + obj[i]['category'] + '</cite>' +
                            '<div class="container-fluid">' +
                            '<div class="row">' +
                            '<div class="col-6"><a href="javascript:addActivity(\'' + group + '\',\'' + obj[i]['id'] + '\')" class="btn btn-block btn-primary">Selecionar</a></div>' +
                            '<div class="col-6"><a href="index.php?action=edit&id=' + obj[i]['id'] + '" class="btn btn-block btn-primary">Visualizar</a></div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                    content.innerHTML += card;
                    //var card = document.createElement('div');
                    //card.classList.add("card");
                }
                swapModalContent(container);
                //showModal("Selecionar atividade",container,null, false);
            }

        };

        document.getElementById('search').value = query;

        var d = document.getElementById('activityContainerContent');
        var offset = 0;
        if (d != null)
            offset =countSons(d);


        
        var url = '<?php echo BASE_URL; ?>/activity/index.php?action=getActivities_json&offset=' + offset + "&query=" + query;
        xhr.open("GET", '<?php echo BASE_URL; ?>/activity/index.php?action=getActivities_json&offset=' + offset + "&query=" + query, true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        var progId = "<?php echo $programId; ?>";
        xhr.send("");
    }


    function loadFromDatabase() {
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var data = this.responseText;// JSON.parse(this.responseText);

                if (data == "ERROR") {
                    return;
                }
                console.log(this.responseText);

                var obj = JSON.parse(this.responseText, true);
                var i;

                for (i = 0; i < obj.length; i++) {
                    addGroup(obj[i]['name'], obj[i]['id'], obj[i]);
                    if(obj[i]['name']!="AUTO"){
                        var j;
                        for (j = 0; j < obj[i]['activities'].length; j++) {
                            var act = obj[i]['activities'][j];

                            addActivityToGroupHtml(act, obj[i]['id'], act['id']);
                        }
                    }   
                }

            }


        };

        xhr.open("POST", '<?php echo BASE_URL; ?>/program/index.php?action=getGroups_json', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        
        var progId = "<?php echo $programId; ?>";
        console.log("CURR ID: " + progId);
        xhr.send("programId=" + progId);
    }

    function askToRemove(group) {
        showModal("Deseja remover o Programa? ", "Deseja remover o Programa? Isso não pode ser desfeito.", function () {


            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText;// JSON.parse(this.responseText);

                    if (data == "ERROR") {
                        return;
                    }
                    
                    var g = document.getElementById(group);
                    g.parentNode.removeChild(g);
                    
                    closeModal();
                }


            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/program/index.php?action=removeGroup', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            var progId = "<?php echo $programId; ?>";
            xhr.send("programId=" + progId + "&groupId=" + group);


        }, true);
    }


    function askToRemoveActivity(group, activity_htmlId) {
        showModal("Deseja remover a atividade? ", "Deseja remover a atividade do programa? Ela continuará existindo, mas não estará mais no programa. A atividade poderá¡ ser adicionada novamente.", function () {


            var xhr = new XMLHttpRequest();

            var rem = document.getElementById(activity_htmlId);

            var group = rem.getAttribute('data-group');

            var remId = rem.getAttribute('data-groupActivity-id');


            var acts = document.querySelectorAll('[data-group="' + group + '"]');
            
            var jsonHtml = [];
            var json = [];
            if (rem.getAttribute('data-position') < acts.length) {
                //not the last

                var i;
                var start = rem.getAttribute('data-position');
                for (i = start; i < acts.length; i++) {

                    json[acts[i].getAttribute('data-groupActivity-id')] = i;
                    jsonHtml[acts[i].id] = i;
                }

            }
            
            var str_json = JSON.stringify(Object.assign({}, json));

            xhr.onreadystatechange = function () {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText;// JSON.parse(this.responseText);

                    if (data == "ERROR") {
                        return;
                    }
                    
                    var i;
                    
                    for (var key in jsonHtml) {
                        
                        var act = document.getElementById(key);
                        act.setAttribute('data-position', jsonHtml[key]);
                    }
                    rem.parentNode.removeChild(rem);
                }
                closeModal();


            };




            xhr.open("POST", '<?php echo BASE_URL; ?>/program/index.php?action=removeActivityFromGroup', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            var progId = "<?php echo $programId; ?>";
            xhr.send("groupId=" + group + "&removeId=" + remId + "&activities=" + str_json);


        }, true);
    }

    function askToAddGroup() {
        var form = document.getElementById('newGroupTemplate').cloneNode(true);
        form.id = "newGroup";

        form.hidden = false;

        showModal("Nome do novo Programa ", form, function () {



            var name = form.querySelector("#groupName");
            var antecedent = form.querySelector("#groupAntecedent");
            var behavior = form.querySelector("#groupBehavior");
            var consequence = form.querySelector("#groupConsequence");
            var category = form.querySelector("#groupCategory");
            if (!name.checkValidity()) {
                name.classList.add('border', 'border-danger');
                return;
            }

            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText;// JSON.parse(this.responseText);
                    
                    if (data == "ERROR") {
                        return;
                    }
                    addGroup(name.value, data);
                    closeModal();
                }


            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/program/index.php?action=addGroup', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            var progId = "<?php echo $programId; ?>";
            xhr.send("programId=" + progId + "&groupName=" + name.value+"&groupAntecedent="+antecedent.value+"&groupBehavior="+behavior.value+"&groupConsequence="+consequence.value+"&groupCategory="+category.value);


        }, true);
    }


    function moveUp(group, activity) {

        var node = document.getElementById(activity);


        var prev = node.previousElementSibling;
        if (prev == null)
            return;


        if (prev != null && prev.getAttribute('data-type') == 'activity') {

            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText;

                    if (data == "ERROR") {
                        return;
                    }
                    var parent = node.parentNode;

                    parent.removeChild(node);
                    parent.insertBefore(node, prev);
                    var t = node.getAttribute('data-position');

                    node.setAttribute('data-position', prev.getAttribute('data-position'));
                    prev.setAttribute('data-position', t);
                }


            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/program/index.php?action=swapActivity', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");


            var activitiId1 = node.getAttribute('data-groupActivity-id');
            var activityId2 = prev.getAttribute('data-groupActivity-id');
            var position1 = node.getAttribute('data-position');
            var position2 = prev.getAttribute('data-position');
            xhr.send("groupId=" + group + "&activity1=" + activitiId1 + "&activity2=" + activityId2 + "&position1=" + position1 + "&position2=" + position2);


        }
    }

    function moveDown(group, activity) {
        var node = document.getElementById(activity);
        

        var next = node.nextElementSibling;
        

        if (next == null || next.getAttribute('data-type') == 'activity') {

            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText;
                    
                    if (data == "ERROR") {
                        return;
                    }
                    var parent = node.parentNode;

                    parent.removeChild(next);
                    parent.insertBefore(next, node);
                    var t = node.getAttribute('data-position');

                    node.setAttribute('data-position', next.getAttribute('data-position'));
                    next.setAttribute('data-position', t);
                }
            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/program/index.php?action=swapActivity', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            var progId = "<?php echo $programId; ?>";

            var activitiId1 = node.getAttribute('data-groupActivity-id');
            var activityId2 = next.getAttribute('data-groupActivity-id');
            var position1 = node.getAttribute('data-position');
            var position2 = next.getAttribute('data-position');
            xhr.send("groupId=" + group + "&activity1=" + activitiId1 + "&activity2=" + activityId2 + "&position1=" + position1 + "&position2=" + position2);
        } else {
            return;
        }
    }




    function addActivity(group, activity) {


        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var data = this.responseText;

                if (data == "ERROR") {
                    return;
                }


                var obj = JSON.parse(this.responseText, true);

                addActivityToGroupHtml(obj, group, activity);

                closeModal();

            }


        };
        var acts = document.querySelectorAll('[data-group="' + group + '"][data-type="activity"]');
        var pos = acts.length + 1;



        xhr.open("POST", '<?php echo BASE_URL; ?>/program/index.php?action=addActivityToGroup', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        var progId = "<?php echo $programId; ?>";
        xhr.send("groupId=" + group + "&activityId=" + activity + "&position=" + pos);
    }

    function previewGroup(group, progId) {

        var url;
        if (group.length > 0)
            url = "<?php echo BASE_URL; ?>/program/index.php?action=run&type=program&preview=preview&programId=" + group + "&curriculumId=" + progId;
        else
            url = "<?php echo BASE_URL; ?>/program/index.php?action=run&type=curriculum&preview=preview&curriculumId=" + progId;
        window.open(url);
    }

    function addGroup(name, id, obj = null) {
        
        


        var ng = document.getElementById('groupTemplate').cloneNode(true);
        ng.id = id;

        var content = ng.querySelector('#collapseTemplate');
        content.addEventListener("hidden.bs.collapse", function (e) {
            process(e.detail)
        });
        content.id = "content" + id;

        var desc = ng.querySelector('#descriptionTemplate');
        desc.innerHTML = name;

        var button = ng.querySelector('#showContentTemplate');
        
        if( (obj==null) || ("auto" in obj && obj['auto']!=1) ){
            button.id = "show" + id;
            button.setAttribute('data-target', "#content" + id);// dataTarget =;
            button.setAttribute('data-target', "#content" + id);
            button.ariaControls = "content" + id;
        }else{
            button.classList.remove('btn-secondary');
            button.classList.add('btn-warning');
            button.setAttribute('data-toggle','tooltip');
            button.setAttribute('data-placement','right');
            button.setAttribute('data-title','Gerar atividades do programa');
            button.innerHTML='<i class="fas fa-clipboard-list"></i>';
            button.id = "editAuto" + id;
            button.onclick=function(){
                window.open("<?php echo BASE_URL;?>/program/index.php?action=editAutoProgram&programId="+id);
            };
        }


        var remBut = ng.querySelector('#removeTemplate');
        remBut.id = "remove" + id;
        remBut.onclick = function () {
            var groupId = ng.id;
            askToRemove(groupId);
        };

        var prevBut = ng.querySelector('#previewTemplate');
        prevBut.id = "preview" + id;
        prevBut.onclick = function () {
            var groupId = ng.id;
            var programId = "<?php echo $programId; ?>";
            previewGroup(groupId, programId);
        };

        var addBut = ng.querySelector('#addActivityTemplate');
        addBut.id = "add" + id;
        addBut.onclick = function () {
            showModal("Selecionar atividade", "", null, false);
            selectActivity(ng.id);
        };
        
        var addNewBut = ng.querySelector('#addNewActivityTemplate');
        addNewBut.id="add"+id;
        addNewBut.onclick= function(){
          window.location.href = "<?php echo BASE_URL;?>/activity/index.php?action=new&groupId="+id;
          //window.selectActivity(ng.id);  
        };

        var confButt = ng.querySelector('#configureGroupTemplate');
        
        confButt.onclick = function () {
            //TODO: showGroupOrProgramConfig(id);
            configProgramOrGroup(id, 'group');
        };


        ng.hidden = false;
        document.getElementById('groups').appendChild(ng);

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    }




</script>
