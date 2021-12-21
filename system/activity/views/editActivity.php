<?php

$sel_lang = 'ptb';
if (!defined('ROOTPATH')) {
    require '../root.php';
}
?>

<script src="<?php echo BASE_URL; ?>/external/face-api.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/AttributeDescriptor.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/Stimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/DFTImage.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/TextStimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/ImageStimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/AudioStimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/TextInputStimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/YoutubeVideo.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/CamCaptureStimulus.js"></script>



<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/Activity.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/Instruction.js"></script>

<?php
/* TODO: user
 * TODO: gifs http://themadcreator.github.io/gifler/docs.html
 * TODO https://github.com/matt-way/gifuct-js/blob/master/demo/demo.js
 */


foreach ($data['instructions'] as $instruction) {
    echo '<script type="text/javascript" src="' . $instruction . '"></script>';
}
require ROOTPATH . "/ui/modal.php";
?>

<?php require_once 'newStimuliModal.php'; ?>

<script>
    var GLOBAL_YT_READY = false;
    var tag = document.createElement('script');
    console.log("GET CONTROL");
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    console.log("first script Tag");
    console.log(firstScriptTag);
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


    function onYouTubeIframeAPIReady() {
        GLOBAL_YT_READY = true;
    }
</script>

<script>
    var hint = null;


    var selNumber = 0;

    function mobileAndTabletcheck() {
        var check = false;
        (function(a) {
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4)))
                check = true;
        })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    }
    var tuto_finished = "<?php echo $_SESSION['tuto_finished']; ?>" == "1";



    function askToPerformTour() {


        if (tuto_finished)
            return;
        if (mobileAndTabletcheck()) {
            alert("A criação de atividades deve ser feita utilizando um computador/notebook :)");
            return;
        }


        var steps = [

            {
                'click [href="javascript:showEditor(0)"]': "Clicando no botão Editar é possível editar (inseir estímulos) uma tela da atividade.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            },

            {
                'next #drawCanvas': "Esta é uma previa da tela que será exibida para o estudante.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                'textColor': "#000",
                "showSkip": false
            },

            {
                'click #button_edit_model': "É possível inserir estímulos já cadastrados à atividade. Selecione um estímulo modelo.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            }
        ];

        hint = new EnjoyHint();
        hint.set(steps);
        hint.run();
    }

    function askToPerformTour_2() {
        console.log("askToPerformTour_2");
        if (tuto_finished)
            return;
        if (mobileAndTabletcheck()) {
            alert("A criação de atividades deve ser feita utilizando um computador/notebook :)");
            return;
        }

        selNumber++;

        var steps = [

            {
                'click #modalContent ': "Selecione uma imagem.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            }
        ];

        hint = new EnjoyHint({
            onEnd: function() {
                setTimeout(function() {
                    askToPerformTour_3();
                }, 1000);

            }
        });
        hint.set(steps);
        hint.run();
    }






    function askToPerformTour_3() {
        console.log("askToPerformTour_3");
        if (tuto_finished)
            return;
        if (mobileAndTabletcheck()) {
            alert("A criação de atividades deve ser feita utilizando um computador/notebook :)");
            return;
        }


        var steps = [

            {
                'next #drawCanvas': "Clique e arraste o estímulo para a posição que desejar.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                'textColor': "#000",
                "showSkip": false
            },
            {
                'click #button_edit_images  ': "Insira um estímulo de comparação.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            }
        ];

        hint = new EnjoyHint();
        hint.set(steps);
        hint.run();
    }







    function askToPerformTour_4() {
        console.log("askToPerformTour_4");
        if (tuto_finished)
            return;
        if (mobileAndTabletcheck()) {
            alert("A criação de atividades deve ser feita utilizando um computador/notebook :)");
            return;
        }


        var steps = [

            {
                'click #modalContent ': "Selecione uma imagem.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            }
        ];

        hint = new EnjoyHint({
            onEnd: function() {
                setTimeout(function() {
                    askToPerformTour_5();
                }, 1000);

            }
        });
        hint.set(steps);
        hint.run();
    }



    function askToPerformTour_5() {

        if (tuto_finished)
            return;
        if (mobileAndTabletcheck()) {
            alert("A criação de atividades deve ser feita utilizando um computador/notebook :)");
            return;
        }



        var steps = [{
                'next #drawCanvas': "Clique e arraste o estímulo para a posição que desejar",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                'textColor': "#000",
                "showSkip": false
            },
            {
                'click #button_edit_expectedImage ': "Configure o estímulo de comparação correto, aquele que o estudante deve selecionar.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            },
        ];

        hint = new EnjoyHint({
            onEnd: function() {
                setTimeout(function() {
                    askToPerformTour_6();
                }, 1000);

            }
        });
        hint.set(steps);
        hint.run();
    }











    function askToPerformTour_6() {

        if (tuto_finished)
            return;
        if (mobileAndTabletcheck()) {
            alert("A criação de atividades deve ser feita utilizando um computador/notebook :)");
            return;
        }


        var steps = [


            {
                'click #modalContent ': "Configure o estímulo de comparação correto, aquele que o estudante deve selecionar.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            },
            {
                'click #closeEditor ': "O X fecha a janela. As modificações realizadas não são perdidas.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            },


            {
                'click #tourSave ': "Guarde as alterações feitas na atividade.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            },
            {
                'click #tourPreview ': "Teste o resultado.",
                'nextButton': {
                    text: "Próximo"
                },
                "skipButton": {
                    className: "d-none"
                },
                "showSkip": false
            }
        ];

        hint = new EnjoyHint({
            onEnd: function() {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                    }
                };
                var url = "<?php echo BASE_URL; ?>/professional/index.php?action=setPerformedTutorial";
                xhttp.open('POST', url, true);
                xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhttp.send("");
            }
        });
        hint.set(steps);
        hint.run();
    }
</script>

<script>
    function showHelp() {
        /*var content = '<h1> Atividade de Exibir Instrução</h1>'
        +'<iframe width="560" height="315" src="https://www.youtube.com/embed/0byoEOph1Bc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        + '<h1> Atividade de Matching-to-Sample</h1>'    
        + '<iframe width="560" height="315" src="https://www.youtube.com/embed/xhau1w-q7Wo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        +  '<h1> Atividade de Inserção de Texto</h1>'       
        + '<iframe width="560" height="315" src="https://www.youtube.com/embed/ysUz3CdqrNk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        +  '<h1> Atividade de drag (arrastar)</h1>'           
        + '<iframe width="560" height="315" src="https://www.youtube.com/embed/MumBJUdx-XU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        showModal("Ajuda",content);
        */

        askToPerformTour();
    }
</script>

<script>
    var stimuliResultDivId = "modalContent";
    var showMoreButtonId = "showMoreButtonId";
    var activity = null;
    var animate;
    var G_filter_id = "filterSdasdaf";
    var last = -1;


    var G_instruction = -1;
    var G_types = "";
    var G_attr_descriptor = null;

    /*
     * Redraws canvas
     * @returns {undefined}
     */
    function update() {


        var d = new Date();
        var curr = d.getTime();

        var dt = curr - last;
        last = curr;

        var canvas = document.getElementById('drawCanvas');
        /*canvas.width = max_w;
         canvas.height = max_h;*/
        var ctx = canvas.getContext("2d");

        var scale = 1;
        //activity.render(context);
        if (activity != null && activity.instructionBeingEdited != null)
            activity.renderPreview(ctx, scale);
        ctx.font = "30px Arial";
        ctx.fillStyle = "#000000";
        //ctx.fillText("Preview", 10, 50);
    }

    function resize() {
        return;
        var s_w = document.getElementById('canvasPanel').offsetWidth;
        var s_h = document.getElementById('canvasPanel').offsetHeight;


        var w_w = window.innerWidth;
        var w_h = window.innerHeight * 0.9;


        var max_w, max_h;

        if (s_h > s_w) {
            max_w = s_w;
            max_h = s_w * 0.75;
        } else {
            max_w = s_w;
            max_h = Math.floor(s_w * 0.75);
        }


        if (max_h > w_h) {
            max_h = w_h;
            max_w = max_h * 1.25;
        }
        document.getElementById('drawCanvas').width = max_w;
        document.getElementById('drawCanvas').height = max_h;

    }

    function waitForLoad() {
        if (activity != null) {
            if (activity.isReady()) {
                document.getElementById('editableInstructions').innerHTML = "";
                activity.edit();
                var i;
                for (i = 0; i < activity.instructions.length; i++) {
                    var x;

                    var inst = activity.instructions[i];
                    if (inst.editable) {

                        var card =
                            '<div class="card border-dark m-3 " > ' +
                            //'<img class="card-img-top" src="..." alt="Card image cap">'+
                            '<h5 class="card-header">' + inst.description + '</h5>' +
                            '<div class="card-body">' +
                            //'<h5 class="card-text">' + inst.description + '</h5>' +
                            '<a href="javascript:showEditor(' + inst.position + ')" class="btn btn-outline-info btn-lg ">' + "Editar" + '</a>' +
                            '</div>' +
                            '</div>';
                        document.getElementById('editableInstructions').innerHTML += card;
                    }

                }

                animate = window.setInterval(update, 66);
            } else {
                document.getElementById('editableInstructions').innerHTML = "Carregando dados...";


                setTimeout(function() {
                    waitForLoad();
                }, 1000);
            }

        } else {

            setTimeout(function() {
                waitForLoad();
            }, 1000);
        }


    }
    /**
     * Loads an instruction and add cards for each editable instruction
     * @param {type} id
     * @returns {undefined}
     */
    function load(id) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                if (this.responseText.length <= 0) {
                    alert("Erro carregando atividade. Contate o administrador");
                } else {
                    var canvas = document.getElementById('drawCanvas');

                    activity = new Activity(id, this.responseText, document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");

                    waitForLoad();
                }
            }
        };

        xhttp.open("GET", "<?php echo BASE_URL; ?>/activity/index.php?action=getActivity&id=" + id, true);
        xhttp.send();
    }
    document.body.onload = function() {

        if (mobileAndTabletcheck()) {
            alert("A criação de atividades deve ser feita utilizando um computador/notebook :)");
            return;
        }
        load('<?php echo $data['activity_id'] ?>');
        window.addEventListener("resize", resize);
        resize();
        var canvas = document.getElementById('drawCanvas');
        canvas.addEventListener("mousedown", function(evt) {
            activity.pointerDown(evt);
        });
        canvas.addEventListener("mousemove", function(evt) {
            activity.pointerMove(evt);
        });
        canvas.addEventListener("mouseup", function(evt) {
            activity.pointerUp(evt);
        });

        canvas.addEventListener("touchstart", function(evt) {
            activity.pointerDown(evt);
        });
        canvas.addEventListener("touchmove", function(evt) {
            activity.pointerDown(evt);
        });
        canvas.addEventListener("touchend", function(evt) {
            activity.pointerUp(evt);
        });
        canvas.addEventListener("touchcancel", function(evt) {
            activity.pointerUp(evt);
        });

        if (activity != null)
            activity.resize();
        document.getElementById('activityDifficulty').value = "<?php echo $data['activity_difficulty']; ?>";
        if (!tuto_finished)
            askToPerformTour();
    };
</script>



<div id="paperModel" class="paper" hidden>
    <div class="paper-content">
        <textarea autofocus></textarea>
    </div>
</div>

<div style="display: none;" id='runActivityData'></div>

<div class="row mt-3 " id="activityInfo">
    <div class="col">
        <div class="row alert alert-primary m-2 ">
            <div class="col ">
                <button id="tourCancelar" type="button" class="btn btn-info btn-lg btn-block" onclick="window.location = 'index.php';">Cancelar</button>
            </div>
            <div class="col">
                <button id="tourPreview" type="button" class="btn btn-info btn-lg btn-block" onclick="saveActivity('preview')">Pré-visualizar</button>
            </div>
            <div class="col">
                <button id="tourSave" type="button" class="btn btn-info btn-lg btn-block" onclick="saveActivity('save')">Guardar alterações</button>
            </div>
            <div class="col">
                <button id="tourAsNew" type="button" class="btn btn-info btn-lg btn-block" onclick="saveActivity('asNew')">Guardar como Nova Atividade</button>
            </div>
            <div class="col d-none">
                <button type="button" class="btn btn-info btn-lg btn-block" onclick="saveActivity('asTemplate')">Guardar como template</button>
            </div>


        </div>

        <div class="row alert alert-primary m-2">
            <div class="col-sm-2"> Nome da Atividade: </div>
            <div class="col-sm-6"><input class="form-control" type='text' id="activityName" name='activityName' value="<?php echo $data['activity_name']; ?>"></div>
            <div class="col-sm-2"> Nível de Dificuldade: </div>




            <div class="col-sm-2">

                <select class="form-control" id="activityDifficulty" name='activityDifficulty'>
                    <option value="NOT_RATED">Sem avaliação</option>
                    <option value="EASY">Fácil</option>
                    <option value="MEDIUM"> Médio</option>
                    <option value="HARD">Difícil</option>
                </select>

            </div>
        </div>


    </div>
    <div id='help' style="position: absolute; top:5px; right: 30px;">
        <button class='btn btn-block btn-lg btn-warning' onclick="showHelp()"><i class="fas fa-question"></i></button>

    </div>
</div>

<div class="row mt-3" id="editableInstructionsRow">
    <div class="col" class="w-100">
        <!--<div id="editableInstructions" class="card-columns "> -->
        <div id="editableInstructions" class="mx-auto w-100">

        </div>
    </div>
</div>



<script>
    function saveActivity(dest) {

        if (activity != null) {
            var data = [];


            var name = "" + document.getElementById('activityName').value;

            data['name'] = name; //document.getElementById('activityName').value;

            data['difficulty'] = document.getElementById('activityDifficulty').value;
            //data['antecedent'] = document.getElementById('antecedent').value;
            //data['behavior'] = document.getElementById('behavior').value;
            //data['consequence'] = document.getElementById('consequence').value;
            data['id'] = activity.id;
            //var xhr = new XMLHttpRequest();
            //xhr.onreadystatechange = function() {
            //    if (this.readyState != 4)
            //        return;

            //    if (this.status == 200) {
            //        var ret = this.responseText;

            //        activity.saveActivity(dest);
            //    }
            //};
            console.log(data);
            var str_json = JSON.stringify(Object.assign({}, data));
            activity.saveActivity(dest, "", str_json);


            //xhr.open("POST", '<?php echo BASE_URL; ?>/activity/index.php?action=updateMetadata', true);
            //xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            //xhr.send("metadata=" + str_json);



        } else {
            alert("Erro salvando atividade. Contate o administrador");
        }
    }

    function hideEdit() {
        document.getElementById('edit').classList.add('d-none');
        document.getElementById('menu').classList.remove('d-none');
        document.getElementById('activityInfo').classList.remove('d-none');

        document.getElementById('editableInstructionsRow').classList.remove('d-none');
    }

    function showEditor(inst_number) {
        activity.instructionBeingEdited = inst_number;
        G_instruction = activity.instructions[inst_number];
        createEditor(activity.instructions[inst_number]);
        document.getElementById('editableInstructionsRow').classList.add('d-none');
        document.getElementById('menu').classList.add('d-none');
        document.getElementById('edit').classList.remove('d-none');
        document.getElementById('activityInfo').classList.add('d-none');
        resize();
    }

    /**
     * Adds a button to edit an instruction attribute
     * @param {type} inst
     * @param {type} attr
     * @param {type} attrType
     * @param {type} val
     * @param {type} min
     * @param {type} max
     * @returns {undefined}
     */
    function addEditButton(inst, attr_descriptor) { //, attrType,edit, description, val){                
        var button = document.createElement('button');


        button.id = "button_edit_" + attr_descriptor.attributeName;

        button.classList.add('btn', 'btn-primary', 'btn-lg', 'btn-block');
        button.type = "button";
        button.innerHTML = attr_descriptor.attributeDescription;

        var buttonCol = document.createElement('div');
        buttonCol.classList.add('col');
        buttonCol.appendChild(button);

        var buttonRow = document.createElement('div');
        buttonRow.classList.add('row');

        button.onclick = function() {
            this.blur();

            editAttribute(inst, attr_descriptor);
        };


        var col = document.createElement('div');
        col.classList.add('col');

        var row = document.createElement('div');
        row.classList.add('row', 'p-2');



        buttonRow.appendChild(buttonCol);


        col.appendChild(buttonRow);
        row.appendChild(col);
        document.getElementById("controls").appendChild(row);
    }
    /**
     * Adds the buttons to open each instruction editor.
     * @param {type} instruction instruction unic ID (numbr
     * @returns {undefined}
     */
    function createEditor(instruction) {
        document.getElementById("controls").innerHTML = "";
        var att = instruction.editableAttributes;
        document.getElementById("editInstructionName").innerHTML = instruction.description;
        var i;
        for (i = 0; i < att.length; i++) {
            addEditButton(instruction, att[i]);
        }
    }






    /**********************NEW**************************************/
    function editAttribute(instruction, attr_descriptor) {


        G_attr_descriptor = attr_descriptor;
        var attr_desc = attr_descriptor;
        attr_desc.attributeTypes;
        if (attr_desc.attributeTypes.length > 0) {
            if (attr_desc.attributeTypes.length == 1 && attr_desc.attributeTypes[0] == 'boolean') {
                editBoolean(instruction, attr_descriptor);
                return;
            } else if (attr_desc.attributeTypes.length == 1 && attr_desc.attributeTypes[0] == 'integer') {
                editInteger(instruction, attr_descriptor);
                return;
            } else if (attr_desc.attributeTypes.length == 1 && attr_desc.attributeTypes[0] == 'text') {
                editText(instruction, attr_descriptor);
                return;
            } else if (attr_desc.attributeTypes.length == 1 && attr_desc.attributeTypes[0] == 'callFunction') {
                //throw new Error('add position');
                instruction[attr_desc.attributeEditType]();
                return;
            } else if (attr_desc.attributeTypes.length == 1 && attr_desc.attributeTypes[0] == 'selection') {
                ///Cria um seletor com dropdown
                editSelection(instruction, attr_descriptor);
                return;
            } else if (attr_desc.attributeTypes.length == 1 && attr_desc.attributeTypes[0] == 'color') {
                ///Cria um seletor para cor
                editColor(instruction, attr_descriptor);
                return;
            } else { //search in database or locally.
                //if user can add text
                editStimuli(instruction, attr_descriptor);
                return;
            }
        }
    }

    function createSelectStimuliContainer(types, instruction, attr_descriptor, showMore = true, showNew = true) {
        var content = document.createElement('div');
        content.id = stimuliResultDivId;
        content.classList.add('card-columns');
        var all = document.createElement('div');

        if (showNew) {
            var newStimullusButtonRow = document.createElement('div');
            newStimullusButtonRow.classList.add('row');
            var newStimulusButtonCol = document.createElement('div');
            newStimulusButtonCol.classList.add('col');
            var newStimulusButton = document.createElement('button');
            newStimulusButton.classList.add('btn', 'btn-primary', 'btn-lg', 'btn-block');
            newStimulusButton.innerHTML = "Adicionar Novo estímulo";
            newStimulusButton.onclick = function() {
                createNewStimuli(types);
            };

            all.appendChild(newStimulusButton);


        }
        var showCreateNewText = false;
        var types_arr = types.split(',');
        var t;

        for (t = 0; t < types_arr.length; t++) {
            if (types_arr[t] == 'text') {
                showCreateNewText = true;
            }
        }
        if (showCreateNewText) {
            var newTextButtonRow = document.createElement('div');
            newTextButtonRow.classList.add('row');
            var newTextButtonCol = document.createElement('div');
            newTextButtonCol.classList.add('col');
            var newTextButton = document.createElement('button');
            newTextButton.classList.add('btn', 'btn-primary', 'btn-lg', 'btn-block');
            newTextButton.innerHTML = "Adicionar Novo Texto";
            newTextButton.onclick = function() {
                addNewText(instruction, attr_descriptor);
            };

            all.appendChild(newTextButton);
        }


        var showCreateNewVideo = false;
        types_arr = types.split(',');
        t;

        for (t = 0; t < types_arr.length; t++) {
            if (types_arr[t] == 'video') {
                showCreateNewVideo = true;
            }
        }
        if (showCreateNewVideo) {
            var newTextButtonRow = document.createElement('div');
            newTextButtonRow.classList.add('row');
            var newTextButtonCol = document.createElement('div');
            newTextButtonCol.classList.add('col');
            var newTextButton = document.createElement('button');
            newTextButton.classList.add('btn', 'btn-primary', 'btn-lg', 'btn-block');
            newTextButton.innerHTML = "Adicionar Vídeo";
            newTextButton.onclick = function() {
                addNewVideo(instruction, attr_descriptor);
                //addNewText(instruction, attr_descriptor);
            };

            all.appendChild(newTextButton);
        }


        var filter = document.getElementById("filterFormTemplate").cloneNode(true);
        filter.id = G_filter_id;
        filter.classList.remove('d-none');
        all.appendChild(filter);
        all.appendChild(content);

        if (showMore) {
            var showMoreDiv = document.createElement('div');
            var showMoreButton = document.createElement('button');
            showMoreButton.classList.add('btn', 'btn-primary', 'btn-lg', 'btn-block');
            showMoreButton.innerHTML = "Carregar Mais Resultados";
            //showMoreButton.disabled = true;
            showMoreDiv.appendChild(showMoreButton);
            all.appendChild(showMoreDiv);
            showMoreButton.id = showMoreButtonId;
        }
        return all;
    }

    function addNewVideo(instruction, attr_descriptor) {
        //throw new Error("add new text");
        var params = [];
        params['type'] = 'video';
        instruction.setAttributeValue(attr_descriptor, "", params);
        closeModal();
    }
    /**
     * Cria um modal com os tipos de estímulos que o usuário pode selecionar
     * @param {type} ints
     * @param {type} attr
     * @returns {undefined}     */
    function showSelectStimuli_createModal(types, instruction, attr_descriptor) {



        if (attr_descriptor.attributeTypes.length == 1 && attr_descriptor.attributeTypes[0] == 'stimulusID') {
            var container = createSelectStimuliContainer(types, instruction, attr_descriptor, false, false);
            showModal("Selecionar Estímulo", container, null, false);
            showSelecLocalStimuli(types, instruction, attr_descriptor, [instruction[attr_descriptor.attributeName]]);

        } else {
            var container = createSelectStimuliContainer(types, instruction, attr_descriptor, true, true);
            showModal("Selecionar Estímulo", container, null, false);
            showSelectStimuli(types, '', instruction, attr_descriptor);
        }
    }


    function showSelecLocalStimuli(types, instruction, attr_descriptor) {
        var exclude = [];
        var i;
        for (i = 0; i < instruction.ignoreInLocalSearch.length; i++) {
            exclude.push(instruction[instruction.ignoreInLocalSearch[i]]);
        }
        for (var key in instruction.idStimulis) {
            var obj = instruction.idStimulis[key];
            if (!exclude.includes(obj.localID)) {
                addLocalStimulus(obj, instruction, attr_descriptor);
            }

        }
    }

    function showSelectStimuli(types, query, instruction, attr_descriptor) {
        var xhttp = new XMLHttpRequest();
        //get stimulis
        xhttp.onreadystatechange = function() {

            if (this.readyState == 4 && this.status == 200) {

                if (this.responseText.length <= 0) {
                    console.log("lascou-se");
                } else {
                    document.getElementById(showMoreButtonId).disabled = false;
                    console.log(this.responseText);
                    if (this.responseText == "STIMULI_NOT_FOUND") {
                        console.log("nao existe");
                        document.getElementById(showMoreButtonId).disabled = true;
                        return;
                    }

                    var objs = JSON.parse(this.responseText, true);
                    if (objs['results'].length <= 0)
                        document.getElementById(showMoreButtonId).disabled = true;


                    ///configures the 'show more results' button.
                    document.getElementById(showMoreButtonId).onclick = function() {
                        showSelectStimuli(types, query, instruction, attr_descriptor);
                    };

                    //present stimuli
                    for (key in objs['results']) {
                        var obj = objs['results'][key];
                        addStimulus(obj, instruction, attr_descriptor);

                    }
                    if (hint) {
                        if (selNumber == 0) {
                            askToPerformTour_2();
                        } else {
                            askToPerformTour_4();
                        }
                    }

                }
            }
        };

        var d = document.getElementById(stimuliResultDivId); // where the stimulis go
        var offset = countSons(d);

        xhttp.open("GET", "../stimuli/index.php?action=get_as_json&types=" + types + "&query=" + query + "&offset=" + offset, true);
        xhttp.send();
    }

    function countSons(element) {

        var activities = element.childNodes;
        var num = 0;
        for (var i = 0; i < activities.length; i++) {
            if (activities[i].nodeType != Node.TEXT_NODE) {
                num++;
            }
        }
        return num;
    }

    function addLocalStimulus(stimulus, instruction, attr_descriptor, params = null) {
        //console.log(stimulus);
        var card = document.createElement('div');
        card.classList.add('card');

        var cardBody = document.createElement('div');
        cardBody.classList.add('card-body');



        var cardButton = document.createElement('button');
        cardButton.classList.add('btn', 'btn-primary');

        cardButton.innerHTML = "Selecionar";


        var stimuliHTML = null;



        if (stimulus.type == 'image' || stimulus.type == 'ImageStimulus') {
            console.log('local image');
            stimuliHTML = document.createElement('img');
            stimuliHTML.src = document.getElementById(stimulus.id).src; //stimuliAssocArray['data'];
            stimuliHTML.id = stimulus.localID; //stimuliAssocArray['id'];

        } else if (stimulus.type == "audio" || stimulus.type == 'AudioStimulus') {
            stimuli = document.createElement('audio');
            stimuli.controls = true;
            var source = document.createElement('source');
            source.src = "<?php echo BASE_URL; ?>" + "/" + stimuliAssocArray['url'];

            stimuli.id = stimuliAssocArray['id'];

            stimuli.appendChild(source);
        } else if (stimulus.type == "video") {
            throw new Error('editActivity.php/addStimulus: video');
        } else if (stimulus.type == "text") {
            stimuliHTML = document.createElement('div');
            stimuliHTML.innerHTML = stimulus.text;
            stimuliHTML.style.color = stimulus.fontColor;
            stimuliHTML.id = stimulus.localID;

        }



        stimuliHTML.classList.add('card-img-top');
        if (attr_descriptor != null) {

            cardButton.id = "select_stimuli_btn_" + stimuliHTML.id;

            cardButton.onclick = function() {
                var params = [];
                params['type'] = "stimulusID";
                instruction.setAttributeValue(attr_descriptor, stimuliHTML.id, params);
                //instruction.setAttributeValue(attr_descriptor,stimuli.id, params);
                closeModal();
            };
        } else {

            cardButton.onclick = function() {
                setContainerID(params['dest'], stimuliHTML.id);
                console.log("<<<<<<<<<<<<contID: " + stimulus.containerID);
            };

        }



        var localId = document.createElement("p");
        localId.innerHTML = "<b> Identificador: " + stimulus.getPosition() + "</b>";



        cardBody.appendChild(stimuliHTML);
        cardBody.appendChild(localId);
        cardBody.appendChild(cardButton);
        card.appendChild(cardBody);

        var d = document.getElementById(stimuliResultDivId);
        if (d != null) {
            d.appendChild(card);
        }
    }

    function addStimulus(stimuliAssocArray, instruction, attr_descriptor, local = false) {
        var stimuli = null;
        var params = [];

        var card = document.createElement('div');
        card.classList.add('card');
        //card.style.widtg = "18rem";

        var cardBody = document.createElement('div');
        cardBody.classList.add('card-body');

        var cardTitle = document.createElement('div');
        cardTitle.classList.add('card-title');
        cardTitle.innerHTML = stimuliAssocArray['name'];

        var cardText = document.createElement("div");
        cardText.classList.add('card-text');
        cardText.innerHTML = stimuliAssocArray['description'];

        var cardButton = document.createElement('button');
        cardButton.classList.add('btn', 'btn-primary');
        cardButton.innerHTML = "Selecionar";

        cardButton.id = "select_stimuli_btn_" + stimuliAssocArray['id'];
        console.log("id: " + cardButton.id);

        if (stimuliAssocArray['type'] == 'image') {
            stimuli = document.createElement('img');

            stimuli.src = stimuliAssocArray['data'];
            stimuli.id = stimuliAssocArray['id'];

        } else if (stimuliAssocArray['type'] == "audio") {
            stimuli = document.createElement('audio');
            stimuli.controls = true;
            var source = document.createElement('source');
            source.src = "<?php echo BASE_URL; ?>" + "/" + stimuliAssocArray['url'];

            stimuli.id = stimuliAssocArray['id'];

            stimuli.appendChild(source);
        } else if (stimuliAssocArray['type'] == "video") {
            throw new Error('editActivity.php/addStimulus: video');
        }

        stimuli.classList.add('card-img-top');


        cardButton.onclick = function() {
            console.log(instruction);
            if (typeof instruction == 'number')
                activity.instructions[instruction].setAttributeValue(attr_descriptor, stimuli.id, stimuliAssocArray);
            else
                instruction.setAttributeValue(attr_descriptor, stimuli.id, stimuliAssocArray);
            //instruction.setAttributeValue(attr_descriptor,stimuli.id, params);
            console.log('inst: ' + instruction + " id: " + stimuli.id);
            closeModal();
        };




        cardBody.appendChild(cardTitle);
        cardBody.appendChild(stimuli);
        cardBody.appendChild(cardText);
        cardBody.appendChild(cardButton);
        card.appendChild(cardBody);

        var d = document.getElementById(stimuliResultDivId);
        if (d != null) {
            d.appendChild(card);
        }


    }


    //verifyes the stimuli types.
    function editStimuli(instruction, attr_descriptor) {
        console.log("editStimuli()");
        var types = "";
        var i;
        for (i = 0; i < attr_descriptor.attributeTypes.length; i++) {
            types = types + attr_descriptor.attributeTypes[i];
            if (i < attr_descriptor.attributeTypes.length - 1)
                types = types + ',';
        }
        G_types = types;
        console.log("types: '" + types + "'");
        console.log()
        showSelectStimuli_createModal(types, instruction, attr_descriptor);

    }

    ///cria um modal com um checkbox.
    function editBoolean(instruction, attr_descriptor) {

        console.log('editBoolean()');

        var form = document.createElement('form');
        form.classList.add('form-inline', 'mx-auto');

        var checkDiv = document.createElement('div');
        checkDiv.classList.add('form-check');

        var checkBox = document.createElement('input');
        checkBox.classList.add('form-check-input');
        checkBox.id = 'chkBox';
        checkBox.type = 'checkbox';
        if (instruction[attr_descriptor.attributeName] == true) {
            checkBox.checked = true;
        }

        checkBox.onchange = function() {
            var params = [];
            params['type'] = 'boolean';
            instruction.setAttributeValue(attr_descriptor, this.checked, params);
        };

        var checkLabel = document.createElement('label');
        checkLabel.classList.add('form-check-label');
        checkLabel.for = 'chkBox';
        checkLabel.innerHTML = attr_descriptor.attributeDescription;

        checkDiv.appendChild(checkBox);
        checkDiv.appendChild(checkLabel);

        form.appendChild(checkDiv);

        showModal("Selecionar audio", form, null, false);

    }

    function editColor(instruction, attr_descriptor) {

        console.log('editColor()');
        console.log(attr_descriptor.selectValues);

        var form = document.createElement('form');
        form.classList.add('form-inline', 'mx-auto');

        var formGroup = document.createElement('div');
        formGroup.classList.add('form-group');

        var colorInput = document.createElement('input');
        colorInput.classList.add('form-control','input-lg');
        colorInput.style.width="60px";
        colorInput.type="color";
        colorInput.id = 'colorInput';
       

        colorInput.value = instruction[attr_descriptor.attributeName];

        var label = document.createElement('label');
        label.for = "colorInput";
        label.innerHTML = attr_descriptor.attributeDescription;

        var okButton = document.createElement('button');
        okButton.innerHTML = "OK";
        okButton.type = 'button';
        okButton.classList.add('btn', 'btn-primary');



        okButton.onclick = function() {
            var val = (document.getElementById('colorInput').value);
            var params = [];
            params['type'] = 'string';
            instruction.setAttributeValue(attr_descriptor, val, params);
            closeModal();
        };

        formGroup.appendChild(label);

        formGroup.appendChild(colorInput);

        form.appendChild(formGroup);
        form.appendChild(okButton);



        showModal("Selecionar " + attr_descriptor.attributeDescription, form, null, false);

    }

    /**
     * Abre um modal com um seletor dropdown.
     */
    function editSelection(instruction, attr_descriptor) {

        console.log('esditSelection()');
        console.log(attr_descriptor.selectValues);

        var form = document.createElement('form');
        form.classList.add('form-inline', 'mx-auto');

        var formGroup = document.createElement('div');
        formGroup.classList.add('form-group');

        var selectInput = document.createElement('select');
        selectInput.classList.add('form-control');
        selectInput.id = 'selectInput';
        for (var i = 0; i < attr_descriptor.selectValues.length; i++) {
            var val = attr_descriptor.selectValues[i];
            var opt = document.createElement('option');
            opt.value = val[0];
            opt.innerHTML = val[1]
            selectInput.appendChild(opt);
        }

        selectInput.value = instruction[attr_descriptor.attributeName];

        var label = document.createElement('label');
        label.for = "selectInput";
        label.innerHTML = attr_descriptor.attributeDescription;

        var okButton = document.createElement('button');
        okButton.innerHTML = "OK";
        okButton.type = 'button';
        okButton.classList.add('btn', 'btn-primary');



        okButton.onclick = function() {
            var val = (document.getElementById('selectInput').value);
            var params = [];
            params['type'] = 'string';
            instruction.setAttributeValue(attr_descriptor, val, params);
            closeModal();
        };

        formGroup.appendChild(label);
        formGroup.appendChild(selectInput);

        form.appendChild(formGroup);
        form.appendChild(okButton);



        showModal("Selecionar " + attr_descriptor.attributeDescription, form, null, false);

    }

    function editInteger(instruction, attr_descriptor) {

        console.log('editInteger()');

        var form = document.createElement('form');
        form.classList.add('form-inline', 'mx-auto');

        var formGroup = document.createElement('div');
        formGroup.classList.add('form-group');

        var numberInput = document.createElement('input');
        numberInput.classList.add('form-control');
        numberInput.type = "number";
        numberInput.id = 'numberInput';
        numberInput.value = instruction[attr_descriptor.attributeName];

        var label = document.createElement('label');
        label.for = "numberInput";
        label.innerHTML = attr_descriptor.attributeDescription;

        var okButton = document.createElement('button');
        okButton.innerHTML = "OK";
        okButton.type = 'button';
        okButton.classList.add('btn', 'btn-primary');



        okButton.onclick = function() {
            var val = parseInt(document.getElementById('numberInput').value);
            var params = [];
            params['type'] = 'integer';
            instruction.setAttributeValue(attr_descriptor, val, params);
            closeModal();
        };

        formGroup.appendChild(label);
        formGroup.appendChild(numberInput);

        form.appendChild(formGroup);
        form.appendChild(okButton);



        showModal("Selecionar audio", form, null, false);

    }


    function addNewText(instruction, attr_descriptor) {

        //throw new Error("add new text");
        var params = [];
        params['type'] = 'text';
        instruction.setAttributeValue(attr_descriptor, "Texto", params);
        closeModal();
    }


    function showEditText(instruction, attr_descriptor) {}

    function createNewStimuli(types) {

        var labels = [];
        labels['image'] = "Imagem";
        labels['audio'] = "Áudio";
        labels['video'] = "Vídeo";

        var all = genNewStimuliForm(types.split(','), labels);
        swapModalContent(all);
        modal_changeType();
    }

    function filter() {
        document.getElementById(stimuliResultDivId).innerHTML = "";
        var query = document.getElementById('search').value;



        showSelectStimuli(G_types, query, G_instruction, G_attr_descriptor);
    }


    /******************************************************************/
    /**
     * Realiza a busca ao apertar 'enter' no campo de busca.
     * @param {type} e
     * @returns {undefined}
     */
    function filterKeyUp(e) {
        var keynum;

        if (window.event) { // IE                    
            keynum = e.keyCode;
        } else if (e.which) { // Netscape/Firefox/Opera                   
            keynum = e.which;
        }
        if (keynum == 13)
            filter();
    }


    var config_form_id = "oiuioawgf";

    function configureStimulusCallback_submit(stimulus, inst) {

        if (stimulus.type == 'text') {
            stimulus.text = document.forms[config_form_id]["text-value"].value;
            stimulus.fontSize = parseInt(document.forms[config_form_id]["text-fontSize"].value);
            console.log("font siz " + stimulus.fontSize)
            stimulus.fontColor = document.forms[config_form_id]["text-color"].value;
        } else if (stimulus.type == 'image') {
            throw new Error('image.');
        } else if (stimulus.type == 'audio') {
            throw new Error('audio.');
        } else if (stimulus.type == 'video') {
            var url = document.forms[config_form_id]["video-url"].value;
            if (!url.includes("youtube.com", 0) && !url.includes("youtu.be", 0)) {
                alert("Insira uma URL do youtube válida! (copie o endereço do vídeo)");
                return;
            }
            stimulus.setURL(url);
        }


        closeModal();
    }

    function setContainerID(stimulus, id) {

        stimulus.containerID = id;
        console.log(stimulus);
        closeModal();
    }

    function showSelecStimuliContainer(stimulus, obj_array, instruction) {

        var i;
        console.log(obj_array);
        for (i = 0; i < obj_array.length; i++) {

            var obj = obj_array[i];
            console.log("***********************************************************");
            console.log(obj.localID);
            var f = function() {
                console.log("=-=-=-===-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=");
                console.log('localID: ' + obj.localID);
                //    setContainerID(stimulus, obj);

            };

            if (!obj.fullContainer) {


                addLocalStimulus(obj, instruction, null, {
                    'dest': stimulus
                });
            }

        }
    }

    function containerConfigureFull(stimulus) {
        console.log('editBoolean()');

        var form = document.createElement('form');
        form.classList.add('form-inline', 'mx-auto');

        var checkDiv = document.createElement('div');
        checkDiv.classList.add('form-check');

        var checkBox = document.createElement('input');
        checkBox.classList.add('form-check-input');
        checkBox.id = 'chkBox';
        checkBox.type = 'checkbox';
        if (stimulus.fullContainer) {
            checkBox.checked = true;
        }

        checkBox.onchange = function() {
            stimulus.fullContainer = this.checked;
        };

        var checkLabel = document.createElement('label');
        checkLabel.classList.add('form-check-label');
        checkLabel.for = 'chkBox';
        checkLabel.innerHTML = "Cheio? (não permite adicionar estímulos)";

        checkDiv.appendChild(checkBox);
        checkDiv.appendChild(checkLabel);

        form.appendChild(checkDiv);

        showModal("Contener cheio? (não pode adicionar estímulos)", form, null, false);
    }

    //Callback chamada ao clicar na configuração do estímulo (engrenagem)
    function configureStimulusCallback(stimulus, inst) {


        var content = "Sem atributos para editar.";
        ///Get form..
        console.log("TYPE: " + stimulus.type);
        if (stimulus.hasOwnProperty('emotionDescriptor')) {
            //TODO: selecioanr emoção aqui 
            showSelectEmotion(stimulus);
            return;

        } else if (stimulus.type == "text") {
            content = document.getElementById('imagePropsTemplate').cloneNode(true);
        } else if (stimulus.type == "video") {
            content = document.getElementById('videoPropsTemplate').cloneNode(true);
        } else if (stimulus.type == 'image') {
            if (stimulus.dragAndAssociate == true) {
                var container = createSelectStimuliContainer('image', stimulus.instruction, null, false, false);
                showModal("Selecione o conteiner correto para o estimulo", container, null, false);
                showSelecStimuliContainer(stimulus, stimulus.instruction.positions, stimulus.instruction);
                return;
            } else if (stimulus.isContainer) {

                containerConfigureFull(stimulus);
                return;
            }

            showModal("Configurar Estímulo", "Sem atributos para editar.");
        }
        content.hidden = false;
        content.id = config_form_id;

        showModal("Configurar Estímulus", content, function() {
            configureStimulusCallback_submit(stimulus, inst);
        }, true);
        if (stimulus.type == 'text') {
            document.forms[config_form_id]["text-value"].value = stimulus.text;
            document.forms[config_form_id]["text-fontSize"].value = stimulus.fontSize;
            document.forms[config_form_id]["text-color"].value = stimulus.fontColor;
        }


    }

    function removeStimulusCallback_submit(stimulus, inst) {
        console.log(stimulus);
        console.log("Remove stimuli " + stimulus);
        console.log(inst);
        inst.removeStimuli(stimulus.localID, 'image');
        closeModal();
    }

    function removeStimulusCallback(stimulus, inst) {
        showModal("Remover Estímulo", "Deseja remover o estímulo?", function() {
            removeStimulusCallback_submit(stimulus, inst);
        }, true);
    }

    function showSelectEmotion(stimulus) {
        var content = document.getElementById('emotionDescriptorTemplate').cloneNode(true);
        content.classList.remove("d-none");
        if (stimulus.emotionDescriptor.length > 0)
            content.querySelector("#emotionDescriptorValue").value = stimulus.emotionDescriptor;
        showModal("Selecione a emoção que o estímulo representa", content, function() {
            var val = content.querySelector("#emotionDescriptorValue").value;

            stimulus.emotionDescriptor = val;
            closeModal();
        }, true);
    }
</script>

<form id='emotionDescriptorTemplate' class="d-none">
    <div class='form-row'>
        <div class="form-group col-md-8">
            <select class="form-control" id="emotionDescriptorValue" name='emotionDescriptorValue'>
                <option value="hapiness">Felicidade</option>
                <option value="sadness">Tristeza</option>
                <option value="anger"> Raiva</option>
                <option value="surprise">Surpresa</option>
                <option value="fear">Medo</option>
                <option value="disgust">Desgosto</option>
            </select>
        </div>
    </div>
</form>



<form id='videoPropsTemplate' hidden>
    <div class='form-row'>
        <div class="form-group col-md-8">
            <label for="video-url">Endereço do vídeo:</label>
            <input type="text" class="form-control" id="video-url" name='video-url'>
        </div>
    </div>
</form>

<form id='imagePropsTemplate' hidden>
    <div class='form-row'>
        <div class="form-group col-md-8">
            <label for="text-value">Texto</label>
            <input type="text" class="form-control" id="text-value" name='text-value'>
        </div>
        <div class="form-group col-md-2">
            <label for="text-fontSize">Tamanho da fonte</label>
            <input type="number" min='1' class="form-control" id="text-fontSize" name='text-fontSize'>
        </div>
        <div class="form-group col-md-2">
            <label for="text-color">Cor da fonte</label>
            <input type="color" class="form-control" id="text-color" name='text-color'>
        </div>
    </div>
</form>


<img hidden id="_playerEndPosition" src="<?php echo BASE_URL . "/ui/finish-306245_1280.png"; ?>">
<img hidden id="_emotionPosition" src="<?php echo BASE_URL . "/ui/emoji-2074153_1280.png"; ?>">
<img hidden id="_playerStartPosition" src="<?php echo BASE_URL . "/ui/1024px-OOjs_UI_icon_userAvatarOutline-progressive.svg.png"; ?>">
<img hidden id="_camFrame" src="<?php echo BASE_URL . "/ui/camera-2008479_640.png"; ?>">
<img hidden id="_gearIcon" src="<?php echo BASE_URL . "/ui/icons8-engrenagem-64.png"; ?>">
<img hidden id="_audioIcon" src="<?php echo BASE_URL . "/ui/audioPlayIcon.png"; ?>">
<img hidden id="_notFoundIcon" src="<?php echo BASE_URL . "/ui/Image-Not-Found1.png"; ?>">
<img hidden id="_textFrame" src="<?php echo BASE_URL . "/ui/textFrame.png"; ?>">
<img hidden id="_finishButton" src="<?php echo BASE_URL . "/ui/check-24849_640.png"; ?>">

<img hidden id="_correctButton" src="<?php echo BASE_URL . "/ui/correct-button-png-9.png"; ?>">
<img hidden id="_correctWithTipButton" src="<?php echo BASE_URL . "/ui/5dcd519269d67.png"; ?>">
<img hidden id="_wrongButton" src="<?php echo BASE_URL . "/ui/error-24842_640.png"; ?>">
<!-- busca -->
<div class="row mt-4 d-none" id="filterFormTemplate">
    <div class="col">
        <div class="form-row">
            <div class="form-group col-lg-11">
                <input onkeyup="filterKeyUp(event)" class="form-control mr-sm-2" id="search" name="query" type="query" placeholder="Filtrar " aria-label="Search" value="">
            </div>
            <div class="form-group col-lg-1">
                <!--<button type="button" onclick="filter(event)" class="btn btn-outline-success form-control" >Filtrar </button>-->
                <button type="button" class="btn btn-outline-success form-control" onclick="filter(event)">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- busca -->


<!-- editor -->
<div id="edit" class="row d-none">
    <div id='buttonContent' class='bg-light row h-100 w-100 border-bottom m-0 p-0 border-dark'>
        <div style="font-size: 24pt;" id="editInstructionName" class="col-lg-11 font-weight-bold">

        </div>
        <div class='col-lg font-weight-bold'>
            <button id="closeEditor" type="button" class="close" aria-label="Close">
                <span style="font-size: 36pt;" onclick="hideEdit()" aria-hidden="true" class="text-danger">&times;</span>
            </button>
        </div>
    </div>



    <div id="content" class="row p-0 h-100 w-100 p-3">

        <div id="canvasPanel" class="m-0 p-0 col-lg-8 h-100 w-100 ">
            <!--<canvas id="drawCanvas" width="800px" height="600px" style="border:1px solid #000000;">-->
            <canvas class="m-0 p-0" id="drawCanvas" width="800" height="600" style="width:800px; height:600px; border:1px solid #000000;">
            </canvas>
        </div>



        <div id='controls' class="col-lg-4">

        </div>
    </div>
</div>
<!-- editor -->

<!-- add new stimuli -->