<?php
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require_once ROOTPATH . '/utils/DBAccess.php';
require_once ROOTPATH . '/program/ProgramController.php';
require_once ROOTPATH . '/ui/modal.php';
$SQL = "SELECT * FROM student WHERE student.id='" . $data['studentId'] . "'";

$pc = new ProgramController();
$s_progress = $pc->checkLastRun(['studentId' => $data['studentId']]);
$canContinue = false;
if ($s_progress != null) {


    if ($s_progress['complete'] == false) {
        $canContinue = true;
    }
}

isset($data['athena'])? $athena = $data['athena'] : $athena = "false";



$db = new DBAccess();
$res = $db->query($SQL);
$student_data = mysqli_fetch_array($res);
$professionals = array();

//TODO: right join 
$SQL = "SELECT * FROM student_tutorship WHERE student_id='" . $student_data['id'] . "'";
$res = $db->query($SQL);
while ($fetch = mysqli_fetch_assoc($res)) {
    array_push($professionals, $fetch);
}
?>

<form id="swapAvatarTemplate" class="d-none" enctype="multipart/form-data" autocomplete="off" action="" method="post">


    <div class="input-group mb-3">
        <input name="student_id" id="student_id" type="text" hidden value="<?php echo $student_data['id']; ?>">
        <input required name="stimuli_file" id="stimuli_image" onchange="modal_loadFile(event)" class="inputFile" accept="image/*" type="file" style="display: none;">
        <div class="input-group-prepend">
            <button onclick="document.querySelector('#stimuli_image').click();" class="btn btn-outline-secondary" type="button">Selecionar Arquivo</button>
        </div>
        <input id="inp_fileName" type="text" readonly class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
    </div>
    <div class="container" id="preview">
        Selecione um arquivo.
    </div>
</form>
<script>
    function showHelp() {
        var content =
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/HHBPxL6vyH0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        showModal("Ajuda", content);
    }
</script>
<script>
    var modal_loadFile = function(event) {
        var inpt = document.getElementById('inp_fileName');
        var file = event.target.files[0];
        var fileURL = URL.createObjectURL(file);

        inpt.value = file.name;
        var type = file.type
        console.log("type: " + type);

        var preview = document.getElementById("preview");

        if (type.startsWith("image")) {
            var media = document.createElement("img");
            media.classList.add("img-fluid");
            media.classList.add("rounded");
            media.classList.add("mx-auto");
            media.classList.add("d-block");


            media.src = fileURL;
            preview.innerHTML = "";
            preview.appendChild(media);
        }

    };



    var modal_addImage = function(event) {

        var form = document.getElementById('newAvatar');
        console.log(form);



        let req = new XMLHttpRequest();
        let formData = new FormData(document.getElementById('newAvatar'));
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if (this.responseText == "AVATAR_SWAP") {
                    location.reload();

                } else
                    console.log("Não funcionou :( ");
            }
        };
        var url = "<?php echo BASE_URL . '/professional/index.php?action=swapStudentAvatar'; ?> ";
        req.open("POST", url);
        req.send(formData);
    }

    function selectAvatar() {
        var content = document.getElementById('swapAvatarTemplate').cloneNode(true);
        content.id = "newAvatar";
        content.classList.remove('d-none');
        showModal("Selecione o avatar", content, function() {
            modal_addImage();
        }, true);

    }
</script>

<div class="row mt-3">
    <div class="col">
        <div class="container">
            <div class="row">
                <div class="col-3">
                    <div class="row">
                        <div class="col"><img class="img-fluid rounded" src="<?php echo BASE_URL; ?>/data/student/<?php echo $student_data['id']; ?>/<?php echo $student_data['avatar']; ?>"></div>
                    </div>
                    <?php if($athena=='false'){?>
                    <div class="row">
                        <div class="col">
                            <button onclick='selectAvatar()' class=" mt-1 btn btn-primary btn-lg btn-block" type="button">Selecionar avatar... </button>
                        </div>
                    </div>
                    <?php }?>


                </div>
                <div class="col-9">
                    <div class="row alert alert-primary" role="alert">
                        <div class="col">
                            <?php echo $student_data['name']; ?>
                        </div>
                    </div>
                    <div class="row alert alert-primary" role="alert">
                        <div class="col">
                            Nascimento:
                        </div>
                        <div class="col">
                            <input disabled class="form-control" type="date" disabled value="<?php echo $student_data['birthday']; ?>">
                        </div>
                        <div class="col">
                            Sexo:
                        </div>
                        <div class="col">
                            <?php if (strcmp($student_data['sex'], "male") == 0) echo "Masculino";
                            else echo "Feminino"; ?>
                        </div>
                    </div>
                    <div class="row alert alert-primary" role="alert">
                        <div class="col-2">
                            Endereço:
                        </div>
                        <div class="col-8">
                            <input disabled class="form-control" id="city" name="city" value="<?php echo $student_data['city']; ?>">
                        </div>
                        <div class="col-2">
                            <select disabled id="state" name="state" class="form-control">
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BH</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                        </div>
                    </div>
                    <div class="row alert alert-primary" role="alert">
                        <div class="col-4">
                            Medicamentos:
                        </div>
                        <div class="col-8">
                            <input disabled class="form-control" type="text" value="<?php echo $student_data['medication']; ?>">
                        </div>

                    </div>

                    <div class="row alert alert-primary" role="alert">
                        <div class="col-4">
                            Equipe:
                        </div>

                        <div class="col-8">
                            <?php
                            for ($i = 0; $i < count($professionals); $i++) { ?>
                                <div class="row">
                                    <div class="col alert alert-danger" role="alert">
                                        <?php echo $professionals[$i]['professional_id']; ?>
                                    </div>
                                </div>
                            <?php
                            } ?>
                        <?php if($athena=='false'){?>
                            <div class="row m-1">
                                <div class="col">
                                    <button onclick="showAddProfessional()" class="btn btn-primary btn-lg btn-block" type="button" data-toggle="tooltip" data-placement="right" title="Permite que outros profissionais vejam e realizem sessões com este estudante."> Adicionar profissional </button>
                                </div>
                            </div>
                            <div class="row m-1">
                                <div class="col">
                                    <button onclick="showAddTutor()" class="btn btn-primary btn-lg btn-block" type="button" data-toggle="tooltip" data-placement="right" title="Gera um cadastro para que um aplicador realize uma sessão com o estudante."> Adicionar aplicador (pais/responsáveis). </button>
                                </div>
                            </div>
                        <?php }?>
                        </div>

                    </div>






                    <div class="row alert alert-warning" role="alert">
                        <div class="col-4">
                            Iniciar e configurar Programações de Ensino
                        </div>
                        <div class="col-8">
                            <a href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=index&athena=<?php echo $athena;?>&studentId=<?php echo $student_data['id']; ?>" class="btn btn-success btn-lg btn-block" type="button" data-toggle="tooltip" data-placement="right" title="Editar programções de Ensino."> Programações de Ensino </a>
                        </div>
                    </div>


                    
                    <div class="row alert alert-primary" role="alert">
                        <div class="col-4">
                            Sessão de Ensino (ordena programa de ensino para o estudante realizar)
                        </div>
                        <div class="col-8">
                            <a href="<?php echo BASE_URL; ?>/sessionProgram/index.php?action=editFullSession&athena=<?php echo $athena;?>&id=<?php echo $student_data['curriculum_id']; ?>" type="button" class="btn btn-primary btn-lg btn-block" data-toggle="tooltip" data-placement="right" title="Configura as sessões de ensino a serem aplicadas com o estudante.">
                                Editar Sessão de ensino 
                            </a>

                        </div>

                    </div>


                    <div class="row alert alert-primary" role="alert">
                        <div class="col-4">
                            Relatórios
                        </div>
                        <div class="col-8">
                            <a href="<?php echo BASE_URL; ?>/professional/index.php?action=studentReport&studentId=<?php echo $student_data['id']; ?>" class="btn btn-success btn-lg btn-block" type="button" data-toggle="tooltip" data-placement="right" title="Visualizar relatórios do rendimento do estudante."> Relatórios </a>
                        </div>
                    </div>

                    <div class="d-none row alert alert-primary" role="alert">
                        <div class="col-2">
                            Sessão
                        </div>
                        <div class="col-5">
                            <a href="<?php echo BASE_URL; ?>/program/index.php?action=run&studentId=<?php echo $student_data['id']; ?>&curriculumId=<?php echo $student_data['curriculum_id']; ?>&continue=new&preview=run" class="btn btn-success btn-lg btn-block" type="button" data-toggle="tooltip" data-placement="right" title="Inicializa a sessão de atividades de acordo com o programa de ensino selecionado."> Iniciar Nova Sessão </a>
                        </div>
                        <div class="col-5">
                            <a href='<?php echo BASE_URL; ?>/program/index.php?action=run&studentId=<?php echo $student_data['id']; ?>&session_id=<?php echo $s_progress['id']; ?>&continue=continue&preview=run' id='continueSessionButton' onclick="" class="btn btn-success btn-lg btn-block" type="button" data-toggle="tooltip" data-placement="right" title="Continuar a última sessão de atividades a partir da última atividade aplicada."> Continuar Última Sessão </a>
                        </div>

                    </div>

                    <div class="d-none row alert alert-primary" role="alert">
                        <div class="col-2">
                            Avaliação
                        </div>
                        <div class="col-5">
                            <a href="<?php echo BASE_URL; ?>/program/index.php?action=edit&id=<?php echo $student_data['evaluation_id']; ?>" class="btn btn-primary btn-lg btn-block" type="button">Editar Avaliação </a>
                        </div>
                        <div class="col-5">
                            <a href="<?php echo BASE_URL; ?>/program/index.php?action=edit&id=<?php echo $student_data['evaluation_id']; ?>" class="btn btn-success btn-lg btn-block" type="button">Iniciar Avaliação </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div id='help' style="position: absolute; top:5px; right: 30px;">
        <button class='btn btn-block btn-lg btn-warning' onclick="showHelp()"><i class="fas fa-question"></i></button>

    </div>
</div>




<div class="col d-none" id="newTutorTemplate">
    <div class="container">
        <h2>Adicionar aplicador</h2>

        <form id="newTutorForm">

            <div class="form-group">
                <label for="signup_username">Login</label>
                <input required type="text" class="form-control" id="signup_username" name="signup_username">
            </div>
            <div class="form-group">
                <label for="signup_name">Nome</label>
                <input required type="text" class="form-control" id="signup_name" name="signup_name">
            </div>

            <div class="form-group">
                <label for="signup_email">E-mail</label>
                <input required type="email" class="form-control" id="signup_email" name="signup_email">
            </div>

            <div class="form-group">
                <label for="signup_city">Cidade</label>
                <input required type="text" class="form-control" id="signup_city" name="signup_city">
            </div>

            <button type="button" onclick="submitNewUser()" class="btn btn-primary btn-lg btn-block">Cadastrar</button>
        </form>
    </div>
</div>

<div id="existingTutorTemplate" class="d-none">
    <div class="row ">
        <div class="col">
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <button id="selReinforcerButtonTemplate" onclick="findUser()" class="form-control btn btn-info" type="button"> <i class="fas fa-search"></i></button>
                </div>

                <input id="userIdOrEmail" class="form-control" type="text" placeholder="Nome de usuário ou E-mail">
            </div>
        </div>
    </div>
    
</div>

<div id="existingProfessionalTemplate" class="d-none">
    <div class="row ">
        <div class="col">
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <button id="selReinforcerButtonTemplate" onclick="findProfessional()" class="form-control btn btn-info" type="button"> <i class="fas fa-search"></i></button>
                </div>

                <input id="userIdOrEmail" class="form-control" type="text" placeholder="Nome de usuário ou E-mail">
            </div>
        </div>
    </div>
    
</div>


<div class="row d-none" id="tutor_type_template">
    <div class="col">
        <button class="btn btn-primary btn-block btn-lg" onclick="showAddNewTutor()">Novo Usuário</button>
    </div>
    <div class="col">
        <button class="btn btn-primary btn-block btn-lg" onclick="showAddExisting()">Usuário Existente</button>
    </div>
</div>



<script>
    function startNewSession() {
        window.location = "";
    }

    function continueSession() {

    }
    window.onload = function() {
        setSelectInput();
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
        var canContinue = '<?php echo $canContinue; ?>' == '1';
        //console.log('can  continue? ' + canContinue);
        if (!canContinue) {
            document.getElementById('continueSessionButton').classList.add('disabled'); //enable=false;
        }
    };

    function setSelectInput() {
        document.getElementById('state').value = "<?php echo $student_data['state']; ?>";
    }

    function showSelectTeachingProgram() {}

    function validate() {

        var format = /[áàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        var username = document.getElementById('signup_username');

        var valid = true;
        if (format.test(username.value)) {
            alert("O nome de usuário só pode conter letras e números (sem acentuação)");
            return false;
        }

        var form_container = document.getElementById('newTutor');
        var form = form_container.querySelector("#newTutorForm");

        for (var i = 0; i < form.elements.length; i++) {
            var inpObj = form.elements[i];

            inpObj.classList.remove("border-danger");
            if (!inpObj.checkValidity()) {
                inpObj.classList.add("border-danger");

                valid = false;
            }
        }


        return valid;

    }

    function showAddProfessional() {

        

        

        var existingTutorTemplate = document.getElementById('existingProfessionalTemplate').cloneNode(true);
        existingTutorTemplate.id = "existingProfessional";
        existingTutorTemplate.classList.remove("d-none");
        showModal("Adicionar Profissional", existingTutorTemplate);
    }

    function submitNewUser() {
        var valid = validate(document.getElementById('newTutor'));

        if (valid) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var ret = this.responseText;
                    console.log(ret);
                    if (ret == "ALREADY_EXISTS_OK") {
                        alert("Usuário já cadastrado! Adicione um usuário existente ou crie um novo cadastro com login e e-mail diferentes.");
                        closeModal();
                    }
                    if(ret == "NEW_USER_OK" ){
                        alert("Usuário adicionado com sucesso!");
                        closeModal();
                        window.location.reload();
                    }
                    else if(ret.toUpperCase().includes("ERROR")){
                        alert("Ocorreu um erro: contate o administrador.   "+ret);
                    }

                }
            };
            var form_container = document.getElementById('newTutor');
            var form = form_container.querySelector("#newTutorForm");
            var data = [];
            for (var i = 0; i < form.elements.length; i++) {
                var inpObj = form.elements[i];
                data[inpObj.id] = inpObj.value;
            }
            data['student_id'] = "<?php echo $student_data['id']; ?>";

            var str_json = JSON.stringify(Object.assign({}, data));

            xhr.open("POST", '<?php echo BASE_URL; ?>/auth/index.php?action=newTutor_json', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            xhr.send("metadata=" + str_json);
        }
    }

    function showAddExisting() {
        var existingTutorTemplate = document.getElementById('existingTutorTemplate').cloneNode(true);
        existingTutorTemplate.id = "existingTutor";
        existingTutorTemplate.classList.remove("d-none");
        swapModalContent(existingTutorTemplate);
    }

    function showAddTutor() {
        var tutor_type_template = document.getElementById("tutor_type_template").cloneNode(true);
        tutor_type_template.classList.remove('d-none');

        showModal("Adicionar Aplicador", tutor_type_template);
    }


    function findProfessional(){
        var container = document.getElementById("existingProfessional");

        var name_email = container.querySelector("#userIdOrEmail").value;

        var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState != 4)
                    return;
                if (this.status == 200) {
                    var ret = this.responseText;
                    var obj = JSON.parse(ret,true);
                    console.log(obj);

                    for(var i = 0; i < obj.length; i++){
                        var professionalRow = document.createElement("div");
                        professionalRow.classList.add("row","alert", "alert-success","mt-2");

                        var nameCol = document.createElement("div");
                        nameCol.classList.add("col");
                        nameCol.innerHTML = obj[i]['name'];

                        var userNameCol = document.createElement("div");
                        userNameCol.classList.add("col");
                        userNameCol.innerHTML = obj[i]['username'];

                        var emalCol  = document.createElement("div");
                        emalCol.classList.add("col");
                        emalCol.innerHTML = obj[i]['email'];

                        var addButtonCol = document.createElement("div");
                        emalCol.classList.add("col");
                        
                        var addButton = document.createElement("button");
                        addButton.classList.add("btn","btn-lg","btn-success");
                        addButton.innerHTML = "Adicionar";
                        addButton.setAttribute("data-username",obj[i]['username']);
                        addButton.onclick = function(){
                            addProfessional(this.getAttribute("data-username"));
                        };

                        addButtonCol.appendChild(addButton);
                        professionalRow.appendChild(nameCol);
                        professionalRow.appendChild(userNameCol);
                        professionalRow.appendChild(emalCol);
                        professionalRow.appendChild(addButtonCol);

                        container.appendChild(professionalRow);
                    }
                }
            };
        
            xhr.open("POST", '<?php echo BASE_URL; ?>/auth/index.php?action=findProfessional_json', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            xhr.send("nameOrEmail=" + name_email);
    }


    function findUser(){
        var container = document.getElementById("existingTutor");

        var name_email = container.querySelector("#userIdOrEmail").value;

        var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState != 4)
                    return;
                if (this.status == 200) {
                    var ret = this.responseText;
                    var obj = JSON.parse(ret,true);
                    console.log(obj);

                    for(var i = 0; i < obj.length; i++){
                        var professionalRow = document.createElement("div");
                        professionalRow.classList.add("row","alert", "alert-success","mt-2");

                        var nameCol = document.createElement("div");
                        nameCol.classList.add("col");
                        nameCol.innerHTML = obj[i]['name'];

                        var userNameCol = document.createElement("div");
                        userNameCol.classList.add("col");
                        userNameCol.innerHTML = obj[i]['username'];

                        var emalCol  = document.createElement("div");
                        emalCol.classList.add("col");
                        emalCol.innerHTML = obj[i]['email'];

                        var addButtonCol = document.createElement("div");
                        emalCol.classList.add("col");
                        
                        var addButton = document.createElement("button");
                        addButton.classList.add("btn","btn-lg","btn-success");
                        addButton.innerHTML = "Adicionar";
                        addButton.setAttribute("data-username",obj[i]['username']);
                        addButton.onclick = function(){
                            addExistingUser(this.getAttribute("data-username"));
                        };

                        addButtonCol.appendChild(addButton);
                        professionalRow.appendChild(nameCol);
                        professionalRow.appendChild(userNameCol);
                        professionalRow.appendChild(emalCol);
                        professionalRow.appendChild(addButtonCol);

                        container.appendChild(professionalRow);
                    }
                }
            };
        
            xhr.open("POST", '<?php echo BASE_URL; ?>/auth/index.php?action=findTutor_json', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            xhr.send("nameOrEmail=" + name_email);
    }

    function addProfessional(id){
        

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;
            if (this.status == 200) {
                var ret = this.responseText;
                console.log(ret);
                if(ret=="OK"){
                    alert("Profissional adicionado!");
                    window.location.reload();
                }
                else if(ret =="ALREADY_EXISTS"){
                    alert("O profissional já possui acesso ao estudante!");
                }
            }
        };  
        var student_id = "<?php echo $student_data['id']; ?>";
        
        xhr.open("POST", '<?php echo BASE_URL; ?>/professional/index.php?action=addExistingTutorship', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send("professional_id=" + id+"&student_id="+student_id);
    }

    function addExistingUser(id){
        

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState != 4)
                return;
            if (this.status == 200) {
                var ret = this.responseText;
                console.log(ret);
                if(ret=="OK"){
                    alert("Aplicador adicionado!");
                    window.location.reload();
                }
                else if(ret =="ALREADY_EXISTS"){
                    alert("O aplicador já possui acesso ao estudante!");
                }
            }
        };  
        var student_id = "<?php echo $student_data['id']; ?>";
        
        xhr.open("POST", '<?php echo BASE_URL; ?>/professional/index.php?action=addExistingTutorship', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send("professional_id=" + id+"&student_id="+student_id);
    }
    function showAddNewTutor() {
        var tutorContainer = document.getElementById("newTutorTemplate").cloneNode(true);
        tutorContainer.classList.remove('d-none');
        tutorContainer.id = "newTutor";
        swapModalContent(tutorContainer);
        //showModal("Adicionar Aplicador",tutorContainer);
    }
</script>