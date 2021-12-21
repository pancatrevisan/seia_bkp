
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

    var programId="<?php echo $data['programId'];?>";
    var G_instruction = -1;
    
    var G_attr_descriptor = null;
    
    
    var G_types = "images";
    var G_model = false;

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
        return;
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

                    activity = new Activity(id, this.responseText, document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");

                    waitForLoad();
                }
            }
        };

        xhttp.open("GET", "<?php echo BASE_URL; ?>/activity/index.php?action=getActivity&id=" + id, true);
        xhttp.send();
    }


</script>



<div id="paperModel" class="paper" hidden>
    <div class="paper-content">
        <textarea autofocus></textarea>
    </div>
</div>

<div style="display: none;" id='runActivityData'></div>    

<div class="row mt-3 " id="activityInfo">
    <div class="col alert alert-primary">
        <div class="row   ">

            <div class="col">
                <button type="button" class="btn btn-info btn-lg btn-block" onclick="showSelectStimuli_createModal(true);">Selecionar Estímulo Modelo</button> 
            </div>
            <div class="col">
                <button type="button" class="btn btn-info btn-lg btn-block" onclick="showSelectStimuli_createModal(false)">Selecionar Estímulos de Comparação</button> 
            </div>
        </div>

        <div class="row mt-2">
            
            
                
                    <div class="col input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Número de estímulos de comparação</div>
                        </div>
                        <input class="form-control" min="2" max="4" value="2"type='number' id="numberOfStimuli" name='numberOfStimuli' >
                    </div>
                
                    <div class="col input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Número de atividades</div>
                        </div>
                        <input class="form-control" min="1" max="15" value="1"type='number' id="numberOfActivity" name='numberOfActivity' >
                    </div>
                
            
            
            
            
            
            
        </div>
        <div class="row">
        <div class="col"> 
                <button type="button" class="btn btn-success btn-lg btn-block" onclick="generateActivities()">Gerar programa de ensino</button> 
            </div>
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


<script>

    function saveActivity(dest) {        
        
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

    function createSelectStimuliContainer(types, showMore = true, showNew = true) {
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
    function showSelectStimuli_createModal(model=false) {
        G_model = model;
        var types = 'image';
        var container = createSelectStimuliContainer(types, true, true);
        showModal("Selecionar Estímulo", container, null, false);
        showSelectStimuli(types,"",model);
        
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

    function showSelectStimuli(types, query, model=false) {
        var xhttp = new XMLHttpRequest();
        G_types = types;
        G_model = model;
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
                        showSelectStimuli(types, query, model);
                    };

                    //present stimuli
                    for (key in objs['results'])
                    {
                        var obj = objs['results'][key];
                        addStimulus(obj,model);
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

    function addStimulus(stimuliAssocArray, model=false) {
        var stimuli = null;
        
        console.log(stimuliAssocArray);

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
        cardButton.id = "butt"+stimuli.id;
        
        stimuli.classList.add('card-img-top');


        cardButton.onclick = function () {
            ///
            ///
            //AQUI POHAN
            
            var c = card.cloneNode(true);
            var but = c.querySelector("#butt"+stimuli.id);
            but.innerHTML = "Remover";
            c.id="selStimuli"+stimuli.id;
            c.setAttribute('data-type','stimuli');
            c.setAttribute('data-dbID',stimuli.id);
            
            but.onclick = function(){
                var s = document.getElementById(c.id);
                s.parentNode.removeChild(s);
            };
            
            if(model){
                console.log("estimulo selecionado; modelo;");
                document.getElementById('modelStimulus').innerHTML="";
                document.getElementById('modelStimulus').appendChild(c);// = card;
                closeModal();
            }
            else{
                console.log("Comaparação");
                document.getElementById('selectedStimuli').appendChild(c);// = card;
                
            }
        
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
        
        console.log('buscar');

        showSelectStimuli(G_types, query, G_model);
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
            var f = function () {
                console.log("=-=-=-===-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=");
                console.log('localID: ' + obj.localID);
                //    setContainerID(stimulus, obj);

            };

            addLocalStimulus(obj, instruction, null, {'dest': stimulus});


        }
    }


    function configureStimulusCallback(stimulus, inst) {

        console.log("type? " + stimulus.type + "  " + stimulus.dragAndAssociate);
        var content = "null";
        ///Get form..
        if (stimulus.type == "text") {
            content = document.getElementById('imagePropsTemplate').cloneNode(true);
        } else if (stimulus.type == 'image') {
            if (stimulus.dragAndAssociate == true) {
                console.log("era para mostrar :?:?:?");

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
        console.log(stimulus);
        console.log("Remove stimuli " + stimulus);
        console.log(inst);
        inst.removeStimuli(stimulus.localID, 'image');
        closeModal();
    }
    function removeStimulusCallback(stimulus, inst)
    {
        showModal("Remover Estímulo", "Deseja remover o estímulo?", function () {
            removeStimulusCallback_submit(stimulus, inst);}, true);
    }


</script> 



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
<div id="modelStimulus" class="alert alert-info card-columns">
    
</div>

<div id="selectedStimuli" class="alert alert-warning card-columns">
    
</div>

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

<div id="previewAutoTemplate"class="d-none">
    <p>Atividades Criadas!</p>
    <button id="testAuto" type="button" class="btn btn-info btn-lg btn-block" ><i class="fas fa-play"></i></button>            
</div>


<script>
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
    function generateActivities(){
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if(this.responseText == "OK"){
                    generateActivities_2();
                }
                
            }
        };

        xhttp.open("GET", "<?php echo BASE_URL; ?>/program/index.php?action=resetProgram&programId="+programId+"&json=true", true);
        xhttp.send();
    }
    function generateActivities_2(){
        var modelContainer = document.getElementById('modelStimulus');
        
        
        if(countSons(modelContainer) <=0)
        {
            alert("Selecione o estímulo modelo!");
            return;
        }
        
        var compareContainer = document.getElementById('selectedStimuli');
        var numberOfStimuli = document.getElementById('numberOfStimuli').value;
        var numNeededStimuli =  numberOfStimuli - 1;
        if(countSons(compareContainer)<numNeededStimuli)
        {
            
            alert("Você deve selcionar no mínimo " + numNeededStimuli +" estímulos");
        }
        var numberOfActivities =document.getElementById('numberOfActivity').value;
        
        console.log(activity);
        var modelPosition = [];
        
        var positions = [];
        var size = [200,150];
        var modelOnTop=true;
        if(modelOnTop){
            if(numberOfStimuli == 2){
                modelPosition=[300,124];
                positions.push([105,362]);
                positions.push([485,362]);
            }
            else if(numberOfStimuli == 3){
                modelPosition=[300,124];
                positions.push([62,382]);
                positions.push([303,382]);
                positions.push([562,382]);
            }
            else if(numberOfStimuli==4){
                modelPosition = [299,44];
                positions.push([22,227]);
                positions.push([164,415]);
                positions.push([437,415]);
                positions.push([569,227]);
            }
        }
        else{
            modelPosition = [300,600];
        }
        
        
        //id do modelo...
        var model_database_id = "";
        
        for(var el =0; el <document.getElementById('modelStimulus').childNodes.length;el++){
                var tmp = document.getElementById('modelStimulus').childNodes[el];
                if(tmp.nodeType != Node.TEXT_NODE)
                {
                    if(tmp.getAttribute('data-type')=='stimuli'){
                        model_database_id = tmp.getAttribute('data-dbID');
                    }
                }
        }
        
        console.log(model_database_id);
        //ids dos estimulos de comparação
        var compare_database_ids = [];
        for(var el =0; el <document.getElementById('selectedStimuli').childNodes.length;el++){
                var tmp = document.getElementById('selectedStimuli').childNodes[el];
                if(tmp.nodeType != Node.TEXT_NODE)
                {
                    if(tmp.getAttribute('data-type')=='stimuli'){
                        compare_database_ids.push(tmp.getAttribute('data-dbID'));
                    }
                }
        }
        console.log(compare_database_ids);
        
        for(var i=0;i<numberOfActivities;i++){
            console.log("*****************************************************************");
            var sIds = [];
            var selImage = activity.instructions[0];
            for(var j = 0; j < selImage.stimulis.length;j++){
                sIds.push(selImage.stimulis[j].localID);
            }
            
            //remove as imagens
            for(var j = 0; j <sIds.length;j++){
                selImage.removeStimuli(sIds[j], 'image');
            }
            
            //array clone
            var _pos =[];
            for(var k=0; k<positions.length;k++){
                _pos.push([positions[k][0],positions[k][1]]);
            }
            console.log('pos: ');
            console.log(_pos);
            console.log(selImage);
            //descritores dos atributos  da instruao de selecao
            var model_attr_desc = selImage.getAttributeDescriptor('model'); //image
            var images_attr_desc = selImage.getAttributeDescriptor('images');//image
            var expected_attr_desc = selImage.getAttributeDescriptor('expectedImage');//stimulusID
            console.log(model_attr_desc);
            console.log(images_attr_desc);
            console.log(expected_attr_desc);
            
            
            //adiciona o modelo
            var model_ = 
                selImage.setAttributeValue(model_attr_desc, model_database_id, {type: 'image'});
            
            
            model_.renderImage.position = [modelPosition[0],modelPosition[1]];
            model_.renderImage.size = size;
            
            
            //adiciona o modelo como estimulo de comparacao
            var modelStimuli = 
                selImage.setAttributeValue(images_attr_desc, model_database_id, {type: 'image'});
            
            var rand_pos = Math.floor(Math.random()*_pos.length);
            console.log('r pos: '+rand_pos);
            var r_pos = [_pos[rand_pos][0],_pos[rand_pos][1]];
            _pos.splice(rand_pos,1);
             console.log('pos: ');
            console.log(_pos);
            modelStimuli.renderImage.position = [r_pos[0],r_pos[1]];
            modelStimuli.renderImage.size = size;
               
            //define o esperado como o modelo (id local)...
            console.log(modelStimuli);
            selImage.setAttributeValue(expected_attr_desc, modelStimuli.localID, {type: 'stimulusID'});
            
            
            var tmp_compare = [];
            for(var h = 0; h < compare_database_ids.length; h++){
                tmp_compare.push(compare_database_ids[h]);
            }
            
            //adiciona estimulos de comparaçao
            while(_pos.length>0){
                console.log("add comp stimuli...");
                var rand_s = Math.floor(Math.random()*tmp_compare.length);
                
                var compStimuli =tmp_compare[rand_s];
                
                tmp_compare.splice(rand_s,1);
                
                rand_pos = Math.floor(Math.random()*_pos.length);
                r_pos = [_pos[rand_pos][0],_pos[rand_pos][1]];
                _pos.splice(rand_pos,1);
                
                var newStimuli = selImage.setAttributeValue(images_attr_desc, compStimuli, {type: 'image'});
                newStimuli.renderImage.position =[r_pos[0],r_pos[1]];
                newStimuli.renderImage.size = size;
                 console.log('pos: ');
            console.log(_pos);
            }
            
            activity.saveActivity('auto',programId);
            console.log(selImage);
        }
        
        var content = document.getElementById('previewAutoTemplate').cloneNode(true);
        content.id = "watah";
        content.classList.remove('d-none');
        var testButton =content.querySelector('#testAuto');
        testButton.onclick=function(){
            window.location.href = "<?php echo BASE_URL;?>/program/index.php?action=run&type=group&preview=preview&groupId="+programId;
        };
        showModal("",content);
        
        
    }
    document.body.onload = function () {
        console.log("on load body");
        load('<?php echo $data['activity_id'] ?>');
    }
</script>