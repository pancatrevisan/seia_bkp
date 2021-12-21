<?php

if (!defined('ROOTPATH')) {
    require '../root.php';
}

require ROOTPATH . "/ui/modal.php";
?>
<script>
    var DIFFICULTY = {
        "NOT_RATED": "Não avaliado",
        "EASY": "Fácil",
        "MEDIUM": "Médio",
        "HARD": "Difícil"
    };

    function showHelp() {
        var content =
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/lHzMbF6vv7E" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        showModal("Ajuda", content);
    }

    function showTipForTip() {
        var content =
            'Próxima atividade (<b>acerto após correção/padrão</b>) é a atividade executada caso o estudante acerte a atividade após ter errado e uma dica ter sido apresentada <br>' +
            'Próxima atividade (<b>erro após correção</b>) é executada caso o estudante erre a atividade, a dica seja apresentada e ele erre mesmo assim.<br>' +
            'Caso não seja configurada uma atividade para "Próxima atividade (<b>erro após correção</b>)", será executada a atividade "Próxima atividade (<b>acerto após correção/padrão</b>)".';

        showModal("Ajuda", content);
    }

    function updateSessionName() {
        var name = document.getElementById('sessionName').value;
        if (name.length > 0) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {

                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText;
                    console.log(data);
                }
            }
            xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=updateSessionProgramName', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

            xhr.send("&sessionId=" + sessionProgram_id + "&sessionName=" + name);
        }
    }

    var sessionProgram_id = "<?php echo $data['sessionId']; ?>";
    document.body.onload = function() {
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })


        //load from database...

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var data = this.responseText;
                if (data == "ERROR") {
                    return;
                }
                document.getElementById('activities').innerHTML = "";
                
                data = JSON.parse(data, true);
                console.log(data);
                if (data['follow_activity_order'] == 1) {
                    document.getElementById('followOrder').checked = true;
                }
                if (data['follow_activity_order'] == 2) {
                    document.getElementById('followOrder_nobaseline').checked = true;
                }
                document.getElementById('sessionName').value = data['name'];
                for (var i = 0; i < data['activities'].length; i++) {
                    addActivityToHTML(data['activities'][i]);
                }
                checkActivities(data);
            }
        };

        xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=loadSessionProgram', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

        xhr.send("&session_id=" + sessionProgram_id);

    }
</script>

<!-- aqui -->
<script>

//se é um valor vazio para uma configuração, apaga o valor da configuração.
function checkEmptyValue(object, value, toClean, elementToClean=null){
    if(object.value == "none" || object.value == "undefined" || object.value == null || object.value == "null"){
        
        if(elementToClean!=null){
            var el = document.getElementById(elementToClean);
            if(el!=null){
                el.value = "";
            }
        }
    }
}

function checkMissingValue(object, attribute, value, emptyAttributeValue, whoToTell, mainId, data){
    
    //if(object[attribute]!=emptyAttributeValue){//se nao tiver o valor de 'vazio', coloca uma mensagem
        
        if(attribute == "spa_reinforcer_type" && object[attribute]!=emptyAttributeValue && object[attribute]!="auto_reinforce"){
            if(object[value].length <= 0 || object[value] == null || object[value] == "null" || object[value] == undefined || object[value] == "undefined"){
                var el = document.getElementById(whoToTell);
            var newNode = document.createElement('span');
            newNode.classList.add("badge","badge-warning","badge_panca");
            newNode.innerHTML = "VERIFICAR!";
            el.parentNode.insertBefore(newNode, el.nextSibling);
            var activityRow = document.getElementById(mainId);
            activityRow.classList.add("border","border-warning");
            }
            return;
        }
        
        //caso seja configuracao de proxima, verifica se nao está vazio e se está no programa de ensino.
        if(attribute == "spa_next_after_correction" || attribute == "spa_next_after_correction_wrong" || attribute == "spa_next_on_correct" || attribute == "spa_next_on_wrong"){

            if(object[value].length <= 0 || object[value] == null || object[value] == "null" || object[value] == undefined || object[value] == "undefined" ||object[value] == "nenhuma"){
                var el = document.getElementById(whoToTell);
                var newNode = document.createElement('span');
                newNode.classList.add("badge","badge-warning","badge_panca");
                newNode.innerHTML = "VERIFICAR!";
                el.parentNode.insertBefore(newNode, el.nextSibling);
                var activityRow = document.getElementById(mainId);
                activityRow.classList.add("border","border-warning");
            }
            else{
                console.log(object[value] + " in actvitities?");
                var inList = false;
                for (var a  = 0; a < data['activities'].length; a++){

                    if(data['activities'][a].id == object[value] ){
                        inList = true;
                    }
                }

                if(!inList){
                    var el = document.getElementById(whoToTell);
                    var newNode = document.createElement('span');
                    newNode.classList.add("badge","badge-warning","badge_panca");
                    newNode.innerHTML = "VERIFICAR!";
                    el.parentNode.insertBefore(newNode, el.nextSibling);
                    var activityRow = document.getElementById(mainId);
                    activityRow.classList.add("border","border-warning");
                }
            }
        }    
   // }
}

function checkActivities(data){
    for (var i =0; i < data['activities'].length; i++){
        var a = data['activities'][i];
        
        checkMissingValue(a, "spa_reinforcer_type","spa_reinforcer_value","none", "reinfInput_"+a.id, a.id, data);

        if(data.follow_activity_order == 0){
            checkMissingValue(a, "spa_next_on_correct","spa_next_on_correct_id","", "nextActivityOnCorrect_"+a.id, a.id, data);
            checkMissingValue(a, "spa_next_on_wrong","spa_next_on_wrong_id","", "nextActivityOnError_"+a.id, a.id, data);

            if(a.spa_correction_type!="none"){
                checkMissingValue(a, "spa_next_after_correction","spa_next_after_correction_id","", "nextActivityAfterCorrection_"+a.id, a.id, data);
                checkMissingValue(a, "spa_next_after_correction_wrong","spa_next_after_correction_wrong_id","", "nextActivityOnCorrectionErrorTemplate_"+a.id, a.id, data);

            }
        }        
    }
}
</script>
<!-- aqui -->

<script>
    function showConfirmRemove(activity_id) {
        showModal("Deseja remover a atividade? ", "Deseja remover a atividade da sessão? Ela continuará existindo, mas não estará mais nesta sessão. A atividade poderá ser adicionada novamente.", function() {
            var xhr = new XMLHttpRequest();


            var rem = document.getElementById(activity_id);


            xhr.onreadystatechange = function() {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText; // JSON.parse(this.responseText);
                    console.log(data);
                    if (data == "ERROR") {
                        return;
                    }
                    var newPos = JSON.parse(data, true);
                    console.log(newPos);

                    rem.parentNode.removeChild(rem);
                    for (var k in newPos) {
                        document.getElementById(k).setAttribute('data-activity-position', newPos[k]);
                    }
                }
                closeModal();


            };


            xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=removeActivityFromSessionProgram', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

            xhr.send("sessionId=" + sessionProgram_id + "&removeId=" + rem.id);

        }, true);
    }
</script>

<script>
    function selectReinforcement(query = "", input_id, activity_id, badReinf = false) {
        console.log("sel reinf : " + input_id);
        if (!isModalVisible()) {
            showModal("Selecionar Reforço", "", null, false);
        }

        var xhr = new XMLHttpRequest();
        console.log("select reinforcer...");
        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var ret = this.responseText; // JSON.parse(this.responseText);

                if (ret == "ERROR") {
                    return;
                }

                if (ret.length <= 0) {

                    return;
                }

                var obj = JSON.parse(this.responseText, true);
                console.log(obj);

                var i;
                var container = document.getElementById('activityContainer');
                if (container == null)
                    container = document.getElementById('activityContainerTemplate').cloneNode(true);

                var searchInput = container.querySelector('#search');
                searchInput.onkeyup = function(e) {
                    var keynum;
                    if (window.event) { // IE                    
                        keynum = e.keyCode;
                    } else if (e.which) { // Netscape/Firefox/Opera                   
                        keynum = e.which;
                    }
                    if (keynum == 13) {
                        var container = document.getElementById('activityContainerContent');
                        var query = document.getElementById('search').value;
                        var group = container.getAttribute('data-group');
                        container.innerHTML = "";
                        selectReinforcement(query, input_id, activity_id, badReinf);
                    }
                };

                var searchButton = container.querySelector('#searchButton');
                searchButton.onclick = function() {
                    var container = document.getElementById('activityContainerContent');
                    var query = document.getElementById('search').value;
                    var group = container.getAttribute('data-group');
                    container.innerHTML = "";
                    selectReinforcement(query, input_id, activity_id, badReinf);

                };


                var showMoreButton = container.querySelector("#buttonShowMoreTemplate");
                if (showMoreButton == null)
                    showMoreButton = container.querySelector("#buttonShowMore");
                showMoreButton.id = "buttonShowMore";
                showMoreButton.disabled = false;

                showMoreButton.onclick = function() {
                    var container = document.getElementById('activityContainer');
                    var query = document.getElementById('search').value;
                    var group = container.getAttribute('data-group');

                    selectReinforcement(query, input_id, activity_id, badReinf);
                }
                if (obj.length <= 0) {

                    showMoreButton.disabled = true;
                }
                container.hidden = false;
                container.id = "activityContainer";



                var content = container.querySelector("#activityContainerContent");
                for (i = 0; i < obj.length; i++) {
                    var card =
                        '<h4 class="card-header">' + obj[i]['name'] + '</h4>' +
                        '<img class="card-img-top rounded  img-thumbnail" src="' + obj[i]['thumb'] + '">' +
                        '<div class="card-body">' +
                        '<div class="container-fluid">' +

                        '<div class="row">' +
                        '<div class="col-6"><button data-act-name="' + obj[i]['name'] + '" data-actId="' + obj['id'] + '" type="button"  id="selButton" class="btn btn-block btn-primary">Selecionar</button></div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    var card_div = document.createElement('div');
                    card_div.classList.add('card', 'bg-light');
                    card_div.id = obj[i]['id'];


                    card_div.innerHTML = card;

                    var selectButton = card_div.querySelector('#selButton');
                    selectButton.setAttribute('data-actId', obj[i]['id']);
                    selectButton.onclick = function() {
                        var id = this.getAttribute('data-actId');
                        var name = this.getAttribute('data-act-name');

                        setRewardValue(input_id, activity_id, id, name, badReinf);
                        closeModal();

                    };
                    content.appendChild(card_div);
                }
            }
            swapModalContent(container);
        };

        document.getElementById('search').value = query;

        var d = document.getElementById('activityContainerContent');
        console.log(d);
        var offset = 0;
        if (d != null)
            offset = countSons(d);

        console.log("Offset: " + offset);
        xhr.open("GET", '<?php echo BASE_URL; ?>/activity/index.php?action=getReinforcers_json&offset=' + offset + "&query=" + query, true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send("");
    }

    function setRewardValue(input_id, activity_id, reinforcer_id, name, badReinf = false) {

        if (badReinf) {
            console.log("input id: " + input_id);
            document.getElementById(input_id).setAttribute('data-reinforcer-id', reinforcer_id);
            document.getElementById(input_id).value = reinforcer_id;
            updateActivityConfig(activity_id);
        } else {

            console.log("input id: " + input_id);
            document.getElementById(input_id).setAttribute('data-reinforcer-id', reinforcer_id);
            document.getElementById(input_id).value = name;
            updateActivityConfig(activity_id);
        }
    }

    function updateAFollowActivityOrder(who) {
        if (who.id == "followOrder_nobaseline") {
            var followOrder_nobaseline_check = document.getElementById('followOrder_nobaseline').checked;
            if (followOrder_nobaseline_check) {
                document.getElementById("followOrder").checked = false;
            }
        } else if (who.id == "followOrder") {
            var check = document.getElementById("followOrder").checked;
            if (check) {
                document.getElementById("followOrder_nobaseline").checked = false;
            }

        }





        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                console.log(this.responseText);
            }


        };
        xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=setSessionFollowActivityOrder', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

        var order = "0";
        var check = document.getElementById("followOrder").checked;
        var followOrder_nobaseline_check = document.getElementById('followOrder_nobaseline').checked;
        if (check)
            order = "1";
        else if (followOrder_nobaseline_check)
            order = "2";

        xhr.send("&session_program_id=" + sessionProgram_id + "&order=" + order);




    }

    function updateGlobalConfig(who) {
        if (who.id == "followOrder_nobaseline") {
            var followOrder_nobaseline_check = document.getElementById('followOrder_nobaseline').checked;
            if (followOrder_nobaseline_check) {
                document.getElementById("followOrder").checked = false;
            }
        } else if (who.id == "followOrder") {
            var check = document.getElementById("followOrder").checked;
            if (check) {
                document.getElementById("followOrder_nobaseline").checked = false;
            }
        }





        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                console.log(this.responseText);
            }


        };
        xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=setSessionFollowActivityOrder', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

        var order = "0";
        var check = document.getElementById("followOrder").checked;
        var followOrder_nobaseline_check = document.getElementById('followOrder_nobaseline').checked;
        if (check)
            order = "1";
        else if (followOrder_nobaseline_check)
            order = "2";
        xhr.send("&session_program_id=" + sessionProgram_id + "&order=" + order);

    }
</script>




<div class="row mt-3">
    <div class="col-3">
        <div class="card text-white bg-secondary ">
            <div class="card-header font-weight-bold text-uppercase">
                <div class="form-group">
                    <label for="sessionName">Nome do Programa:</label>
                    <input type="text" class="form-control" id="sessionName" onfocusout="updateSessionName()">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="followOrder" onchange="updateAFollowActivityOrder(this)">
                    <label class="form-check-label" for="followOrder" data-toggle="tootip" title="Ignora as configurações de 'próxima atividade' em caso de acerto ou erro, executa conforme as atividades estão ordenadas e não apresenta reforços ou dicas">Linha de Base</label>

                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="followOrder_nobaseline" onchange="updateAFollowActivityOrder(this)">
                    <label class="form-check-label" for="followOrder_nobaseline" data-toggle="tootip" title="Ignora as configurações de 'próxima atividade' em caso de acerto ou erro, executa conforme as atividades estão ordenadas e apresenta reforços ou dicas">Seguir a ordem das atividades</label>

                </div>
                <div class="form-check d-none ">
                    <input type="checkbox" class="form-check-input" id="useGlobalConfig" onchange="">
                    <label class="form-check-label" for="useGlobalConfig" data-toggle="tootip" title="Usa as mesmas  recompensas para todo o programa">Seguir ordem e apresentar recompensas (<b>Ainda não implementado</b>)</label>

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
                <div class="col"><img class="img-fluid rounded" src="<?php echo BASE_URL; ?>/data/student/<?php echo $fetch['id']; ?>/<?php echo $fetch['avatar']; ?>"></div>
                <h4 class="card-header border-dark"><?php echo $fetch['name']; ?></h4>
                <div class="card-body">



                    <p class="card-text">Nascimento: <?php $date = date_create($fetch['birthday']);
                                                        echo date_format($date, "d/m/Y"); ?></p>
                    <p class="card-text">Endereço: <?php echo $fetch['city'];
                                                    echo " - " . $fetch['state'];
                                                    ?></p>
                    <p class="card-text">Medicação: <?php echo $fetch['medication']; ?></p>


                    <a type="button" class="btn btn-lg btn-block btn-success" href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=runSessionProgram&athena=false&session_id=<?php echo $data['sessionId']; ?>&student_id=<?php echo $data['student_id']; ?>"> Iniciar Programa </a>

                </div>
            </div>
        </div>



    </div>

    <div class="col-9" id="sessionProgram">


        <div class='row' id="theContent" class="mt-3">
            <div class='col' id="activities">

                <div id="loading"> <img src="<?php echo BASE_URL; ?>/ui/load.gif" class="mx-auto d-block" alt="Responsive image"></div>
            </div>
        </div>

        <div class='row' class="mt-3 mb-3 pb-3">
            <div class='col'>
                <button type="button" id="addActivityButton" class='btn btn-lg btn-block btn-outline-success' onclick="openSelectActivity()"> Adicionar atividade do repositório</button>
            </div>
        </div>



    </div>
    <div id='help' style="position: absolute; top:5px; right: 30px;">
        <button class='btn btn-block btn-lg btn-warning' onclick="showHelp()"><i class="fas fa-question"></i></button>

    </div>
</div>


<div id='activityContainerTemplate' hidden>
    <div class="row mt-4">
        <div class="col-12">

            <div class="form-row">
                <div class="form-group col-lg-11">
                    <input class="form-control mr-sm-2" id="search" name="query" type="query" placeholder="Filtrar " aria-label="Search" value="">
                </div>
                <div class="form-group col-lg-1">
                    <button type="button" id='searchButton' class="btn btn-outline-success form-control">
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
            <button id="buttonShowMoreTemplate" type="button" class="btn btn-block btn-primary">
                Mostrar mais
            </button>
        </div>
    </div>
</div>



<div id="activityConfigTemplate" class="row collapse" hidden>
    <div class="col">
        <div class="container alert alert-info">
            <div class="form-group row">
                <label for="staticEmail" class="col-sm-6 col-form-label">Consequência de acerto</label>
                <div class="col-sm-6">
                    <select id="reinforcerTypeTemplate" class="form-control" onchange="reinforcerChange(this)">
                        <option value="none" selected>Nenhum</option>
                        <option value="auto_reinforce" selected>Avaliação de Preferência</option>
                        <option value="showReinforcer"> Recompensa</option>
                    </select>
                </div>
            </div>

            <div class="form-group row d-none" id="selectReinforcerTemplate">
                <div class="col">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <button id="selReinforcerButtonTemplate" class="form-control btn btn-info" type="button">Selecionar Recompensa</button>
                        </div>
                        <div class="input-group-prepend">
                            <button type="button" class="btn  btn-danger" id="noReinforcerTemplate">Nenhuma</button>
                        </div>

                        <input id="reinforcerInptTemplate" class="form-control" type="text" readonly placeholder="Selecione">

                    </div>
                </div>

            </div>

        </div>







        <div class="container alert alert-warning">
            <div class="form-group row ">
                <label for="staticEmail" class="col-sm-6 col-form-label">Consequência de erro</label>
                <div class="col-sm-6">
                    <select id="correctionTypeTemplate" class="form-control" onchange="correctionChange(this)">
                        <option value='none' selected>Nenhum</option>
                        <option value='repeat'>Repetir</option>
                        <option value='tip'>Dica</option>
                        <option value='bad_reinforcer'>Apresentar "reforço negativo -- NÃO UTILIZAR AINDA"</option>
                    </select>
                </div>
            </div>
            <div class="form-group row d-none" id="tipTemplate">
                <div class="col-6">
                    <label for="staticEmail" class="col-sm-6 col-form-label">Selecione a dica</label>
                </div>
                <div class="col-6">
                    <select id="tip_valueTemplate" class="form-control">
                        <option value='none' selected>Nenhuma</option>
                        <option value='blink'>Piscar a borda do estímulo</option>
                        <option value='shrink'>Diminuir o tamanho</option>
                        <option value='enlarge'>Aumentar o tamanho</option>
                        <option value='delay'>Demorar para apresentar (delay)</option>
                    </select>
                </div>

            </div>

            <div class="form-group row d-none" id="repeatTemplate">
                <div class="col-6">
                    <label for="staticEmail" class="col-sm-6 col-form-label">Número de Repetições</label>
                </div>
                <div class="col-6">
                    <input class="form-control" type="number" value="1" id="repeatNumberTemplate">
                </div>
            </div>

            <div class="form-group row d-none" id="selectBadReinforcerTemplate">
                <div class="col">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <button id="selBadReinforcerButtonTemplate" class="form-control btn btn-info" type="button">Selecionar Recompensa</button>
                        </div>
                        <div class="input-group-prepend">
                            <button type="button" class="btn  btn-danger" id="noBadReinforcerTemplate">Nenhuma</button>
                        </div>

                        <input id="badReinforcerInptTemplate" class="form-control" type="text" readonly placeholder="Selecione">

                    </div>
                </div>

            </div>


            <div class=" d-none" id="afterCorrectionTemplate">

                <button class="btn  btn-warning" onclick="showTipForTip()"><i class="fas fa-question"></i></button>

                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-3 col-form-label">Próxima atividade (<b><u>acerto</u> após correção</b>)</label>
                    <div class="col">
                        <div class=" input-group mb-2">
                            <div class="input-group-prepend">
                                <button type="button" class="btn  btn-info" id="nextafterCorrectionButtonTemplate">Selecionar</button>
                            </div>
                            <div class="input-group-prepend">
                                <button type="button" class="btn btn-danger" id="nexteAfterCorrectionNoneTemplate">Nenhuma</button>
                            </div>
                            <input class="form-control" type='text' id="nextActivityOnCorrectionTemplate" disabled value="nenhuma">
                        </div>
                    </div>
                </div>

                <div class="form-group row ">
                    <label id="nextAfterCorrectionWrongTemplateLabel" for="staticEmail" class="col-sm-3 col-form-label">Próxima atividade <b style="color:red">em caso de erro</b> (<b>erro após apresentação de dica</b>)</label>
                    <div class="col">
                        <div class=" input-group mb-2">
                            <div class="input-group-prepend">
                                <button type="button" class="btn  btn-info" id="nextafterCorrectionErrorButtonTemplate">Selecionar</button>
                            </div>
                            <div class="input-group-prepend">
                                <button type="button" class="btn btn-danger" id="nexteAfterCorrectionErrorNoneTemplate">Nenhuma</button>
                            </div>
                            <input class="form-control" type='text' id="nextActivityOnCorrectionErrorTemplate" disabled value="nenhuma">
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="form-group row alert alert-success">
            <label for="staticEmail" class="col-sm-3 col-form-label">Próxima atividade (acerto)</label>
            <div class="col">
                <div class=" input-group mb-2">
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-block btn-info" id="nextOnCorrectButtonTemplate">Selecionar</button>
                    </div>
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-block btn-danger" id="noneOnCorrectTemplate">Nenhuma</button>
                    </div>
                    <input class="form-control" type='text' id="nextActivityOnCorrectTemplate" disabled value="nenhuma">
                </div>
            </div>
        </div>



        <div class="form-group row alert alert-danger" id="nextOnErrorDiv_template">
            <label for="staticEmail" class="col-sm-3 col-form-label">Próxima atividade (erro)</label>

            <div class="col input-group mb-2">
                <div class="input-group-prepend">
                    <button type="button" class="btn btn-block btn-info" id="nextOnWrongButtonTemplate">Selecionar</button>
                </div>
                <div class="input-group-prepend">
                    <button type="button" class="btn btn-block btn-danger" id="noneOnWrongTemplate">Nenhuma</button>
                </div>
                <input class="form-control" type='text' id="nextActivityOnErrorTemplate" disabled value="nenhuma">
            </div>
        </div>

    </div>
</div>
<img hidden src="<?php echo BASE_URL; ?>/ui/load.gif" id="loadGIF">

<script>
    function cons_correct_change() {

    }
</script>



<script>
    function addActivityToHTML(activity_object) {
        var row = document.createElement('div');
        row.classList.add("row", 'mb-3');
        row.id = activity_object['id'];
        row.setAttribute('data-activity-id', activity_object['id']);
        row.setAttribute('data-activity-name', activity_object['name'] + ("(") + activity_object['position'] + ")");
        row.setAttribute('data-activity-thumbnail', activity_object['thumbnail']);
        row.setAttribute('data-activity-position', activity_object['position']);
        row.setAttribute('data-type', 'activity');

        var nameCol = document.createElement('div');
        nameCol.classList.add('col-8');
        nameCol.innerHTML = "<h1>" + activity_object['name'] + ("(") + activity_object['position'] + ")" + "</h1>";

        var configContent = document.getElementById('activityConfigTemplate').cloneNode(true);
        configContent.setAttribute('data-object-id', activity_object['id']);
        configContent.id = "conf_" + activity_object['id'];
        configContent.hidden = false;

        ////config reinforcer....
        var select_for_reinforcer = configContent.querySelector("#reinforcerTypeTemplate");
        select_for_reinforcer.id = "sel_" + activity_object['id'];
        select_for_reinforcer.setAttribute('data-object-id', activity_object['id']);


        if ('spa_reinforcer_type' in activity_object) {

            select_for_reinforcer.value = activity_object['spa_reinforcer_type'];
        }

        var selReinforcer = configContent.querySelector("#selectReinforcerTemplate");
        selReinforcer.id = "selReinforcer_" + activity_object['id'];
        var reinforcerInput = configContent.querySelector("#reinforcerInptTemplate");
        reinforcerInput.id = "reinfInput_" + activity_object['id'];

        if (select_for_reinforcer.value != 'none') {
            reinforcerInput.setAttribute("data-reinforcer-id", activity_object['spa_reinforcer_value']);
            console.log(activity_object['reinforcer_name']);
            reinforcerInput.value = activity_object['reinforcer_name'];
            //reinforcerInput.value = activity_object['spa_reinforcer_value'];
            selReinforcer.classList.remove('d-none');
        }
        var noReinforcer = configContent.querySelector("#noReinforcerTemplate");
        noReinforcer.setAttribute('data-input-id', reinforcerInput.id);
        noReinforcer.setAttribute('data-activity-id', activity_object['id']);
        noReinforcer.id = "noReinfButton_" + activity_object['id'];
        noReinforcer.onclick = function() {
            document.getElementById(this.getAttribute('data-input-id')).value = "";
            updateActivityConfig(this.getAttribute('data-activity-id'));

        };

        var selReinforcerButton = configContent.querySelector("#selReinforcerButtonTemplate");
        selReinforcerButton.id = "selReinfButton_" + activity_object['id'];
        selReinforcerButton.setAttribute('data-input-id', reinforcerInput.id);
        selReinforcerButton.setAttribute('data-activity-id', activity_object['id']);
        selReinforcerButton.onclick = function() {
            selectReinforcement("", this.getAttribute('data-input-id'), this.getAttribute('data-activity-id'));
        };


        ////config reinforcer....

        ////config tip....
        var corr_sel = configContent.querySelector("#correctionTypeTemplate");
        corr_sel.id = "corr_sel_" + activity_object['id'];
        corr_sel.setAttribute('data-object-id', activity_object['id']);

        if ('spa_correction_type' in activity_object) {

            corr_sel.value = activity_object['spa_correction_type'];

        }

        var nextOnWrongDiv = configContent.querySelector("#nextOnErrorDiv_template");

        nextOnWrongDiv.id = "nextOnErrorDiv_" + activity_object['id'];

        var tip_sel = configContent.querySelector("#tipTemplate");
        tip_sel.id = "tip_" + activity_object['id'];

        var tip_value = configContent.querySelector("#tip_valueTemplate");
        tip_value.id = "tip_Value_" + activity_object['id'];
        tip_value.onchange = function() {
            updateActivityConfig(activity_object['id']);

        };
        ////config tip....


        ///config repeat...

        var repeat_sel = configContent.querySelector("#repeatTemplate");
        repeat_sel.id = "repeat_val_" + activity_object['id'];


        var repeat_number = configContent.querySelector("#repeatNumberTemplate");
        repeat_number.id = "repeat_number" + activity_object['id'];
        repeat_number.onchange = function() {
            updateActivityConfig(activity_object['id']);
        }


        //after correction div.
        var afterCorrection = configContent.querySelector("#afterCorrectionTemplate");
        afterCorrection.id = "afterCorrection_" + activity_object['id'];
        afterCorrection.setAttribute('data-object-id', activity_object['id']);



        var afterCorrectionNoneButton = afterCorrection.querySelector("#nexteAfterCorrectionNoneTemplate");
        afterCorrectionNoneButton.id = "afterCorrectionNone_" + activity_object['id'];
        afterCorrectionNoneButton.setAttribute('data-object-id', activity_object['id']);
        afterCorrectionNoneButton.onclick = function() {
            var id = "nextActivityAfterCorrection_" + this.getAttribute('data-object-id');
            document.getElementById(id).value = "";
            document.getElementById(id).setAttribute('data-activity-next', "");

            updateActivityConfig(this.getAttribute('data-object-id'));
        };

        //label
        var nextActivityOnCorrection = configContent.querySelector("#nextActivityOnCorrectionTemplate");
        nextActivityOnCorrection.id = "nextActivityAfterCorrection_" + activity_object['id'];
        nextActivityOnCorrection.value = activity_object['spa_next_after_correction'];
        nextActivityOnCorrection.setAttribute('data-activity-next', activity_object['spa_next_after_correction_id']);
        console.log(activity_object);

        var nextafterCorrectionButton = configContent.querySelector("#nextafterCorrectionButtonTemplate");
        nextafterCorrectionButton.id = "nextafterCorrectionButton_" + activity_object['id'];

        nextafterCorrectionButton.setAttribute('data-inputId', nextActivityOnCorrection.id);
        nextafterCorrectionButton.setAttribute('data-spActivityId', activity_object['id']);
        nextafterCorrectionButton.setAttribute('data-activityName', activity_object['name']);
        nextafterCorrectionButton.onclick = function() {
            //
            var id = this.getAttribute('data-object-id');
            selectLocalActivity(this);
        }

        /*==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=*/
        //BAD REINFORCER

        var selBadReinforcer = configContent.querySelector("#selectBadReinforcerTemplate");
        selBadReinforcer.id = "selBadReinforcer_" + activity_object['id'];
        var badReinforcerInput = configContent.querySelector("#badReinforcerInptTemplate");
        badReinforcerInput.id = "badReinfInput_" + activity_object['id'];

        console.log("################corr.sel.value = " + corr_sel.value);
        console.log("#correction value: " + activity_object['spa_correction_value']);
        if (corr_sel.value != 'none') {
            badReinforcerInput.setAttribute("data-reinforcer-id", activity_object['spa_reinforcer_value']);

            badReinforcerInput.value = activity_object['spa_correction_value'];

            selBadReinforcer.classList.remove('d-none');
        }
        var noReinforcer = configContent.querySelector("#noBadReinforcerTemplate");
        noReinforcer.id = "noReinfButton_" + activity_object['id'];
        noReinforcer.setAttribute('data-input-id', badReinforcerInput.id);
        noReinforcer.setAttribute('data-activity-id', activity_object['id']);

        noReinforcer.onclick = function() {
            document.getElementById(this.getAttribute('data-input-id')).value = "";
            updateActivityConfig(this.getAttribute('data-activity-id'));

        };

        var selBadReinforcerButton = configContent.querySelector("#selBadReinforcerButtonTemplate");
        selBadReinforcerButton.id = "selReinfButton_" + activity_object['id'];
        selBadReinforcerButton.setAttribute('data-input-id', badReinforcerInput.id);
        selBadReinforcerButton.setAttribute('data-activity-id', activity_object['id']);
        selBadReinforcerButton.onclick = function() {
            selectReinforcement("", this.getAttribute('data-input-id'), this.getAttribute('data-activity-id'), true);
        };

        /*==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=*/



        //------------------------------------------------------------------------------------------------
        //input
        var nextActivityOnCorrectionErrorTemplate = configContent.querySelector("#nextActivityOnCorrectionErrorTemplate");
        nextActivityOnCorrectionErrorTemplate.id = "nextActivityOnCorrectionErrorTemplate_" + activity_object['id'];
        nextActivityOnCorrectionErrorTemplate.value = activity_object['spa_next_after_correction_wrong'];
        nextActivityOnCorrectionErrorTemplate.setAttribute('data-activity-next', activity_object['spa_next_after_correction_wrong_id']);
        console.log(nextActivityOnCorrectionErrorTemplate);


        var nexteAfterCorrectionErrorNoneTemplate = afterCorrection.querySelector("#nexteAfterCorrectionErrorNoneTemplate");
        nexteAfterCorrectionErrorNoneTemplate.id = "nexteAfterCorrectionErrorNoneTemplate_" + activity_object['id'];
        nexteAfterCorrectionErrorNoneTemplate.setAttribute('data-object-id', activity_object['id']);
        nexteAfterCorrectionErrorNoneTemplate.onclick = function() {
            var id = "nextActivityOnCorrectionErrorTemplate_" + this.getAttribute('data-object-id');
            document.getElementById(id).value = "";
            document.getElementById(id).setAttribute('data-activity-next', "");

            updateActivityConfig(this.getAttribute('data-object-id'));
        };

        var nextAfterCorrectionWrongTemplateLabel = configContent.querySelector("#nextAfterCorrectionWrongTemplateLabel");
        nextAfterCorrectionWrongTemplateLabel.id = "nextAfterCorrectionWrongTemplateLabel_" + activity_object['id'];


        var nextafterCorrectionErrorButtonTemplate = configContent.querySelector("#nextafterCorrectionErrorButtonTemplate");
        nextafterCorrectionErrorButtonTemplate.id = "nextafterCorrectionErrorButtonTemplate_" + activity_object['id'];

        nextafterCorrectionErrorButtonTemplate.setAttribute('data-inputId', nextActivityOnCorrectionErrorTemplate.id);
        nextafterCorrectionErrorButtonTemplate.setAttribute('data-spActivityId', activity_object['id']);
        nextafterCorrectionErrorButtonTemplate.setAttribute('data-activityName', activity_object['name']);
        nextafterCorrectionErrorButtonTemplate.onclick = function() {
            //
            var id = this.getAttribute('data-object-id');
            selectLocalActivity(this);
        }

        //------------------------------------------------------------------------------------------------




        if (corr_sel.value != "none") {
            nextOnWrongDiv.classList.add('d-none');
            if (corr_sel.value == "tip") {
                tip_value.value = activity_object['spa_correction_value'];
            } else if (corr_sel.value == "repeat") {
                repeat_number.value = activity_object['spa_correction_value'];
            }
            afterCorrection.classList.remove('d-none');
        }
        //

        var nextOnCorrect = configContent.querySelector("#nextActivityOnCorrectTemplate");
        nextOnCorrect.id = "nextActivityOnCorrect_" + activity_object['id'];
        if ('spa_next_on_correct' in activity_object) {
            nextOnCorrect.value = activity_object['spa_next_on_correct'];
            console.log("____OBJ");
            console.log(activity_object);

            nextOnCorrect.setAttribute('data-activity-next', activity_object['spa_next_on_correct_id']);

        }


        var nextOnWrong = configContent.querySelector("#nextActivityOnErrorTemplate");
        nextOnWrong.id = "nextActivityOnError_" + activity_object['id'];

        if ('spa_next_on_wrong' in activity_object) {
            nextOnWrong.value = activity_object['spa_next_on_wrong'];
            nextOnWrong.setAttribute('data-activity-next', activity_object['spa_next_on_wrong_id']);
            nextOnWrong.id = 'nextActivityOnError_' + activity_object['id'];
        }


        var noneOnCorrectButton = configContent.querySelector("#noneOnCorrectTemplate");
        noneOnCorrectButton.setAttribute('data-input-id', nextOnCorrect.id);
        noneOnCorrectButton.setAttribute('data-activity-id', activity_object['id']);
        noneOnCorrectButton.onclick = function() {
            document.getElementById(this.getAttribute('data-input-id')).value = '';
            updateActivityConfig(this.getAttribute('data-activity-id'));
        };

        var noneOnWrongButton = configContent.querySelector("#noneOnWrongTemplate");
        noneOnWrongButton.setAttribute('data-input-id', nextOnWrong.id);
        noneOnWrongButton.setAttribute('data-activity-id', activity_object['id']);
        noneOnWrongButton.onclick = function() {
            document.getElementById(this.getAttribute('data-input-id')).value = '';
            updateActivityConfig(this.getAttribute('data-activity-id'));
        };

        var buttonSelectOnCorrect = configContent.querySelector("#nextOnCorrectButtonTemplate");
        buttonSelectOnCorrect.id = "buttonNextOnCorrect_" + activity_object['id'];
        buttonSelectOnCorrect.setAttribute('data-inputId', nextOnCorrect.id);
        buttonSelectOnCorrect.setAttribute('data-spActivityId', activity_object['id']);
        buttonSelectOnCorrect.setAttribute('data-activityName', activity_object['name']);

        buttonSelectOnCorrect.onclick = function() {
            selectLocalActivity(this); //nextOnCorrect.id, activity_object['id'],activity_object['name']);

        };

        var buttonSelectOnWrong = configContent.querySelector("#nextOnWrongButtonTemplate");
        buttonSelectOnWrong.id = "buttonNextOnWrong_" + activity_object['id'];
        buttonSelectOnWrong.setAttribute('data-inputId', nextOnWrong.id);
        buttonSelectOnWrong.setAttribute('data-spActivityId', activity_object['id']);
        buttonSelectOnWrong.setAttribute('data-activityName', activity_object['name']);
        buttonSelectOnWrong.onclick = function() {
            selectLocalActivity(this); //nextOnWrong.id, activity_object['id'],activity_object['name']);
        };


        var config = document.createElement('div');
        config.classList.add('col-1');
        config.id = "show" + activity_object['id'];
        config.setAttribute('data-target', "#conf_" + activity_object['id']); // dataTarget =;
        config.setAttribute('data-toggle', "collapse");
        config.ariaControls = "conf_" + activity_object['id'];
        config.innerHTML = '<button class="btn btn-block btn-lg btn-info"><i class="fas fa-bars"></i></button>';

        //config.setAttribute('data-placement',"right");
        config.title = "Configurar...";



        var up = document.createElement('div');
        up.classList.add('col-1');
        up.onclick = function() {
            moveACtivityUp(this.getAttribute('data-activity-id'));
        };
        up.innerHTML = '<button class="btn btn-block btn-lg btn-info"><i class="fas fa-arrow-circle-up"></i></button>';
        up.setAttribute('data-toggle', "tooltip");
        up.setAttribute('data-placement', "right");
        up.setAttribute("data-activity-id", activity_object['id']);
        up.title = "Mover atividade para cima";


        var down = document.createElement('div');
        down.classList.add('col-1');
        down.onclick = function() {
            moveACtivityDown(this.getAttribute('data-activity-id'));
        }
        down.setAttribute('data-toggle', "tooltip");
        down.setAttribute('data-placement', "right");
        down.setAttribute('data-activity-id', activity_object['id']);
        down.title = "Mover atividade para baixo";
        down.innerHTML = '<button class="btn btn-block btn-lg btn-info"><i class="fas fa-arrow-circle-down"></i></button>';


        var remove = document.createElement('div');
        remove.classList.add('col-1');

        remove.innerHTML = '<button class="btn btn-block btn-lg btn-danger"><i class="fas fa-trash-alt"></i></button>';
        remove.setAttribute('data-toggle', "tooltip");
        remove.setAttribute('data-placement', "right");
        remove.setAttribute('data-activity-id', activity_object['id']);

        remove.onclick = function() {
            showConfirmRemove(this.getAttribute('data-activity-id'));
        };
        remove.title = "Remover atividade";


        row.appendChild(nameCol);
        row.appendChild(config);
        row.appendChild(up);
        row.appendChild(down);
        row.appendChild(remove);
        row.classList.add("alert", "alert-primary", "border");

        var container = document.getElementById('activities');
        console.log("append chilg");
        var a_r = document.createElement('div');
        a_r.classList.add("row");

        var a_c = document.createElement('div');
        a_c.classList.add("col", "alert", "alert-light", "border");


        a_c.appendChild(row);
        a_c.appendChild(configContent);

        a_r.appendChild(a_c);
        container.appendChild(a_r);
        a_r.id = activity_object['id'];
        a_r.setAttribute('data-activity-id', row.getAttribute('data-activity-id'));
        a_r.setAttribute('data-activity-name', row.getAttribute('data-activity-name'));
        a_r.setAttribute('data-activity-thumbnail', row.getAttribute('data-activity-thumbnail'));
        a_r.setAttribute('data-activity-position', row.getAttribute('data-activity-position'));

        //para mostrar a dica selecionada... No final, após inserir tudo no HTML 
        correctionChange(corr_sel, false);
    }

    function load_result() {
        document.getElementById('activities').classList.add('d-none');
        var img_copy = document.getElementById('loadGIF').cloneNode();
        img_copy.id = "LOADING_GIF";
        img_copy.hidden = false;
        document.getElementById('theContent').appendChild(img_copy);
    }

    function show_result() {
        var act = document.getElementById('activities');
        var cont = document.getElementById('theContent');
        var img_copy = document.getElementById('LOADING_GIF');
        cont.removeChild(img_copy);
        act.classList.remove('d-none');
    }

    function moveACtivityUp(id) {
        load_result();
        var node = document.getElementById(id);


        var prev = node.previousElementSibling;
        if (prev == null)
            return;


        if (prev != null) { // && prev.getAttribute('data-type') == 'activity') {

            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText;

                    if (data == "ERROR") {
                        return;
                    }
                    var newPos = JSON.parse(this.responseText, true);
                    var parent = node.parentNode;

                    parent.removeChild(node);
                    parent.insertBefore(node, prev);
                    var t = node.getAttribute('data-activity-position');

                    node.setAttribute('data-activity-position', newPos['activity1_position']);
                    prev.setAttribute('data-activity-position', newPos['activity2_position']);
                    show_result();
                }


            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=swapProgramActivityPosition', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");


            var activitiId1 = node.getAttribute('data-activity-id');
            var activityId2 = prev.getAttribute('data-activity-id');
            var position1 = node.getAttribute('data-activity-position');
            var position2 = prev.getAttribute('data-activity-position');
            xhr.send("&activity1=" + activitiId1 + "&activity2=" + activityId2 + "&position1=" + position1 + "&position2=" + position2);


        }
    }

    function moveACtivityDown(id) {
        load_result();
        var node = document.getElementById(id);


        var next = node.nextElementSibling;


        if (next != null) { // || next.getAttribute('data-type') == 'activity') {

            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var data = this.responseText;

                    if (data == "ERROR") {
                        return;
                    }
                    var newPos = JSON.parse(this.responseText, true);
                    console.log(newPos);
                    var parent = node.parentNode;

                    parent.removeChild(next);
                    parent.insertBefore(next, node);
                    var t = node.getAttribute('data-activity-position');

                    node.setAttribute('data-activity-position', newPos['activity1_position']);
                    next.setAttribute('data-activity-position', newPos['activity2_position']);
                    show_result();
                }
            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=swapProgramActivityPosition', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");


            var activitiId1 = node.getAttribute('data-activity-id');
            var activityId2 = next.getAttribute('data-activity-id');
            var position1 = node.getAttribute('data-activity-position');
            var position2 = next.getAttribute('data-activity-position');
            xhr.send("&activity1=" + activitiId1 + "&activity2=" + activityId2 + "&position1=" + position1 + "&position2=" + position2);
        } else {
            return;
        }
    }

    function updateActivityConfig(id) {
        //get everything of the activity and send using json.
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;
            if (this.status == 200) {
                console.log(this.responseText);
            }
        }
        var correction_type = document.getElementById('corr_sel_' + id).value;

        var correction_value = "";
        if (correction_type == "repeat") {
            correction_value = document.getElementById('repeat_number' + id).value;
        } else if (correction_type == "tip") {
            correction_value = document.getElementById('tip_Value_' + id).value;
        } else if (correction_type == "bad_reinforcer") {
            correction_value = document.getElementById('badReinfInput_' + id).value;
        } else {
            correction_value = "none";
        }

        var reinforcer_type = document.getElementById('sel_' + id).value;
        var reinforcer_value = document.getElementById('reinfInput_' + id).getAttribute('data-reinforcer-id');
        var next_on_correct = document.getElementById('nextActivityOnCorrect_' + id).value;
        var next_on_correct_id = document.getElementById('nextActivityOnCorrect_' + id).getAttribute('data-activity-next');
        var next_on_wrong = document.getElementById('nextActivityOnError_' + id).value;
        var next_on_wrong_id = document.getElementById('nextActivityOnError_' + id).getAttribute('data-activity-next');

        var next_after_correction = document.getElementById('nextActivityAfterCorrection_' + id).value;
        var next_after_correction_id = document.getElementById('nextActivityAfterCorrection_' + id).getAttribute('data-activity-next');
        var next_after_correction_wrong = document.getElementById('nextActivityOnCorrectionErrorTemplate_' + id).value;
        var next_after_correction_wrong_id = document.getElementById('nextActivityOnCorrectionErrorTemplate_' + id).getAttribute('data-activity-next');

        var position = 0; // document.getElementById('0').value;
        xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=updatSessionProgramActivityData', true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send('&id=' + id + '&correction_type=' + correction_type + "&correction_value=" + correction_value + '&reinforcer_type=' + reinforcer_type +
            "&reinforcer_value=" + reinforcer_value + "&next_on_correct=" + next_on_correct + "&next_on_wrong=" + next_on_wrong + "&position=" + position +
            "&next_on_wrong_id=" + next_on_wrong_id + "&next_on_correct_id=" + next_on_correct_id + "&next_after_correction=" + next_after_correction +
            "&next_after_correction_id=" + next_after_correction_id + "&next_after_correction_wrong=" + next_after_correction_wrong + "&next_after_correction_wrong_id=" + next_after_correction_wrong_id);

    }


    function reinforcerChange(obj) {
        var act_id = obj.getAttribute('data-object-id');
        opt = document.getElementById("selReinforcer_" + act_id);
        opt.classList.add('d-none');
        if (obj.value == 'showReinforcer') {
            var act_id = obj.getAttribute('data-object-id');
            console.log("change. activity id: " + act_id);
            var opt = document.getElementById("selReinforcer_" + act_id);
            opt.classList.remove('d-none');
        }
        updateActivityConfig(act_id);
    }

    function correctionChange(obj, update = true) {
        var act_id = obj.getAttribute('data-object-id');
        var opt = document.getElementById("tip_" + act_id);
        opt.classList.add('d-none');
        opt = document.getElementById("repeat_val_" + act_id);
        opt.classList.add('d-none');
        document.getElementById("afterCorrection_" + act_id).classList.add('d-none');

        var nextOnWrongDiv = document.getElementById("nextOnErrorDiv_" + act_id);
        nextOnWrongDiv.classList.add('d-none');

        if (obj.value == 'tip') {
            var act_id = obj.getAttribute('data-object-id');
            console.log("change. activity id: " + act_id);
            var opt = document.getElementById("tip_" + act_id);
            opt.classList.remove('d-none');
            document.getElementById("afterCorrection_" + act_id).classList.remove('d-none');

            document.getElementById("nextafterCorrectionErrorButtonTemplate_" + act_id).classList.remove('d-none');
            document.getElementById("nexteAfterCorrectionErrorNoneTemplate_" + act_id).classList.remove('d-none');
            document.getElementById("nextActivityOnCorrectionErrorTemplate_" + act_id).classList.remove('d-none');
            document.getElementById("nextAfterCorrectionWrongTemplateLabel_" + act_id).classList.remove('d-none');


        } else if (obj.value == "repeat") {
            console.log("repetir");

            var act_id = obj.getAttribute('data-object-id');
            var opt = document.getElementById("repeat_val_" + act_id);
            opt.classList.remove('d-none');
            document.getElementById("afterCorrection_" + act_id).classList.remove('d-none');

            document.getElementById("nextafterCorrectionErrorButtonTemplate_" + act_id).classList.add('d-none');
            document.getElementById("nexteAfterCorrectionErrorNoneTemplate_" + act_id).classList.add('d-none');
            document.getElementById("nextActivityOnCorrectionErrorTemplate_" + act_id).classList.add('d-none');
            document.getElementById("nextAfterCorrectionWrongTemplateLabel_" + act_id).classList.add('d-none');


        } else if (obj.value == "bad_reinforcer") {
            console.log("bad_reinforcer");

            var act_id = obj.getAttribute('data-object-id');
            var opt = document.getElementById("selBadReinforcer_" + act_id);
            opt.classList.remove('d-none');
            document.getElementById("afterCorrection_" + act_id).classList.remove('d-none');

            document.getElementById("nextafterCorrectionErrorButtonTemplate_" + act_id).classList.add('d-none');
            document.getElementById("nexteAfterCorrectionErrorNoneTemplate_" + act_id).classList.add('d-none');
            document.getElementById("nextActivityOnCorrectionErrorTemplate_" + act_id).classList.add('d-none');
            document.getElementById("nextAfterCorrectionWrongTemplateLabel_" + act_id).classList.add('d-none');


        } else {
            nextOnWrongDiv.classList.remove('d-none');
        }
        if (update)
            updateActivityConfig(act_id);
    }

    function setNextActivity(origin, input_id, activity_id, activityName) {
        console.log("ID: " + input_id);
        document.getElementById(input_id).value = activityName;
        document.getElementById(input_id).setAttribute('data-activity-next', activity_id);
        updateActivityConfig(origin);

        closeModal();
    }

    function countSons(element) {

        var activities = element.childNodes;
        var num = 0;
        for (var i = 0; i < activities.length; i++) {
            if (activities[i].nodeType == Node.ELEMENT_NODE) {
                num++;
            }
        }
        return num;
    }

    function selectLocalActivity(button) { //input_id, activityId,activityName){
        var input_id = button.getAttribute('data-inputId');
        var activityId = button.getAttribute('data-spActivityId');
        var activityName = button.getAttribute('data-activityName');
        console.log("inp id: " + input_id + " activity id: " + activityId);
        var act_cont = document.getElementById('activities');
        var activities = act_cont.childNodes;
        var container = document.createElement('div');
        container.classList.add('card-columns');
        container.innerHTML = "";

        console.log(activities);
        for (var i = 0; i < activities.length; i++) {
            if (activities[i].nodeType == Node.ELEMENT_NODE) {
                if (activities[i].id != activityId) {
                    var card =
                        '<div class="card border-dark m-3 " > ' +
                        '<img class="card-img-top" src="' + activities[i].getAttribute('data-activity-thumbnail') + '" alt="Card image cap">' +
                        '<h5 class="card-header">' + activities[i].getAttribute('data-activity-name') + '</h5>' +
                        '<div class="card-body">' +
                        '<a href="javascript:setNextActivity(\'' + activityId + '\',\'' + input_id + '\',\'' + activities[i].getAttribute('data-activity-id') + '\',\'' + activities[i].getAttribute('data-activity-name') + '\')" class="btn btn-outline-info btn-lg "> Selecionar </a>' +
                        '</div>' +
                        '</div>';
                    console.log(card);
                    container.innerHTML += card;
                }
            }
        }
        showModal("Selecione a próxima atividade", container);
    }

    function addActivity(activity_id) {
        console.log("add activity " + activity_id);
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {

                var data = JSON.parse(this.responseText, true);

                addActivityToHTML(data);
            }
        }

        var a_id = activity_id;
        var s_id = sessionProgram_id;
        var position = ""; //countSons(document.getElementById('activities'));
        xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=addActivityToSessionProgram', true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send('&json=true&return_data=true&activity_id=' + a_id + "&sessionProgram_id=" + s_id + "&position=" + position);

    }

    function openSelectActivity() {
        showModal("Selecionar atividade", "", null, false);
        selectActivity();
    }



    function selectActivity(query = "") {
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var data = this.responseText; // JSON.parse(this.responseText);

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
                searchInput.onkeyup = function(e) {
                    var keynum;
                    if (window.event) { // IE                    
                        keynum = e.keyCode;
                    } else if (e.which) { // Netscape/Firefox/Opera                   
                        keynum = e.which;
                    }
                    if (keynum == 13) {
                        var container = document.getElementById('activityContainerContent');
                        var query = document.getElementById('search').value;
                        container.innerHTML = "";
                        selectActivity(query);
                    }
                };

                var searchButton = container.querySelector('#searchButton');
                searchButton.onclick = function() {
                    var container = document.getElementById('activityContainerContent');
                    var query = document.getElementById('search').value;

                    container.innerHTML = "";

                    selectActivity(query);
                };
                var showMoreButton = container.querySelector("#buttonShowMoreTemplate");
                if (showMoreButton == null)
                    showMoreButton = container.querySelector("#buttonShowMore");
                showMoreButton.id = "buttonShowMore";
                showMoreButton.disabled = false;
                showMoreButton.onclick = function() {
                    var container = document.getElementById('activityContainer');
                    var query = document.getElementById('search').value;
                    selectActivity(query);
                }
                if (obj.length <= 0) {

                    showMoreButton.disabled = true;
                }
                container.hidden = false;
                container.id = "activityContainer";

                var content = container.querySelector("#activityContainerContent");
                for (i = 0; i < obj.length; i++) {
                    var card = '<div class="card bg-light" id="' + obj[i]['id'] + '">' +
                        '<img class="card-img-top rounded  img-thumbnail" src="' + obj[i]['thumb'] + '">' +
                        '<h4 class="card-header">' + obj[i]['name'] + '</h4>' +
                        '<div class="card-body">' +
                        '<span class="badge badge-secondary">' + DIFFICULTY[obj[i]['difficulty']] + '</span>  ' +
                        '<div class="container-fluid">' +
                        '<div class="row">' +
                        '<div class="col-6"><a href="javascript:addActivity(\'' + obj[i]['id'] + '\')" class="btn btn-block btn-primary">Selecionar</a></div>' +
                        //'<div class="col-6"><a href="index.php?action=edit&id=' + obj[i]['id'] + '" class="btn btn-block btn-primary">Visualizar</a></div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    content.innerHTML += card;

                }
                swapModalContent(container);
            }
        };

        document.getElementById('search').value = query;

        var d = document.getElementById('activityContainerContent');
        var offset = 0;
        if (d != null)
            offset = countSons(d);


        var url = '<?php echo BASE_URL; ?>/activity/index.php?action=getActivities_json&offset=' + offset + "&query=" + query;
        xhr.open("GET", '<?php echo BASE_URL; ?>/activity/index.php?action=getActivities_json&offset=' + offset + "&query=" + query, true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send("");
    }
</script>