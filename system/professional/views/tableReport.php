<?php
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require_once ROOTPATH . '/utils/DBAccess.php';
require_once ROOTPATH . '/program/ProgramController.php';
require_once ROOTPATH . '/ui/modal.php';



$db = new DBAccess();
$SQL = "SELECT * FROM student WHERE student.id='" . $data['studentId'] . "'";
$res = $db->query($SQL);
$student_data = mysqli_fetch_array($res);



?>

<!DOCTYPE html>
<html lang="ptb">
<head>
  <title><?php echo $data['page_title']; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo BASE_URL;?>/activity/views/paper.css">
  <link rel="stylesheet" href="<?php echo BASE_URL;?>/external/bootstrap.min.css">
  <script src="<?php echo BASE_URL;?>/external/jquery.min.js"></script>
  <script src="<?php echo BASE_URL;?>/external/popper.min.js"></script>
  <script src="<?php echo BASE_URL;?>/external/bootstrap.min.js"></script>
  <script src="<?php echo BASE_URL;?>/external/FileSaver.min.js"></script>
  
  <script type="text/javascript" id="www-widgetapi-script" src="https://s.ytimg.com/yts/jsbin/www-widgetapi-vfl2dBoXz/www-widgetapi.js" async=""></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" >
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <link href="<?php echo BASE_URL;?>/external/enjoyhint/enjoyhint.css" rel="stylesheet">
  <script src="<?php echo BASE_URL;?>/external/enjoyhint/enjoyhint.js"></script>
</head>



<body class="">
<div id="booody" class="container-fluid">

<div class="row mt-3 d-none">
<div class="col-4"></div>
<div class="col-4"></div>
    <div class="col-4">
        <button onclick="printTable()"class="btn btn-lg btn-block btn-info">Imprimir</button>
    </div>
</div>

<div class="row mt-3">
    <div class="col-3">

    <div class="card text-white bg-danger border-dark" id="<?php echo $student_data['id']; ?>">
            <img class="img-fluid rounded img-thumbnail" width="100%" height="auto" src="<?php echo BASE_URL; ?>/data/student/<?php echo $student_data['id']; ?>/<?php echo $student_data['avatar']; ?>">
            <h4 class="card-header border-dark"><?php echo $student_data['name']; ?></h4>
            <h4 class="card-header border-dark">Exportar para Excel</h4>
    </div>


    </div>
    <div class="col-9">
        <div id="table_div"></div>
    </div>
        
</div>



<script>

    function to_excel(){
        
    }

    function getStimuli(stimuli, target_tag, border=null, onlyText = false){
        console.log(stimuli);
        var xhttp = new XMLHttpRequest();
        var activity = this;
        var tag = "";
        
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                var obj = JSON.parse(this.responseText,true);
                var target =  document.getElementById(target_tag);
                console.log("target? " + target+ " destination: ");
                if(obj['type']=='image'){
                    tag = document.createElement("img");
                    tag.src = obj['data'];
                    tag.classList.add("img-thumbnail");
                    tag.width = 200;
                    if(border){
                        tag.classList.add("border", border);
                    }
                }
                else if(obj['type']=="audio"){
                    tag = document.createElement("audio");
                    tag.controls = true;
                    var src = document.createElement("source");

                    src.src = "<?php echo BASE_URL;?>/" + obj['url'];
                    tag.appendChild(src);
                    //tag.classList.add("img-thumbnail");
                    //tag.width = 200;
                    
                }
                target.appendChild(tag);
            }
        };
        if(stimuli['type']=="text"){
            tag = document.createElement("span");
            tag.classList.add("badge", "badge-secondary");
            tag.innerHTML = stimuli['value'];
            if(border){
                tag.classList.add("border", border);
            }
            target.appendChild(tag);
            
        }
        else if(stimuli['type']=="video"){
            
            target.appendChild(tag);
        }
        else{
            var id = stimuli['value'];
            console.log("ID A PEGAR: "+id);
            xhttp.open("GET", "<?php echo BASE_URL; ?>/stimuli/index.php?action=getById_as_json&id="+id+"&destination="+target_tag, true);
            xhttp.send();
        }
        
    }
    
    var stimulis_to_get = [];
    var ID_COUNTER = [];

    function getTheStimuli(){
        console.log(stimulis_to_get);
        for(var i =0; i< stimulis_to_get.length; i++){
            getStimuli(stimulis_to_get[i],stimulis_to_get[i]['id']);
        }
    }

    function genID(){
        /*var id = "s_"+window.performance.now();
        while(id in used_ids){
            id = "s_"+window.performance.now();
        }
        used_ids.push(id);*/
       id = "S_" + ID_COUNTER;
       ID_COUNTER++;
        return id;
    }

    function export_triall(trial){
        var trial_date = "";
        var name = trial['a_name'];
        var model = "";
        var compareStimuli = "";
        var selectedStimuli = "";
        var result = "";
        var expeceted = "";
        var runTime = "";



        var meta = trial['spat_result_data'];
        var s_date = new Date(trial['start_date']); 
        var e_date = new Date(trial['spat_end_date']);
        var timeDiff = (e_date - s_date) / 1000;
        runTime = timeDiff+"s";

        trial_date = s_date.toLocaleDateString() +" " + s_date.toLocaleTimeString();
        var bkg_color="FFF";


        
        if(trial['spat_result'] == '1'){
            result = "Correto";
            
        }if(trial['spat_result'] == '-1'){
            result = "Errado";
         
        }
        if(trial['spat_result'] == '2'){
            result = "Correto (dica)";
         
        }

        if(meta['type']=="select"){
            var model_s = meta['model'];
            var id = genID();
            
            model_s = {"type":model_s['type'], "value":model_s['value'], id:id}; 
            stimulis_to_get.push(model_s);
            model = {v:model_s['value'], f:"<div  id='" + model_s['id']+"'></div>"};
            


            var compare_s = meta['stimulis'];
            var comp_s_div = "";
            for(var j =0; j < compare_s.length; j++){
                var id = genID();
            
                var c_s = {"type":compare_s[j]['type'], "value":compare_s[j]['value'], id:id};

                comp_s_div += "<div id='" + c_s['id']+"'></div>";
                stimulis_to_get.push(c_s);
            }
            compareStimuli = {v:model_s['value'], f:comp_s_div};


            var sel_s = meta['selected'];
            var id = genID();
            ;
            sel_s = {"type":sel_s['type'], "value":sel_s['value'], id:id };
            selectedStimuli = {v:sel_s['value'], f:"<div id='" + sel_s['id']+"'></div>" };
            stimulis_to_get.push(sel_s);

            
            var exp_s = meta['expected'];
            var id = genID();
            
            exp_s = {"type":exp_s['type'], "value":exp_s['value'], id:id };
            expeceted = {v:exp_s['value'], f:"<div id='" + exp_s['id']+"'></div>" };
            stimulis_to_get.push(exp_s);

        }
        else if (meta['type']=="association"){
            
            if('screenshot_id' in meta){

            
                var src = "<?php echo BASE_URL?>/data/student/<?php echo $student_data['id']; ?>/"+meta['screenshot_id']+".png";
                selectedStimuli = '<img class="img-thumbnail" src="' +src+ '">';
            }
        }


        return [trial_date, name, runTime, model, compareStimuli, selectedStimuli, expeceted, result];
    }

    var js = '<?php echo $data["results"]; ?>';
    
    //remove tabs
    js = js.replace(/\t/g, ' ');
    
    
    
    var trial_results = JSON.parse(js, true);
    
    google.charts.load('current', {'packages':['table']});
    google.charts.setOnLoadCallback(drawTable);
    var table, data;
    function drawTable() {
    data = new google.visualization.DataTable();
    data.addColumn('string', 'Data');
    data.addColumn('string', 'Tentativa');
    data.addColumn('string', 'Tempo');
    data.addColumn('string', 'Estímulo modelo');
    data.addColumn('string', 'Estímulos de comparação');
    data.addColumn('string', 'Resposta');
    data.addColumn('string', 'Esperado');
    data.addColumn('string', 'Resultado');
    //data.addColumn('string', 'Data Fim');
    
    for(var i = 0; i < trial_results.length; i++){
        
        var row = export_triall(trial_results[i]);

        data.addRows([row]);
        var c = 7;
        if(row[c]=="Errado"){
            
                data.setProperties(i, c, {style: 'color:rgb(197,15,24);'});
        }
        else if(row[c]=="Correto (dica)"){
            
                data.setProperties(i, c, {style: 'color:rgb(255,201,24);'});
        }
        else if(row[c]=="Correto"){
            
            
                data.setProperties(i, c, {style: 'color:rgb(34,177,76);'});
        }
    }
   

    table = new google.visualization.Table(document.getElementById('table_div'));
    
    table.draw(data, {width: '100%', height: '100%', allowHtml: true });
    console.log(stimulis_to_get);
    getTheStimuli();
        
    
    }


    function printTable(){
        var printContents = document.getElementById('table_div').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
    
</script>
</div>
</body>