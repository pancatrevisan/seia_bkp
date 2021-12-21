<?php 
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if(!defined('ROOTPATH')){
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';
require_once ROOTPATH . '/ui/modal.php';
checkUser(["professional","admin"], BASE_URL);

$user_id = $_SESSION['username'];

?>



<script>
    
   /* setInterval(function(){ 
        console.log("evento");
        window.dispatchEvent(new Event('resize'));
    }, 1000);*/

    var modal_loadFile = function (event) {
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
    
    
    
    var modal_addImage = function(event){
        
        var form = document.getElementById('newAvatar');
     
        
        
        
        let req = new XMLHttpRequest();
        let formData = new FormData(document.getElementById('newAvatar'));
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
               console.log(this.responseText);
                if(this.responseText == "AVATAR_SWAP"){
                    location.reload();
 
                }
                else
                    console.log("Não funcionou :( ");
            }
        };
        var url = "<?php echo BASE_URL . '/professional/index.php?action=changeAvatar'; ?> ";
        req.open("POST", url);
        req.send(formData);
    }
    
    function selectAvatar(){
        var content = document.getElementById('swapAvatarTemplate').cloneNode(true);
        content.id = "newAvatar";
        content.classList.remove('d-none');
        showModal("Selecione o avatar", content, function () {
            modal_addImage();
        }, true);
        
    }

    function showHelp(){
        //var content = '<iframe width="560" height="315" src="https://www.youtube.com/embed/ItHgdKTRsNo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        //showModal("Ajuda",content);
        askToPerformTour();
    }
</script>


<form id="swapAvatarTemplate"class="d-none" enctype="multipart/form-data" autocomplete="off" action="" method="post" >

    
    <div class="input-group mb-3">
        <input name="student_id" id="student_id" type="text" hidden value="<?php echo $student_data['id'];?>">
        <input required name="stimuli_file" id="stimuli_image" onchange="modal_loadFile(event)" class="inputFile" accept="image/*" type="file" style="display: none;">
         <div class="input-group-prepend">
             <button  onclick="document.querySelector('#stimuli_image').click();"class="btn btn-outline-secondary" type="button">Selecionar Arquivo</button>
         </div>
         <input id="inp_fileName" type="text" readonly class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
     </div>
     <div class="container" id="preview">
        Selecione um arquivo.
    </div>
</form>

<div class="row mt-3 mb-3">
    <div class="col-2 bg-secondary p-3">
        <div class="row" >
            <div class="col">
            <img class="img-fluid rounded " width="100%" height="auto" src="<?php echo BASE_URL;?>/data/user/<?php echo $user_id;?>/avatar.png">
            <button onclick='selectAvatar()' class=" mt-1 btn btn-danger btn-lg btn-block border border-dark "type="button"> Trocar avatar </button>
            <a href="<?php echo BASE_URL;?>/auth/index.php?action=changePassword" type="button" class="mt-2 mn-2 btn btn btn-block  btn-danger border border-dark"> Alterar senha </a>
            <a href="<?php echo BASE_URL;?>/auth/index.php?action=logout" type="button" class="mt-2 mn-2 btn btn btn-block  btn-danger border border-dark"> Sair </a>
            </div>
        </div>
    </div>
    <div class="col-10">
        <div class="container">
            
         
            <div class="card-columns mt-3">
                
                
                
                <div class="card  bg-light" id="tutoEstudantes">
                    <div class="card-header">Estudantes</div>
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar/cadastrar estudantes</p>
                        <a  href="index.php?action=student" class="btn btn btn-block  btn-danger border border-dark">Estudantes</a>
                    </div>
                </div>

                <div class="card  bg-light" id="tutoEstimulos">
                    <div class="card-header">Estímulos</div>
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar/cadastrar estímulos</p>
                        <a  href="<?php echo BASE_URL . "/stimuli";?>" class="btn btn btn-block  btn-primary border border-dark">Estímulos</a>
                    </div>
                </div>
                
                
                <div class="card  bg-light" id="tutoAtividades">
                    <div class="card-header ">Atividades</div>
                    <div class="card-body">
                        
                        <p class="card-text">Crie ou Edite Atividades.</p>
                        <a href="<?php echo BASE_URL . "/activity";?>" class="btn btn btn-block  btn-warning border border-dark">Atividades</a>
                    </div>
                </div>
                
                <div class="card  bg-light" id="tutoReforcos">
                    <div class="card-header ">Reforços</div>
                    <div class="card-body">
                        
                        <p class="card-text">Crie ou Edite Reforços.</p>
                        <a href="<?php echo BASE_URL . "/activity/index.php?action=reinforcementIndex";?>" class="btn btn-success  btn-block border-dark">Reforços</a>
                    </div>
                </div>

                
                <div class="card  bg-light" id="exampleActivity"> 
                    <div class="card-header ">Atividades Públicas <span class="badge badge-warning">Veja conteúdo criado por outros usuários!</span></div>
                    
                    <div class="card-body">
                        
                        <p class="card-text">Visualizar atividades criadas pela comunidade.</p>
                        <a href="<?php echo BASE_URL . "/activity/index.php?action=repository";?>" class="btn btn-info  btn-block border-dark">Atividades públicas</a> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <?php 
        if(isGodMode()){
            ?>
            <div id='athena' style="position: absolute; bottom:5px; right: 30px;" >
                <a class='btn btn-block btn-lg ' title="Modo 'deus'" href="<?php echo BASE_URL;?>/athena"> <img alt="Modo 'deus' " width="56px" class="img-rounded" src="<?php echo BASE_URL;?>/ui/athena.png"/>  </a>

            </div>
            <?php
        }
    ?>

    <div id='help' style="position: absolute; top:5px; right: 30px;" >
    <button class='btn btn-block btn-lg btn-warning' onclick="showHelp()"><i class="fas fa-question"></i></button>

    </div>
</div>

<script>
var tuto_finished = "<?php echo $_SESSION['tuto_finished'];?>" == "1" ;
console.log("Tuto finished? "+tuto_finished);
var enjoyhint_instance = new EnjoyHint();

document.body.onload=function(){
    var steps = [
        {'next #UIMenu': "Aqui você pode acessar o menu de navegação.",
            'nextButton' :{'text':'Próximo'},
            "skipButton" : {className: "d-none"}
        },
        {'next #tutoEstimulos': "É possível cadastrar estímulos (áudio e imagens) para serem utilizados nas atividades",
            'nextButton' :{'text':'Próximo'},
            "skipButton" : {className: "d-none"}
        },
        {'next #tutoEstudantes': "Aqui é possível visualizar os estudantes cadastrados, editar seus programas e sessões de ensino e também adicionar novos estudantes",
            'nextButton' :{'text':'Próximo'},
            "skipButton" : {className: "d-none"}
        },
        {'next #tutoAtividades': "Aqui é possível visualizar, editar e criar novas atividades",
            'nextButton' :{'text':'Próximo'},
            "skipButton" : {className: "d-none"}
        },
        {'next #tutoReforcos': "Nos reforços, é possível cadastrar recompensas baseadas em imagens, textos, áudio ou vídeos. Um reforço criado pode ser utilizado com diferentes estudantes.",
            'nextButton' :{'text':'Próximo'},
            "skipButton" : {className: "d-none"}
        }
    ];
    enjoyhint_instance.set(steps);
    if(!tuto_finished)
        askToPerformTour();
};
//set script config



function showTips(){
    //run Enjoyhint script
    var enjoyhint_instance = new EnjoyHint();
    enjoyhint_instance.run();
}
</script>


<script>
    function mobileAndTabletcheck() {
    var check = false;
    (function (a) {
        if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4)))
            check = true;
    })(navigator.userAgent || navigator.vendor || window.opera);
    return check;
}
    function askToPerformTour(){
        if(mobileAndTabletcheck()){
            alert("A criação de atividades deve ser feita utilizando um computador/notebook :)");
            return;
        }
        var steps = [
        {'click #tutoAtividades': "Aqui é possível visualizar, editar e criar novas atividades",
            'nextButton' :{'text':'Próximo'},
            "skipButton" : {className: "d-none"}
        }
    ];  
    
    var hint = new EnjoyHint();
    hint.set(steps);
    hint.run();
}
</script>