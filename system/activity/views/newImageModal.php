
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


<div class="col d-none" id="newStimuliTemplate">
    <div class="container">
        <h2> Novo Estímulo<?php //echo $lang['new_stimuli'] ?></h2>

        <form enctype="multipart/form-data" autocomplete="off" action="" method="post" id="form">
            <input hidden required type="text" class="form-control" id="modal" name="modal" value="modal">
            <input hidden required type="text" class="form-control" id="stimuli_type" name="stimuli_type" value="image">
            <div class="form-group">
                <label for="stimuli_name">Nome:</label>
                <input required type="text" class="form-control" id="stimuli_name" name="stimuli_name" value="<?php echo $name_val;?>">
            </div>

            <div class="form-group">
                <label for="stimuli_description">Descrição:</label>
                <input required type="text" class="form-control" id="stimuli_description" name="stimuli_description" value="<?php echo $desc_val;?>">
            </div>

            <div class="form-group">
                <label for="stimuli_category">Categoria:</label>
                <input required type="text" class="form-control" id="stimuli_category" name="stimuli_category" value="<?php echo $cat_val;?>">
            </div>


           <div class="input-group mb-3">
                <input name="stimuli_file" id="stimuli_image" onchange="modal_loadFile(event)" class="inputFile" accept="image/*" type="file" style="display: none;">
                <div class="input-group-prepend">
                    <button  onclick="document.querySelector('#stimuli_image').click();"class="btn btn-outline-secondary" type="button">Selecionar Arquivo</button>
                </div>
                <input id="inp_fileName" type="text" readonly class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
            </div>

            <div class="container" id="preview">
                Selecione um arquivo.
            </div>



            <button type="button"  class="btn btn-primary btn-lg btn-block" onclick="modal_addImage(event)"> Cadastrar<?php //echo $lang['stimuli_next']; ?></button>
        </form>
    </div>
</div>

<script>
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
        
        let req = new XMLHttpRequest();
        let formData = new FormData(document.getElementById('form'));
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
               // Typical action to be performed when the document is ready:
               console.log(this.responseText);
                if(this.responseText == "INSERT_SIMULI_OK"){
                    console.log("funcionou! ");
                    closeModal();
                    changeImage();
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