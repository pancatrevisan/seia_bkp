
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

 require_once ROOTPATH . '/activity/views/newStimuliModal.php'; 

?>


<div class="col " id="">
    <div class="container" id="container">
        
    </div>
</div>

<script>
document.body.onload = function(){
    var labels = [];
        labels['image'] = "Imagem";
        labels['audio'] = "Áudio";
    var types ='image,audio,video';
        var all = genNewStimuliForm(types.split(','), labels,'non_modal');
        modal_changeType();
    document.getElementById('container').appendChild(all);
    var original = document.getElementById('newStimuliTemplate');
    original.innerHTML = "";
    console.log(original);
    //original.parentElement.remove(original);
}; 
</script>