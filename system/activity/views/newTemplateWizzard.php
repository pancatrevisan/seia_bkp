<?php
$sel_lang = 'ptb';
if (!defined('ROOTPATH')) {
    require '../root.php';
}
?>


<script  src="<?php echo BASE_URL;?>/external/face-api.js"></script>
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

$insts = [];
foreach ($data['instructions'] as $instruction) {
    echo '<script type="text/javascript" src="' . $instruction . '"></script>';
}
require ROOTPATH . "/ui/modal.php";
?>

<?php require_once 'newStimuliModal.php'; ?>




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


        if (max_h > w_h)
        {
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
                for (i = 0; i < activity.instructions.length; i++)
                {
                    var x;

                    var inst = activity.instructions[i];
                    if (inst.editable) {

                        var card =
                                '<div class="card border-dark m-3 " id="editor_instruction'+ inst.position +'"> ' +
                                //'<img class="card-img-top" src="..." alt="Card image cap">'+
                                '<h5 class="card-header">' + inst.description + '</h5>' +
                                '<div class="card-body">' +
                                //'<h5 class="card-text">' + inst.description + '</h5>' +
                                '<a href="javascript:showEditor(' + inst.position + ')" class="btn btn-outline-info btn-lg m-3">' + "Editar" + '</a>' +
                                '<a href="javascript:askToRemove(' + inst.position + ')" class="btn btn-outline-danger btn-lg m-3">' + "Remover" + '</a>' +
                                '</div>' +
                                '</div>';
                        document.getElementById('editableInstructions').innerHTML += card;
                    }

                }

                animate = window.setInterval(update, 66);
            } else {
                document.getElementById('editableInstructions').innerHTML = "Carregando dados...";


                setTimeout(function () {
                    waitForLoad();
                }, 1000);
            }

        } else {

            setTimeout(function () {
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
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {

                if (this.responseText.length <= 0)
                {
                    alert("Erro carregando atividade. Contate o administrador");
                } else {
                    var canvas = document.getElementById('drawCanvas');
                    console.log(this.responseText);
                    activity = new Activity(id, this.responseText, document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");

                    waitForLoad();
                }
            }
        };

        xhttp.open("GET", "<?php echo BASE_URL; ?>/activity/index.php?action=getActivity&id=" + id, true);
        xhttp.send();
    }
    document.body.onload = function () {
        console.log("on load body");
        load('<?php echo $data['templateId'] ?>');
        window.addEventListener("resize", resize);
        resize();
        var canvas = document.getElementById('drawCanvas');
        canvas.addEventListener("mousedown", function (evt) {
            activity.pointerDown(evt);
        });
        canvas.addEventListener("mousemove", function (evt) {
            activity.pointerMove(evt);
        });
        canvas.addEventListener("mouseup", function (evt) {
            activity.pointerUp(evt);
        });

        canvas.addEventListener("touchstart", function (evt) {
            activity.pointerDown(evt);
        });
        canvas.addEventListener("touchmove", function (evt) {
            activity.pointerDown(evt);});
        canvas.addEventListener("touchend", function (evt) {
            activity.pointerUp(evt);});
        canvas.addEventListener("touchcancel", function (evt) {
            activity.pointerUp(evt);});

        if (activity != null)
            activity.resize();
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
                <button type="button" class="btn btn-info btn-lg btn-block" onclick="window.location = 'index.php';">Voltar</button> 
            </div>
            <div class="col">
                <button type="button" class="btn btn-info btn-lg btn-block" onclick="saveActivity('preview')">Pré-visualizar</button> 
            </div>
            <div class="col">
                <button type="button" class="btn btn-info btn-lg btn-block" onclick="saveActivity('save')">Guardar alterações</button> 
            </div>
            
            <div class="col">
                <button type="button" class="btn btn-info btn-lg btn-block" onclick="selectInstruction()">Adicionar Tela</button> 
            </div>
             
        </div>
        
        <div class="row alert alert-primary m-2">  <div class="col-sm-2"> Nome da Atividade: </div> 
            <div class="col-sm-10" ><input class="form-control" type='text' id="activityName" name='activityName' value="<?php echo $data['activity_name']; ?>"></div>
        </div>
    </div>
</div>

<div class="row mt-3" id="editableInstructionsRow">
    <div class="col" class="w-100">
        <!--<div id="editableInstructions" class="card-columns "> -->
        <div id="editableInstructions" class="mx-auto w-100">

        </div>
    </div>
</div>
<div class="d-none" id='instructionsTemplate'>

</div>

<script>
     function saveActivity(dest) {

if (activity != null) {
    var data = [];
    
    
    var name = "" + document.getElementById('activityName').value;
    
    data['name'] = name; //document.getElementById('activityName').value;
    
    data['difficulty'] = "NOT_RATED";//document.getElementById('activityDifficulty').value;
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
    activity.saveActivity(dest,"",str_json);
    

    //xhr.open("POST", '<?php echo BASE_URL; ?>/activity/index.php?action=updateMetadata', true);
    //xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
    //xhr.send("metadata=" + str_json);



} else {
    alert("Erro salvando atividade. Contate o administrador");
}
}
   /* function saveActivity(dest) {        
        

        if (activity != null) {
            var data = [];
            data['name'] = document.getElementById('activityName').value;
            //data['antecedent'] = document.getElementById('antecedent').value;
            //data['behavior'] = document.getElementById('behavior').value;
            //data['consequence'] = document.getElementById('consequence').value;
            data['id'] = activity.id;
            var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                if (this.readyState != 4)
                    return;

                if (this.status == 200) {
                    var ret = this.responseText;
                    console.log(ret);
                    activity.saveActivity(dest);
                }
            };
            var str_json = JSON.stringify(Object.assign({}, data));
            console.log("send meta...");
            xhr.open("POST", '<?php echo BASE_URL;?>/activity/index.php?action=updateMetadata', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            xhr.send("metadata=" + str_json);


            
        } else {
            alert("Erro salvando atividade. Contate o administrador");
        }
    }*/
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
    function addEditButton(inst, attr_descriptor) {//, attrType,edit, description, val){                
        var button = document.createElement('button');
        button.classList.add('btn', 'btn-primary', 'btn-lg', 'btn-block');
        button.type = "button";
        button.innerHTML = attr_descriptor.attributeDescription;

        var buttonCol = document.createElement('div');
        buttonCol.classList.add('col');
        buttonCol.appendChild(button);

        var buttonRow = document.createElement('div');
        buttonRow.classList.add('row');

        button.onclick = function () {
            this.blur();
            console.log("clicou o botão para editar atributo (addEditButton)");
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
            newStimulusButton.onclick = function () {
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
            newTextButton.onclick = function () {
                addNewText(instruction, attr_descriptor);
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
        xhttp.onreadystatechange = function () {

            if (this.readyState == 4 && this.status == 200) {

                if (this.responseText.length <= 0)
                {
                    console.log("lascou-se");
                } else {
                    document.getElementById(showMoreButtonId).disabled = false;
                    if (this.responseText == "STIMULI_NOT_FOUND")
                    {
                        console.log("nao existe");
                        document.getElementById(showMoreButtonId).disabled = true;
                        return;
                    }

                    var objs = JSON.parse(this.responseText, true);
                    if (objs['results'].length <= 0)
                        document.getElementById(showMoreButtonId).disabled = true;


                    ///configures the 'show more results' button.
                    document.getElementById(showMoreButtonId).onclick = function () {
                        showSelectStimuli(types, query, instruction, attr_descriptor);
                    };

                    //present stimuli
                    for (key in objs['results'])
                    {
                        var obj = objs['results'][key];
                        addStimulus(obj, instruction, attr_descriptor);
                    }
                }
            }
        };

        var d = document.getElementById(stimuliResultDivId); // where the stimulis go
        var offset = countSons(d);

        xhttp.open("GET", "../stimuli/index.php?action=get_as_json&types=" + types + "&query=" + query + "&offset=" + offset, true);
        xhttp.send();
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
            cardButton.onclick = function () {
                var params = [];
                params['type'] = "stimulusID";
                instruction.setAttributeValue(attr_descriptor, stimuliHTML.id, params);
                //instruction.setAttributeValue(attr_descriptor,stimuli.id, params);
                closeModal();
            };
        } else
        {

            cardButton.onclick = function () {
                setContainerID(params['dest'], stimuliHTML.id);
                console.log("<<<<<<<<<<<<contID: " + stimulus.containerID);
            };

        }





        cardBody.appendChild(stimuliHTML);
        cardBody.appendChild(cardButton);
        card.appendChild(cardBody);

        var d = document.getElementById(stimuliResultDivId);
        if (d != null)
        {
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


        cardButton.onclick = function () {
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
        if (d != null)
        {
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

        checkBox.onchange = function () {
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



        okButton.onclick = function () {
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


    function showEditText(instruction, attr_descriptor) {
    }

    function createNewStimuli(types) {

        var labels = [];
        labels['image'] = "Imagem";
        labels['audio'] = "Áudio";
        labels['video'] = "Vídeo";

        var all = genNewStimuliForm(types.split(','), labels);
        swapModalContent(all);
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
            throw new Error('audio.');
        } else if (stimulus.type == 'audio') {
            throw new Error('audio.');
        } else if (stimulus.type == 'video') {
            throw new Error('video.');
        }


        closeModal();
    }

    function setContainerID(stimulus, id) {

        stimulus.containerID = id;
        
        closeModal();
    }
    function showSelecStimuliContainer(stimulus, obj_array, instruction) {

        var i;
        
        for (i = 0; i < obj_array.length; i++) {
            var obj = obj_array[i];
            addLocalStimulus(obj, instruction, null, {'dest': stimulus});
        }
    }


    function configureStimulusCallback(stimulus, inst) {

        var content = "null";
        ///Get form..
        if (stimulus.type == "text") {
            content = document.getElementById('imagePropsTemplate').cloneNode(true);
        } else if (stimulus.type == 'image') {
            if (stimulus.dragAndAssociate == true) {
                var container = createSelectStimuliContainer('image', stimulus.instruction, null, false, false);
                showModal("Selecione o conteiner correto para o estimulo", container, null, false);
                showSelecStimuliContainer(stimulus, stimulus.instruction.positions, stimulus.instruction);
                return;
            }
        }
        content.hidden = false;
        content.id = config_form_id;

        showModal("Configurar Estímulus", content, function () {
            configureStimulusCallback_submit(stimulus, inst);}, true);
        if (stimulus.type == 'text') {
            document.forms[config_form_id]["text-value"].value = stimulus.text;
            document.forms[config_form_id]["text-fontSize"].value = stimulus.fontSize;
            document.forms[config_form_id]["text-color"].value = stimulus.fontColor;
        }


    }
    function removeStimulusCallback_submit(stimulus, inst) {
        inst.removeStimuli(stimulus.localID, 'image');
        closeModal();
    }
    function removeStimulusCallback(stimulus, inst)
    {
        showModal("Remover Estímulo", "Deseja remover o estímulo?", function () {
            removeStimulusCallback_submit(stimulus, inst);}, true);
    }


</script> 
<div id="availableInstructionsTemplate" class='d-none'>

</div>


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
        <div style="font-size: 24pt;"  id="editInstructionName" class="col-lg-11 font-weight-bold">

        </div>
        <div class='col-lg font-weight-bold'>
            <button type="button" class="close" aria-label="Close">
                <span style="font-size: 36pt;" onclick="hideEdit()" aria-hidden="true" class="text-danger">&times;</span>
            </button>
        </div>
    </div>



    <div id="content" class="row p-0 h-100 w-100 p-3" >

        <div id="canvasPanel" class="m-0 p-0 col-lg-8 h-100 w-100 "  >            
            <!--<canvas id="drawCanvas" width="800px" height="600px" style="border:1px solid #000000;">-->
            <canvas class="m-0 p-0" id="drawCanvas" width="800" height="600"  style="border:1px solid #000000;">
            </canvas>    
        </div>



        <div id='controls' class="col-lg-4">

        </div>
    </div>
</div>
<!-- editor -->

<script>
    //wizard editor

    function askToRemove(position){
        showModal("Deseja Remover? Isso não pode ser desfeito","Deseja Remover? Isso não pode ser desfeito",function(){
            removeInstruction(position);
            closeModal();
        })
    }

    function removeInstruction(position){

        var id = "editor_instruction"+position;
        
        var inst = document.getElementById(id);
        inst.parentElement.removeChild(inst);
        delete activity.instructions.splice(position,1);//[position];
        console.log(activity.instructions);
        genCards();
    }


    function genCards(){
        document.getElementById('editableInstructions').innerHTML = "";
        activity.recomputeIndexes();
        for(var i = 0; i<activity.instructions.length; i++){
            var instruction  = activity.instructions[i];
            var card =
                '<div class="card border-dark m-3 " id="editor_instruction'+ instruction.position +'"> ' +
                //'<img class="card-img-top" src="..." alt="Card image cap">'+
                '<h5 class="card-header">' + instruction.description + '</h5>' +
                '<div class="card-body ">' +
                //'<h5 class="card-text">' + instruction.description + '</h5>' +
                '<a href="javascript:showEditor(' + instruction.position + ')" class="btn btn-outline-info btn-lg m-3 ">' + "Editar" + '</a>' +
                '<a href="javascript:askToRemove(' + instruction.position + ')" class="btn btn-outline-danger btn-lg m-3">' + "Remover" + '</a>' +
                '</div>' +
                '</div>';
        document.getElementById('editableInstructions').innerHTML += card;
        
        }
    }

    function addInstruction(name){
        if(activity == null){
            activity = new Activity("<?php echo $data['templateId'];?>",null,document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");
        }
        
        var instruction = eval('new '+name+"()");
        instruction.position = activity.instructions.length;
        
        activity.instructions.push(instruction);
        instruction.activity = activity;
        
        /*var card =
                '<div class="card border-dark m-3 " id="editor_instruction'+ instruction.position +'"> ' +
                //'<img class="card-img-top" src="..." alt="Card image cap">'+
                '<h5 class="card-header">' + instruction.description + '</h5>' +
                '<div class="card-body ">' +
                //'<h5 class="card-text">' + instruction.description + '</h5>' +
                '<a href="javascript:showEditor(' + instruction.position + ')" class="btn btn-outline-info btn-lg m-3 ">' + "Editar" + '</a>' +
                '<a href="javascript:askToRemove(' + instruction.position + ')" class="btn btn-outline-danger btn-lg m-3">' + "Remover" + '</a>' +
                '</div>' +
                '</div>';
        document.getElementById('editableInstructions').innerHTML += card;*/
        genCards();
        closeModal();
    }
    
    function selectInstruction(){
        var instructions = "<?php echo implode(',',$data['instructions']); ?>";
        instructions = instructions.split(',');
        var content = document.getElementById('availableInstructionsTemplate').cloneNode(true);
        content.classList.remove('d-none');
        content.id = "availableInstructions";
        
        var i;
        for (i =0; i < instructions.length; i++)
        {
            instructions[i] = instructions[i].substring(13, instructions[i].length);
            instructions[i] = instructions[i].substring(0, instructions[i].length-3);
            var tmp = eval('new '+instructions[i]+"()");
            
            if(tmp.allowUse){

            
                

                var button = document.createElement('button');
                button.classList.add('btn','btn-lg','btn-primary','btn-block');
                button.innerHTML = tmp.description;
                button.type = "button";
                button.setAttribute('data-instruction',instructions[i]);
                button.onclick = function () {
                    
                    addInstruction(""+this.getAttribute('data-instruction'));
                    
                };

                content.appendChild(button);
            }
        }


        showModal("Selecionar Tipo de Tela", content, null, false);
    }

    

    
</script>
