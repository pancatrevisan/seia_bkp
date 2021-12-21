
<?php
$sel_lang = "ptb";
require ROOTPATH . '/lang/' . $sel_lang . "/stimuli/newStimuli.php";
$name_val = "";
$desc_val = "";
$cat_val = "";
$type = "";
if (isset($data['stimuli_name']))
    $name_val = $data['stimuli_name'];


if (isset($data['stimuli_description']))
    $desc_val = $data['stimuli_description'];

if (isset($data['stimuli_category']))
    $cat_val = $data['stimuli_category'];


if(isset($data['stimuli_type']))
    $type = $data['stimuli_type'];

?>

<script>
    
    var form_mode = 'modal';
    recordedAudio = false;
    var mediaRecorder =  null;
    var audioBlob = null;
    let shouldStop = false;
    let stopped = false;
        const handleSuccess = function(stream) {
            const options = {mimeType: 'audio/webm'};
            const recordedChunks = [];
            mediaRecorder = new MediaRecorder(stream, options);

            mediaRecorder.addEventListener('dataavailable', function(e) {
            if (e.data.size > 0) {
                recordedChunks.push(e.data);
            }

            if(shouldStop === true && stopped === false) {
                mediaRecorder.stop();
                stopped = true;
            }
            });

            mediaRecorder.addEventListener('stop', function() {
            audioBlob = new Blob(recordedChunks,{type:'audio/*'});
            var href = URL.createObjectURL(audioBlob);
            //downloadLink.download = 'acetest.wav';


            var preview = document.getElementById("preview");
            var audio = document.createElement("audio");
            var att = document.createAttribute("controls");       // Create a "class" attribute
            audio.setAttributeNode(att); 
            audio.classList.add("mx-auto");
            audio.classList.add("d-block");
            var source = document.createElement("source");
            source.src = href;
            audio.appendChild(source);
            preview.innerHTML = "";
            preview.appendChild(audio);

            var inpt = document.getElementById('inp_fileName');
            
            
            recordedAudio = true;
            inpt.value = "Áudio gravado. ";
            

            //const player = document.getElementById('player');
            //player.src = href;
            });

            //mediaRecorder.start();
        };

    navigator.mediaDevices.getUserMedia({ audio: true, video: false })
        .then(handleSuccess);
</script>

<div class="col d-none" id="newStimuliTemplate">
    <div class="container">
        <h2> Novo Estímulo<?php //echo $lang['new_stimuli'] ?></h2>

        <form enctype="multipart/form-data" autocomplete="off" action="" method="post" id="form">
            <input hidden required type="text" class="form-control" id="modal" name="modal" value="modal">
            
            <div class="form-group">
                <label for="stimuli_name">Nome:</label>
                <input required type="text" class="form-control" id="stimuli_name" name="stimuli_name" value="<?php echo $name_val;?>">
            </div>

            <div class="form-group">
                <label for="stimuli_description">Descrição:</label>
                <input required type="text" class="form-control" id="stimuli_description" name="stimuli_description" value="<?php echo $desc_val;?>">
            </div>

            <div class="form-group">
                <label for="stimuli_category">Categorias (separadas por espaços):</label>
                <input required type="text" placeholder="categoria1 categoria2"class="form-control" id="stimuli_category" name="stimuli_category" value="<?php echo $cat_val;?>">
            </div>
            
            <div class="form-group">
                <label for="stimuli_type">Tipo de estímulo</label>
                <select onchange="modal_changeType()"class="form-control" id="stimuli_type" name="stimuli_type">
                  <option value="image">Imagem</option>
                  <option value="audio">Áudio</option>
                </select>
              </div>

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                    <input type="checkbox" aria-label="Checkbox for following text input" id="publicStimuli" name="publicStimuli">
                    </div>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text">Compartilhar publicamente? ATENÇÃO: Todos usuários poderão visualizar o estímulo. Use estímulos livres (<a target="_blank" href="https://pixabay.com/">pixabay </a>)</span>
                </div>
                
                
            </div>

           <div class="input-group mb-3">
               <input required name="stimuli_file" id="stimuli_file" onchange="modal_loadFile(event)" class="inputFile" accept="image/*" type="file" style="display: none;">
                <div class="input-group-prepend">
                    <button  onclick="document.querySelector('#stimuli_file').click();"class="btn btn-outline-secondary" type="button">Selecionar Arquivo</button>
                </div>
                <input id="inp_fileName" type="text" readonly class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">

                <button class="btn btn-outline-secondary" disabled type="button" id="recordButton" onclick="recordAudio()"><i class="fas fa-microphone"></i></button>
            </div>

            <div class="container" id="preview">
                Selecione um arquivo.
            </div>
            



            <button type="button"  class="btn btn-primary btn-lg btn-block" onclick="modal_addImage(event)"> Cadastrar<?php //echo $lang['stimuli_next']; ?></button>
        </form>
    </div>
</div>

<script>
    
    
    function recordAudio(){
        const player = document.getElementById('player');
        var button = document.getElementById('recordButton');
        button.innerHTML = '<i class="fas fa-microphone-slash"></i>';
        button.classList.remove("btn-outline-secondary");
        button.classList.add("btn-warning");
        button.onclick = function(){
            stopRecording();
        };
        if(mediaRecorder!=null){
            mediaRecorder.start();
        }
        
        
    }
    function stopRecording(){
        var button = document.getElementById('recordButton');
        button.innerHTML = '<i class="fas fa-microphone"></i>';
        button.classList.add("btn-outline-secondary");
        button.classList.remove("btn-warning");

        button.onclick = function(){
            recordAudio();
        };
        const player = document.getElementById('player');
        if(mediaRecorder!=null){
            mediaRecorder.stop();
        }
        

    }
    
    function genNewStimuliForm(types, labels, mode='modal'){
        console.log("GEN NEW");
        var select = document.getElementById('stimuli_type');
        select.innerHTML = "";
        var i;
        for(i =0; i < types.length; i++){
            if(types[i] in labels){
                var option = document.createElement('option');
                option.value = types[i];
                option.innerHTML = labels[types[i]];
                select.appendChild(option);
            }
        }
        form_mode = mode;
        var form = document.getElementById('newStimuliTemplate').cloneNode(true);
        form.id='newStimuli';
        form.classList.remove('d-none');
        modal_changeType();
        return form;
    }
    
    var modal_changeType= function(event){
         var type = document.getElementById("stimuli_type").value;
         console.log("Type: " + type);
         var input_file = document.getElementById('stimuli_file');
        
         document.getElementById('recordButton').disabled = true;
        
         if(type == "image"){
             input_file.accept="image/*";
         }else if(type=='audio'){
             input_file.accept="audio/*";
             document.getElementById('recordButton').disabled = false;

         }
        var preview = document.getElementById("preview");
        preview.innerHTML = "Selecione um arquivo.";
        
        input_file.value="";
        document.getElementById('inp_fileName').value = "";
        //input_file.replaceWith(input_file.val('').clone(true));
    }
    var modal_loadFile = function (event) {
        var input_file = document.getElementById('stimuli_file');
        recordedAudio = false;
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
        else if (type.startsWith("audio")) {
            var audio = document.createElement("audio");
            var att = document.createAttribute("controls");       // Create a "class" attribute
            audio.setAttributeNode(att); 
            audio.classList.add("mx-auto");
            audio.classList.add("d-block");
            var source = document.createElement("source");
            source.src = fileURL;
            audio.appendChild(source);
            preview.innerHTML = "";
            preview.appendChild(audio);
            input_file.accept="audio/*";
             document.getElementById('recordButton').disabled = false;
        }
        
    };
    
    var modal_addImage = function(event){
        
        var form = document.getElementById('newStimuli');
        var elements = form.getElementsByTagName('input');
        var valid = true;
        var i;
        for(i = 0; i < elements.length; i++){
            if(!elements[i].checkValidity()){
                if(elements[i].id!="stimuli_file"){
                                    
                    valid = false;
                }
                else if(!recordedAudio){
                    valid = false;
                }
                elements[i].classList.add('border-danger');
                console.log(elements[i]);
            }
            else
                elements[i].classList.remove('border-danger');
        }
        if(!valid){
            console.log("not valid");
            return;
        }
        
        let formData = new FormData(document.getElementById('form'));
        let req = new XMLHttpRequest();
        if(recordedAudio){
            var inp = document.getElementById("stimuli_file");
            inp.parentElement.removeChild(inp);   
            
            formData.append("stimuli_file", audioBlob, "file.wav");

        }
        
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
               // Typical action to be performed when the document is ready:
               console.log(this.responseText);
                if(this.responseText == "INSERT_SIMULI_OK"){
                    console.log("funcionou! ");
                    if(form_mode == 'modal')
                        closeModal();
                    else
                        window.location.href = "<?php echo BASE_URL;?>/stimuli";
                    //changeImage();
                }
                else
                    console.log("Não funcionou :( ");
            }
        };
        var url = "<?php echo BASE_URL . '/stimuli/index.php?action=newStimuliProccessForm'; ?> ";
        //req.open("POST", 'index.php?action=newStimuliProccessForm');
        req.open("POST", url);
        req.send(formData);
    }
</script>