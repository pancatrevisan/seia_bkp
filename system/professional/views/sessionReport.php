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


<div class="row mt-3">
    <div class="col">
        <div class="container">
            <div class="row">
                <div class="col-3">
                    <div class="row">
                        <div class="col"><img class="img-fluid rounded" src="<?php echo BASE_URL; ?>/data/student/<?php echo $student_data['id']; ?>/<?php echo $student_data['avatar']; ?>"></div>
                    </div>
                    <div class='row'>
                        <div class='col'>
                            <a class='btn btn-lg btn-info text-light btn-block mt-3' href="<?php echo BASE_URL; ?>/professional/index.php?action=studentReport&studentId=<?php echo $student_data['id']; ?>"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>


                </div>
                <div class="col-9" id="content">
                    <div class="row alert alert-primary" role="alert">
                        <div class="col">
                            <?php echo $student_data['name']; ?>
                        </div>
                    </div>



                    <div class="row alert alert-light border " role="alert">
                        <div class="col" id='sessionDescription'>
                            Sessão ...
                        </div>
                        <div class="col">
                            <div id="chart_div"></div>
                        </div>
                        
                    </div>

                    <div class="row  alert  border" role="alert" >
                        <div class='col' id="line_resumee" style="width: 900px; height: 500px">">
                            Gráfico 
                        </div>
                    </div>


                    <div class="row  alert  border" role="alert" >
                        <div class='col' >
                            <a target="blank" href="<?php echo BASE_URL;?>/professional/index.php?action=tableReport&studentId=<?php echo $student_data['id']; ?>&type=program&program_trial_id=<?php echo $data['sessionId'];?>"class = "btn btn-lg btn-block btn-info" id="seila">Detalhar Aplicação do Programa </a>
                        </div>
                    </div>

                    <div class="row  alert  border" role="alert" >
                        <div class='col' >
                            <button onclick="askToRemoveRegitry()" class= "btn btn-lg btn-block btn-danger" id="seila">Excluir este regitro </button>
                        </div>
                    </div>

                    <div class="row  alert  border" role="alert" >
                        <div class='col' id="trials">
                        </div>
                    </div>


                   
                    
                </div>
            </div>
        </div>
    </div>
</div>



<div class = "row d-none mt-2 border border-dark" id="trialTemplate">
<div class="col">
<div class ="row " >
    <div class='col-4' id="trialNameTemplate">
        Nome da trial    
    </div>
    <div class="col-2" id="trialStartDateTemplate">
        Início
    </div>
    <div class="col-2" id="trialEndDateTemplate">
        Fim
    </div>
    <div class="col-2" id="trialResultTemplate">
        Resultado
    </div>

    <div class='col-2' >
        <button class="btn btn-dark  btn-block" id="trialShowCollapseTemplate" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <i class="fa fa-bars"></i>
        </button>
    </div>
</div>
    <div class="row mt-3  " >
        <div class="col collapse border border-dark alert alert-light" id="collapseExampleTemplate"></div>
    </div>
    </div>
</div>




<script type="text/javascript">
   
    var SESSION_PROGRAM_ID = null;
    var STUDENT_ID = null;
    document.body.onload = function() {
        var xhttp = new XMLHttpRequest();

        var activity = this;
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                
                var res = JSON.parse(this.responseText, true);
                console.log(res);
                SESSION_PROGRAM_ID = res['session_program_id'];
                STUDENT_ID = res['student_id'];
                
                var container = document.getElementById('sessionDescription');
                container.innerHTML = "";
                var sess = document.createElement('div');
            sess.classList.add('row', 'alert', 'border');
            var data = new Date(res['date']);
            sess.innerHTML = //"Aplicador: " + res[i]['professional_id'] + " Data: " + data.toLocaleDateString() + " Horário de início: " + data.toLocaleTimeString();
            '<div class="col">  Aplicador: ' + res['professional_id'] +'</div>' +'<div class="col"> Data início: ' + data.toLocaleDateString() + '</div> <div class="col"> Horário: ' + data.toLocaleTimeString() + "</div>";


                var nameRow = document.createElement("div");
                nameRow.classList.add('row', 'alert', 'border');

                nameRow.innerHTML = '<div class="col"> Programa: ' + res['program_name'] + '</div>';
                container.appendChild(nameRow);
                container.appendChild(sess);
                
                
                for (var i =0; i < res['trials'].length; i++) {
                    
                    addTrial(res['trials'][i]);
                }

                google.charts.setOnLoadCallback(drawLineGraph);
            }
        };
        xhttp.open("GET", "<?php echo BASE_URL; ?>/professional/index.php?action=getSession_json&studentId=<?php echo $student_data['id']; ?>&sessionId=<?php echo $data['sessionId']; ?>", true);
        xhttp.send();
    };

    var numCorrect = 0;
    var numWrong   = 0;
    function addTrial(trial) {
        
        var trialLine = document.getElementById('trialTemplate').cloneNode(true);
        trialLine.id = "trial_" + trial['spat_id'];
        trialLine.classList.remove('d-none');



        var name = trialLine.querySelector("#trialNameTemplate");
        name.id = "trial_name_" + trial['spat_id'];
        name.innerHTML = '<a data-toggle="tooltip" data-placement="top" title="Detalhar tentativas com esta atividade" target="blank" href="<?php echo BASE_URL;?>/professional/index.php?action=tableReport&studentId=<?php echo $student_data['id']; ?>&type=activity_trials&activity_id='+ trial['a_id'] +'"> ' + trial['a_name'] +'</a> ' ;


        var sDate = trialLine.querySelector("#trialStartDateTemplate");
        sDate.id = "trial_sdate_"+trial['spat_id'];
        sDate.innerHTML = trial['start_date'];

        var eDate = trialLine.querySelector("#trialEndDateTemplate");
        eDate.id = "trial_edate_"+trial['spat_id'];
        eDate.innerHTML = trial['spat_end_date'];

        var result = trialLine.querySelector("#trialResultTemplate");
        result.id = "trial_result_"+trial['spat_id'];
        if(trial['spat_result']=='-1'){
            numWrong++;
            result.innerHTML = "Errou";
            trialLine.classList.add('alert-danger');
        }
        else if(trial['spat_result']=='1'){
            numCorrect++;
            result.innerHTML = "Acertou";
            trialLine.classList.add('alert-primary');
        }
        else if(trial['spat_result']=='2'){
            numCorrect++;
            result.innerHTML = "Acertou/Dica";
            trialLine.classList.add('alert-warning');
        }

        var collapseContent = trialLine.querySelector("#collapseExampleTemplate");
        collapseContent.id="collapseContent_"+trial['spat_id'];
        

        var collapseButton = trialLine.querySelector("#trialShowCollapseTemplate");
        collapseButton.id = "collapse_"+trial['spat_id'];
        collapseButton.setAttribute('data-target','#'+collapseContent.id);
        

        


        document.getElementById('trials').appendChild(trialLine);
        if('spat_result_data' in trial){
            var j = JSON.parse(trial['spat_result_data'], true);
            console.log("jsion");
            console.log(j);
            collapseContent.appendChild (doTheTrialReport( trial['spat_result_data'], collapseContent.id));
        }
        
        google.charts.setOnLoadCallback(drawChart);
        
        
    }

    google.charts.load('current', {
        'packages': ['corechart']
    });

    
    function askToRemoveRegitry(){
        showModal("Remover este registro?", "Isso apagará todos os dados deste registro e não pode ser desfeito. Deseja remover?",function(){
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    if(this.responseText=="OK"){
                        alert("Removido!");
                        window.location.href = "<?php echo BASE_URL; ?>/professional/index.php?action=studentReport&studentId="+STUDENT_ID;
                    }
                }
            }
            xhttp.open("POST", "<?php echo BASE_URL; ?>/professional/index.php?action=removeSessionReport", true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.send("session_id=<?php echo $data['sessionId']; ?>");
        }, true);
    }



    function drawLineGraph() {
        
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var report_data = [];
                console.log("TIME GRAPH");
                
                var json_data = JSON.parse(this.responseText, true);
                console.log(json_data);
                
                for(var key in json_data['programs']){
                    var num_corret = 0;
                    var num_correct_tip = 0;
                    var num_wrong = 0;
                    var total = 0;
                    var d = json_data['programs'][key];
                    
                    console.log("D:" + d['date'] + " L: " + d.length);
                    console.log(d);
                    for(var j=0; j < d['activities'].length; j++){
                        var a = d['activities'][j];
                        total++;
                        
                        console.log("act res: " + a['spat_result'] );
                        if(a['spat_result'] == '1'){
                            num_corret++;
                        }else if(a['spat_result'] == '2'){
                            num_correct_tip++;
                        }
                        else if(a['spat_result'] == '-1'){
                            num_wrong++;
                        }
                        
                    }
                    var percentage = 100.0/total;
                    console.log("num correct: " + num_corret);
                    console.log("num tip: "+num_correct_tip);
                    var correct_percentage = num_corret * percentage;
                    var correct_tip_percentage = num_correct_tip * percentage;
                    var wrong_percentage = num_wrong * percentage;
                    report_data.push({'data': {v:d['date'], f:new Date(d['date']).toLocaleDateString() } , 'correct':{v:correct_percentage, f:correct_percentage+"%"}, 'correct_tip':{v:correct_tip_percentage, f:correct_tip_percentage+"%"}, 'wrong':{v:wrong_percentage, f:wrong_percentage+"%"}});
                }

                var data =  new google.visualization.DataTable();
                data.addColumn('string', 'Data');
                data.addColumn('number', 'Acertos (%)');
                data.addColumn('number', 'Acertos com dica (%)');
                data.addColumn('number', 'Erros (%)');
                console.log(report_data);
               // for(var c = 0; c<report_data.length; c++){
                for(var c = report_data.length-1; c>=0; c--){
                    
                    data.addRows([ [report_data[c]['data'], report_data[c]['correct'], report_data[c]['correct_tip'],report_data[c]['wrong']]]);
                }
                var options = {
                    title: 'Progresso nas últimas aplicações',
                    
                    legend: { position: 'bottom' },
                    vAxis: {
                        maxValue    : 100
                    }
                };

                var chart = new google.visualization.LineChart(document.getElementById('line_resumee'));

                chart.draw(data, options);
            }
        }
        var program_id = SESSION_PROGRAM_ID;
        var student_id = STUDENT_ID;
        xhttp.open("GET", "<?php echo BASE_URL; ?>/professional/index.php?action=getProgramReport_json&program_id="+program_id+"&student_id="+student_id, true);
        xhttp.send();
    }





    // Set a callback to run when the Google Visualization API is loaded.
    

    function getStimuli(stimuli, target, border=null){
        if(stimuli == null){
            return;
        }
        var xhttp = new XMLHttpRequest();
        var activity = this;
        var tag = "";
        
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                var obj = JSON.parse(this.responseText,true);
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
            
            xhttp.open("GET", "<?php echo BASE_URL; ?>/stimuli/index.php?action=getById_as_json&id="+id, true);
            xhttp.send();
        }
        
    }
    

    function doTheTrialReport(data, contentId){

        var conteiner = document.createElement("div");
        var arr_data = "";
        try{
            arr_data = JSON.parse(data,true);
        }catch (e){
            conteiner.innerHTML = "";
            return conteiner;
        }
        
        if(arr_data['type']=="draw"){
            var titleRow = document.createElement("div");
            titleRow.classList.add("row", "alert", "alert-success");

            if(arr_data['save_screenshot']){
                var image = document.createElement('img');
                image.classList.add('img-fluid');
                image.src = "<?php echo BASE_URL;?>/data/student/" + STUDENT_ID+"/" + arr_data['screenshot_id']+".png";
                var imgRow = document.createElement("div");
                imgRow.classList.add("row", "alert", "bg-light");

                imgRow.appendChild(image);
                document.getElementById(contentId).appendChild(imgRow);
            }
            var modelLine = document.createElement('div');
            modelLine.classList.add("row","alert","alert-success");
            var nameCol = document.createElement("div");
            nameCol.classList.add("col-6");
            nameCol.innerHTML = "Esperado:";
            var modelCol = document.createElement("div");
            modelCol.classList.add("col-6"); 
            modelLine.appendChild(nameCol);
            modelLine.appendChild(modelCol);
            conteiner.appendChild(modelLine);

            document.getElementById(contentId).appendChild(modelLine);
            getStimuli( arr_data['expected'], modelCol);



            //selected
            var selRow = document.createElement("div");
            selRow.classList.add("row","alert","alert-success");

            var selName = document.createElement("div");
            selName.classList.add("col-6");
            selName.innerHTML = "Estímulo selecionado";

            var selImg = document.createElement("div");
            selRow.appendChild(selName);
            selRow.appendChild(selImg);

            document.getElementById(contentId).appendChild(selRow);
            getStimuli( arr_data['selected'], selImg);



            var textRow = document.createElement("div");
            textRow.classList.add("row","alert","alert-success");

            var textNameCol = document.createElement("div");
            textNameCol.classList.add("col");
            textNameCol.innerHTML = "Tipo de contorno";

            var dataCol = document.createElement("div");
            dataCol.classList.add("col");
            if(arr_data['draw_type'] == 'underline'){
                dataCol.innerHTML = "Sublinhado";
            }else if(arr_data['draw_type'] == 'circle'){
                dataCol.innerHTML = "Circular";
            }
            else if(arr_data['draw_type'] == 'cut'){
                dataCol.innerHTML = "Riscar";
            }else if(arr_data['draw_type'] == 'triangle'){
                dataCol.innerHTML = "Triângulo";
            }



            textRow.appendChild(textNameCol);
            textRow.appendChild(dataCol);

            document.getElementById(contentId).appendChild(textRow);

            if(arr_data['timeFinished']){
                var timeRow = document.createElement("div");
                timeRow.classList.add("row","alert","alert-danger");

                var timeNameCol = document.createElement("div");
                timeNameCol.classList.add("col");
                timeNameCol.innerHTML = "Tempo ESGOTADO";

                timeRow.appendChild(timeNameCol);
                document.getElementById(contentId).appendChild(timeRow);

            }
             

        }
        else if(arr_data['type']=="text"){
            var textRow = document.createElement("div");
            textRow.classList.add("row","alert","alert-success");

            var nameCol = document.createElement("div");
            nameCol.classList.add("col");
            nameCol.innerHTML = "Texto Inserido";

            var dataCol = document.createElement("div");
            dataCol.classList.add("col");
            
            textRow.appendChild(nameCol);
            textRow.appendChild(dataCol);

            document.getElementById(contentId).appendChild(textRow);

            getStimuli( arr_data, textRow);
        }
        if(arr_data['type']=="feedback"){
            if("text" in arr_data){
                var textRow = document.createElement("div");
                textRow.classList.add("row","alert","alert-success");

                var nameCol = document.createElement("div");
                nameCol.classList.add("col");
                nameCol.innerHTML = "Texto Inserido";

                var dataCol = document.createElement("div");
                dataCol.classList.add("col");
                
                textRow.appendChild(nameCol);
                textRow.appendChild(dataCol);

                document.getElementById(contentId).appendChild(textRow);
                var dta = {"type":"text","value":arr_data['text']};
                getStimuli( dta, textRow);
            }
        }
        else if(arr_data['type']=="select"){
            //mts
            var modelLine = document.createElement('div');
            modelLine.classList.add("row","alert","alert-success");
            var nameCol = document.createElement("div");
            nameCol.classList.add("col-6");
            nameCol.innerHTML = "Modelo:";
            var modelCol = document.createElement("div");
            modelCol.classList.add("col-6"); 
            modelLine.appendChild(nameCol);
            modelLine.appendChild(modelCol);
            conteiner.appendChild(modelLine);

            document.getElementById(contentId).appendChild(modelLine);

            getStimuli( arr_data['model'], modelCol);



            //expected
            var expRow = document.createElement("div");
            expRow.classList.add("row","alert","alert-success");

            var expName = document.createElement("div");
            expName.classList.add("col-6");
            expName.innerHTML = "Estímulo correto";

            var expImg = document.createElement("div");

            expRow.appendChild(expName);
            expRow.appendChild(expImg);
            document.getElementById(contentId).appendChild(expRow);
            getStimuli( arr_data['expected'], expImg);

            //selected
            var selRow = document.createElement("div");
            selRow.classList.add("row","alert","alert-success");

            var selName = document.createElement("div");
            selName.classList.add("col-6");
            selName.innerHTML = "Estímulo selecionado";

            var selImg = document.createElement("div");
            selRow.appendChild(selName);
            selRow.appendChild(selImg);

            document.getElementById(contentId).appendChild(selRow);
            getStimuli( arr_data['selected'], selImg);


            

            //COMPARACAO
            var compRow = document.createElement("div");
            compRow.classList.add("row","alert","alert-success");

            var compName = document.createElement("div");
            compName.classList.add("col-6");
            compName.innerHTML = "Estímulos de comparação";

            var compImg = document.createElement("div");
            compRow.appendChild(compName);
            compRow.appendChild(compImg);

            document.getElementById(contentId).appendChild(compRow);
            

            for(var i = 0; i < arr_data['stimulis'].length; i++){
                getStimuli( arr_data['stimulis'][i], compImg);
            }

            
        }
        else if(arr_data['type']=="association"){
            var titleRow = document.createElement("div");
            titleRow.classList.add("row", "alert", "alert-success");

            if(arr_data['save_screenshot']){
                var image = document.createElement('img');
                image.classList.add('img-fluid');
                image.src = "<?php echo BASE_URL;?>/data/student/" + STUDENT_ID+"/" + arr_data['screenshot_id']+".png";
                var imgRow = document.createElement("div");
                imgRow.classList.add("row", "alert", "bg-light");

                imgRow.appendChild(image);
                document.getElementById(contentId).appendChild(imgRow);
            }
        
                
            var titleRow = document.createElement("div");
            titleRow.classList.add("row", "alert", "alert-success");
                var contCol = document.createElement("div");
            contCol.classList.add("col");
            contCol.innerHTML = "Conteiner";

            var stimCol = document.createElement("div");
            stimCol.classList.add("col");
            stimCol.innerHTML = "Estímulos adicionados";

            titleRow.appendChild(contCol);
            titleRow.appendChild(stimCol);

            document.getElementById(contentId).appendChild(titleRow);



            for(var key in arr_data['keys']){
                var row = document.createElement("div");
                row.classList.add("row","alert","alert-info");

                var model = document.createElement("div");
                model.classList.add("col");
                getStimuli( arr_data['keys'][key]['model'], model);

                var inserted = document.createElement("div");
                inserted.classList.add("col");

                row.appendChild(model);
                row.appendChild(inserted);
                document.getElementById(contentId).appendChild(row);

                for(var i =0; i < arr_data['keys'][key]['inserted'].length; i++){
                    console.log(arr_data['keys'][key]['inserted'][i]);
                    if(isIn(arr_data['keys'][key]['inserted'][i], arr_data['keys'][key]['correct']))
                        getStimuli( arr_data['keys'][key]['inserted'][i], inserted,"border-success");
                    else
                        getStimuli( arr_data['keys'][key]['inserted'][i], inserted,"border-danger");
                }
            }
        
            
        }
        else if(arr_data['type']=="raceGame"){
            var titleRow = document.createElement("div");
            titleRow.classList.add("row", "alert", "alert-success");

            
        
                
            var titleRow = document.createElement("div");
            titleRow.classList.add("row", "alert", "alert-success");
            var contCol = document.createElement("div");
            contCol.classList.add("col");
            contCol.innerHTML = "Emoção para imitar";

            var stimCol = document.createElement("div");
            stimCol.classList.add("col");
            stimCol.innerHTML = "Emoção imitada pelo estudante";

            titleRow.appendChild(contCol);
            titleRow.appendChild(stimCol);

            document.getElementById(contentId).appendChild(titleRow);



            for(var i =0;i < arr_data['trials'].length; i++){
                var emot = arr_data['trials'][i];
                var row = document.createElement("div");
                var res = emot['performed']; var expected = emot['expected'];

                row.classList.add("row","alert");
                if(res == expected)
                    row.classList.add("alert-info");
                else
                row.classList.add("alert-danger");

                
                var model_data = document.createElement("div");
                model_data.classList.add("col");
                var stimuli_row = document.createElement("div");
                stimuli_row.classList.add("row");
                var stimuli_col = document.createElement("div");
                stimuli_col.classList.add("row");
                stimuli_row.appendChild(stimuli_col);

                
                getStimuli( emot['stimuli'], stimuli_col);
                var name_row = document.createElement("div");
                name_row.classList.add("row");
                var name_col = document.createElement("div");
                name_col.classList.add("row");
                name_col.innerHTML = emotTranslate[emot['expected']];
                name_row.appendChild(name_col);

                model_data.appendChild(stimuli_row);
                model_data.appendChild(name_row);





                var inserted = document.createElement("div");
                inserted.classList.add("col");
                inserted.innerHTML = emotTranslate[emot['performed']] +". Tempo: " + emot['time'].toFixed(2) + "s";
                row.appendChild(model_data);
                row.appendChild(inserted);
                document.getElementById(contentId).appendChild(row);

                /*for(var i =0; i < arr_data['keys'][key]['inserted'].length; i++){
                    console.log(arr_data['keys'][key]['inserted'][i]);
                    if(isIn(arr_data['keys'][key]['inserted'][i], arr_data['keys'][key]['correct']))
                        getStimuli( arr_data['keys'][key]['inserted'][i], inserted,"border-success");
                    else
                        getStimuli( arr_data['keys'][key]['inserted'][i], inserted,"border-danger");
                }*/
            }
        
            
        }
        else if(arr_data['type']=="preferenceSelection"){
            if(arr_data['pref_ids']){ //compatibilidade...
                for (var i = 0; i < arr_data['pref_ids'].length; i++){
                    var prefRow = document.createElement("div");
                    prefRow.classList.add("row","alert","alert-success");

                    var prefName = document.createElement("div");
                    prefName.classList.add("col-6");
                    prefName.innerHTML = "Preferência " +(i+1);

                    var prefImg = document.createElement("div");
                    prefRow.appendChild(prefName);
                    prefRow.appendChild(prefImg);

                    document.getElementById(contentId).appendChild(prefRow);
                    getStimuli( arr_data['pref_ids'][i], prefImg);
                }
            }
        
            
        }
    
        return conteiner;
    }

    function isIn(el, arr){
        for(var i =0; i < arr.length; i++){
            if(el['id'] == arr[i]['id']){
                return true;
            }
        }
        return false;
    }

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart(correctNumer, wrongNumber ) {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
            ['Acertos', numCorrect],
            ['Erros', numWrong],
        ]);

        // Set chart options
        var options = {
            'title': 'Visão Geral',
            'width': 300,
            'height': 200
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }

    var emotTranslate ={
        "sadness":"Tristeza",
        "anger":"Raiva",
        "hapiness":"Felicidade",
        "surprise":"Surpresa",
        "disgust":"Desgosto",
        "fear":"Medo"
    };
</script>