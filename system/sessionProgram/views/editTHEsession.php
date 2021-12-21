<?php
$curr_id = $data['curriculum_id'];
require ROOTPATH . "/ui/modal.php";
$SQL = "SELECT * FROM curriculum WHERE id = '$curr_id'";
require_once ROOTPATH . '/utils/DBAccess.php';

$db = new DBAccess();
$res = $db->query($SQL);

if(!$res){
    die("ERRO. " . mysqli_error($db->con));
}

$res = mysqli_fetch_assoc($res);

$student_id = $res['student_id'];
$athena = $data['athena'];

?>


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


<div class="row mt-3">
    <div class="col-3">
        <div class="card text-white bg-secondary ">
            <?php
            $SQL = "SELECT *  FROM student WHERE id='$student_id'";
            $db = new DBAccess();
            $res = $db->query($SQL);
            if (!$res) {
                die("ERROR LOADING STUDENT $student_id. CONTACT ADMIN.");
            }
            $fetch = mysqli_fetch_assoc($res);
            ?>
            <div class="card text-white bg-danger border-dark" id="<?php echo $fetch['id']; ?>">
            <div class="col"><img class="img-fluid rounded bg-light" src="<?php echo BASE_URL;?>/data/student/<?php echo $fetch['id'];?>/<?php echo $fetch['avatar'];?>"></div>
                <h4 class="card-header border-dark"><?php echo $fetch['name']; ?></h4>
                <div class="card-body">
                    <p class="card-text">Nascimento: <?php $date = date_create($fetch['birthday']);
                                                        echo date_format($date, "d/m/Y"); ?></p>
                    <p class="card-text">Endereço: <?php echo $fetch['city'];
                                                    echo " - " . $fetch['state'];
                                                    ?></p>
                    <p class="card-text">Medicação: <?php echo $fetch['medication']; ?></p>
                </div>
                <?php if($athena == 'false'){?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col"><a href="<?php echo BASE_URL?>/sessionProgram/index.php?action=runCurriculum&studentId=<?php echo $fetch['id']; ?>" class="btn btn-block btn-success border border-dark">Iniciar Ensino</a></div>
                    </div>
                </div>
                <?php 
                } else if($athena == 'true'){ ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col"><a href="<?php echo BASE_URL?>/sessionProgram/index.php?action=runCurriculum&athena=true&studentId=<?php echo $fetch['id']; ?>" class="btn btn-block btn-success border border-dark">Visualizar Programa de Ensino</a></div>
                    </div>
                </div>
                <?php 
                } ?> 

            </div>
        </div>
    </div>
    
    <div class="col-9" id="sessionProgram">


        <div class='row' class="mt-3">
            <div class='col' id="activities">
             <img src="<?php echo BASE_URL;?>/ui/load.gif" class="mx-auto d-block" alt="Responsive image">
            </div>
        </div>

        <?php if($athena == 'false'){?>

        
        <div class='row' class="mt-3 mb-3 pb-3">
            <div class='col'>
                <button type="button" id="addActivityButton" class='btn btn-lg btn-block btn-outline-success' onclick="openSelectProgram()"> Adicionar Programação</button>
            </div>
        </div>
        <?php }?>



    </div>
</div>

<div class="row d-none alert alert-light border" id="sessionHTMLTemplate">
        <div class="col-8" id="sessionHTMLName">
            
        </div>
        <div class="col-1" >
            <button class="btn btn-block btn-lg btn-info"id="sessionHTMLOption"> <i class="fas fa-bars"></i></button>
        </div>
        <?php if($athena == 'false'){?>

        <div class="col-1" >
            <button class="btn btn-block btn-lg btn-info"id="sessionHTMLup"><i class="fas fa-arrow-circle-up"></i></button>
        </div>
        
        
        <div class="col-1" >
            <button class="btn btn-block btn-lg btn-info" id="sessionHTMLdown"><i class="fas fa-arrow-circle-down"></i></button>
        </div>
        <div class="col-1" >
            <button class="btn btn-block btn-lg btn-danger" id="sessionHTMLremove"><i class="fas fa-trash-alt"></i></button>
        </div>
        <?php }?>
        <?php if($athena == 'true'){?>

            <div class="col-1" >
                <button disabled class="btn disabled btn-block btn-lg btn-info"id="sessionHTMLup"><i class="fas fa-arrow-circle-up"></i></button>
            </div>


            <div class="col-1" >
                <button disabled class="btn disabled  btn-block btn-lg btn-info" id="sessionHTMLdown"><i class="fas fa-arrow-circle-down"></i></button>
            </div>
            <div class="col-1" >
                <button disabled class="btn disabled btn-block btn-lg btn-danger" id="sessionHTMLremove"><i class="fas fa-trash-alt"></i></button>
            </div>
            <?php }?>
        
</div>

<script>
    document.body.onload = function(){
        var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState != 4)
            return;

        if (this.status == 200) {
            console.log(this.responseText);
            document.getElementById('activities').innerHTML = "";
            var objs = JSON.parse(this.responseText, true);
            for (var i = 0; i < objs.length; i++){
                addSessionToHTML(objs[i]);
            }
        }
    };
    var curriculum_id = "<?php echo $curr_id;?>";
    xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=loadCurriculum', true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
    xhr.send("curriculum_id="+curriculum_id);


    }
    var studentId = "<?php echo $student_id;?>";
    function openSelectProgram(){
        showModal("Selecionar atividade", "", null, false);
        selectProgram();
    }

    function selectProgram(query = "") {
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
                        selectProgram(query);
                    }
                };

                var searchButton = container.querySelector('#searchButton');
                searchButton.onclick = function() {
                    var container = document.getElementById('activityContainerContent');
                    var query = document.getElementById('search').value;

                    container.innerHTML = "";

                    selectProgram(query);
                };
                var showMoreButton = container.querySelector("#buttonShowMoreTemplate");
                if (showMoreButton == null)
                    showMoreButton = container.querySelector("#buttonShowMore");
                showMoreButton.id = "buttonShowMore";
                showMoreButton.disabled = false;
                showMoreButton.onclick = function() {
                    var container = document.getElementById('activityContainer');
                    var query = document.getElementById('search').value;
                    selectProgram(query);
                }
                if (obj.length <= 0) {

                    showMoreButton.disabled = true;
                }
                container.hidden = false;
                container.id = "activityContainer";

                var content = container.querySelector("#activityContainerContent");
                for(var i =0; i < obj.length; i++){

                var date = obj[i]['date'];

                var c = '<div class="card">'+

                '<div class="card-body">'+
                '<h5 class="card-title">'+ obj[i]['name'] + '</h5>'+
                '<p class="card-text">' + "Data de criação: " + date +  '</p>' +
                '<button type="button" class="btn btn-lg btn-block btn-danger" onclick="addSession(\'' + obj[i]['id'] +'\')"> Adicionar à sessão de ensino  </button>'+
                '</div>'+
                '</div>';
                content.innerHTML += c;
                }
                swapModalContent(container);
            }
        };



        document.getElementById('search').value = query;

        var d = document.getElementById('activityContainerContent');
        var offset = 0;
        if (d != null)
            offset = countSons(d);


        xhr.open("GET", "<?php echo BASE_URL;?>/sessionProgram/index.php?action=getSessionPrograms&offset="+offset+"&query="+query+"&json=true&studentId=" + studentId, true);
        xhr.send();

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

    function addSession(id){
        
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                console.log(this.responseText);
                var data =  JSON.parse(this.responseText, true);
               
                addSessionToHTML(data);
                closeModal();
            }
        };

        var session_id = id;
        var curriculum_id = "<?php echo $curr_id;?>";
        var position = countSons(document.getElementById('activities'));
        var student_id = studentId;
        
        var str = "&session_id="+session_id+"&curriculum_id="+curriculum_id+"&position="+position+"&student_id="+studentId;
        xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=addSessionToProgram', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send(str);
    }

    function addSessionToHTML(data){
        console.log(data);
        var row = document.getElementById('sessionHTMLTemplate').cloneNode(true);
        row.id = data['id'];
        row.setAttribute("data-session-id",data['id']);
        row.setAttribute("data-position", data['position']);
        row.setAttribute("data-type", 'sessionProgram');
        row.classList.remove("d-none");
        


        var name = row.querySelector("#sessionHTMLName");
        var option = row.querySelector("#sessionHTMLOption");
        var up = row.querySelector("#sessionHTMLup");
        var down = row.querySelector("#sessionHTMLdown");
        var remove = row.querySelector("#sessionHTMLremove");

        name.id = "name_" + data['id'];
        name.innerHTML = data['name'];
        name.setAttribute("data-session-id",data['id']);
        name.setAttribute("data-position", data['position']);


        option.id = "option_" + data['id'];


        up.id = "up_" + data['id'];
        up.setAttribute("data-session-id",data['id']);
        up.setAttribute("data-position", data['position']);
        up.onclick = function(){
            moveSessionProgramUp(this.getAttribute("data-session-id"));
        };


        down.id = "down_" + data['id'];
        down.setAttribute("data-position", data['position']);
        down.setAttribute("data-session-id",data['id']);
        down.onclick = function(){
            moveSessionProgramDown(this.getAttribute("data-session-id"));
        };


        remove.id = "rem_" + data['id'];
        remove.setAttribute("data-session-id",data['id']);
        remove.setAttribute("data-position", data['position']);
        remove.onclick = function(){
            askToRemoveSession(this.getAttribute('data-session-id'));
        };

        document.getElementById("activities").appendChild(row);
    }

    function askToRemoveSession(id){
        showModal("Remover", "Gostaria de remover? Isso não pode ser desfeito.", function(){
            removeSession(id);
        });

    }

    function removeSession(id){
        var xhr = new XMLHttpRequest();

        var acts = document.querySelectorAll('[data-type=sessionProgram]');
        var rem = document.getElementById(id);

        var affected = {};
        var start = parseInt(rem.getAttribute('data-position')) + 1;
        for (var i = start; i < acts.length; i++) {

            affected[acts[i].id] = i - 1;
            acts[i].setAttribute('data-position', i - 1);
        }
        console.log(affected);
        var str_json = JSON.stringify(Object.assign({}, affected));
        console.log(str_json);
        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;

            if (this.status == 200) {
                var data = this.responseText; // JSON.parse(this.responseText);
                console.log(data);
                if (data == "ERROR") {
                    return;
                }


                rem.parentNode.removeChild(rem);
            }
            closeModal();


        };

        var curriculum_id = "<?php echo $curr_id;?>";
        xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=removeSessionFromCurriculum', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

        xhr.send("&curriculum_id="+curriculum_id+"&removeId=" + rem.id + "&sessions=" + str_json);
    }

    function moveSessionProgramUp(id){
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
                    var parent = node.parentNode;

                    parent.removeChild(node);
                    parent.insertBefore(node, prev);
                    var t = node.getAttribute('data-activity-position');

                    node.setAttribute('data-position', prev.getAttribute('data-position'));
                    prev.setAttribute('data-position', t);
                }


            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=swapCurriculumProgramPosition', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");


            var activitiId1 = node.getAttribute('data-session-id');
            var activityId2 = prev.getAttribute('data-session-id');
            var position1 = node.getAttribute('data-position');
            var position2 = prev.getAttribute('data-position');
            xhr.send("&activity1=" + activitiId1 + "&activity2=" + activityId2 + "&position1=" + position1 + "&position2=" + position2);


        }
    }

    function moveSessionProgramDown(id){
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
                    var parent = node.parentNode;

                    parent.removeChild(next);
                    parent.insertBefore(next, node);
                    var t = node.getAttribute('data-position');

                    node.setAttribute('data-position', next.getAttribute('data-position'));
                    next.setAttribute('data-position', t);
                }
            };

            xhr.open("POST", '<?php echo BASE_URL; ?>/sessionProgram/index.php?action=swapCurriculumProgramPosition', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");


            var activitiId1 = node.getAttribute('data-session-id');
            var activityId2 = next.getAttribute('data-session-id');
            var position1 = node.getAttribute('data-position');
            var position2 = next.getAttribute('data-position');
            xhr.send("&activity1=" + activitiId1 + "&activity2=" + activityId2 + "&position1=" + position1 + "&position2=" + position2);
        } else {
            return;
        }
    }

</script>