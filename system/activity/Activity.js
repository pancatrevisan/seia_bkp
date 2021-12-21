class Activity {
    constructor(id, content, canvas, htmlCanvas, baseURL) {
        
        this.result = 0;
        this.RESULT_CORRECT     =  1;
        this.RESULT_WRONG       = -1;
        this.RESULT_NEUTRAL     =  0;//para instruções que não retornam resultado correto/errado
        this.RESULT_CORRECT_TIP =  2;
        this.RESULT_CORRECT_DATA =  7;
        
        this.resultData = "";
        this.baseURL = baseURL;
        this.htmlCanvas = htmlCanvas;
        this.canvas = canvas;
        this.id = id;
        this.dragging = false;
        this.running = false;
        this.done = false;
        this.instructions = [];
        this.currentInstruction = 0;
        this.images = [];
        this.audios = [];
        this.videos = [];
        this.info = [];
        this.lastPointerPos = [];
        this.pointerMovement = [];
        this.downPointer = false;
        this.wasClick = false;
        this.ready = false;
        this.dataReceived=[];
        this.me = this;
        this.editing = false;
        this.instructionBeingEdited;
        this.paused = true;
        this._editorSize = [800,600];
        this.showTip = false;
        this.tipType = 'none';
        this.screenShotTemp  = null;
        //todo. this functions should exist in the current edition page.
        this.removeStimulusCallback ="removeStimulusCallback";
        this.configureStimulusCallback = "configureStimulusCallback";

        
        if (content.length > 0) {
            this.proccessXML(content);
        }
        this.xml = content;
        console.log(this.instructions);
    }
    

    addStimulusLocally(stimulus){
        if(document.getElementById(stimulus.id)!=null){
            var el = document.getElementById(stimulus.id);
            el.parentElement.removeChild(el);
        }
        document.body.appendChild(stimulus);
    }
    saveTemporaryScreenshot(){
        this.screenShotTemp = this.canvas.toDataURL("image/png");
        //console.log("temp ss");
        //console.log(this.screenShotTemp);
    }
    saveScreenshot(key, studentId){
        var activityPreview = this.canvas.toDataURL("image/png");
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        }
        var url = this.baseURL + "/activity/index.php";
        xhttp.open('POST', url, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        
        
        xhttp.send("action=saveScreenshot&student="+studentId+"&key="+key+  "&image="+activityPreview);
    }

    saveTempScreen(key, studentId){
        var activityPreview = this.screenShotTemp;
        //console.log("enviando ss...");
        //console.log(activityPreview);
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                //console.log(this.responseText);
            }
        }

        
        var url = this.baseURL + "/activity/index.php";
        xhttp.open('POST', url, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        
        
        xhttp.send("action=saveScreenshot&student="+studentId+"&key="+key+  "&image="+activityPreview);
    }
    setShowTip(type){
        this.showTip = true;
        this.tipType = type;
    }

    getResultData(){
        return this.resultData;
    }
    getResult(){
        return this.result;
    }
    
    setPause(pause){
        var cascade = false;
        if(pause != this.paused)
            cascade = true;

        this.paused = pause;

        if(cascade)
        if(this.currentInstruction < this.instructions.length)
            if(this.instructions[this.currentInstruction]!=null)
                this.instructions[this.currentInstruction].pause(pause);
    }
    checkForErrors(){
        
    }
    edit(){
        this.editing = true;
    }
    /**
     * Clean unreferenced data before export
     * @returns {undefined}
     */
    cleanData(){
        
    }
    
    resize(){
       
        var w = this.canvas.width;
        var h = this.canvas.height;
        
        if(h>w){
            var x = h;
            h = w;
            w = x;
                    
        }
        var scale = [w/this._editorSize[0], h/this._editorSize[1]];
        console.log("SCALE  screen> " + w+":"+h);
        console.log("scale: "+scale[0]+":"+scale[1]);
        var i,j;
        for(i=0; i < this.instructions.length; i++){
            var inst = this.instructions[i];
            inst.resize(scale);
            
            for(j=0; j < inst.stimulis.length; j++){
                inst.stimulis[j].resize(scale);
            }
        }
    }
    addNodes(xmlDoc, root, els, assoc){               
        if(!assoc){
            var i;
            for(i = 0; i < els.length; i++){
                
                var node = xmlDoc.createElement(els[i][0]);
                var newText=xmlDoc.createTextNode(els[i][1]);
                node.appendChild(newText);
                root.appendChild(node);
            }
        }
        else{
            
            for(var key in els){

                var node = xmlDoc.createElement(key);
                var newText=xmlDoc.createTextNode(els[key]);
                node.appendChild(newText);
                root.appendChild(node);
            }
        }
        
    }
    
    exportXML(){
        var parser = new DOMParser();
        var xml = parser.parseFromString('<?xml version="1.0" encoding="UTF-8"?><activity> </activity>', "text/xml");
        var info = xml.createElement('info');
        
        xml.childNodes[0].appendChild(info);
        this.addNodes(xml,info,this.info,true);
                
        var resources = xml.createElement('resources');        
        xml.childNodes[0].appendChild(resources);

        var code = xml.createElement('code');        
        xml.childNodes[0].appendChild(code);
        
        
        var stimuli_ids = [];
        var i;
        for (i = 0; i < this.instructions.length; i++){
            var inst_data = this.instructions[i].exportXML();
            
            var inst_xml = xml.createElement('instruction');  
             code.appendChild(inst_xml);
            ///add header
            this.addNodes(xml,inst_xml,inst_data['header'],true);
            
            
            //instruction data
            var inst_data_xml = xml.createElement('data'); 
            inst_xml.appendChild(inst_data_xml);
            this.addNodes(xml,inst_data_xml,inst_data['data'],true);
            
            
            //add images to data
            var j;//for each stimulus
            
            for(j =0; j < inst_data['stimuli'].length; j++){
                //exporta cada estimulo da instrucao e armazena no
                //array resources
                var s = inst_data['stimuli'][j];
      
                if(!stimuli_ids.includes(s.id)){
                    
                    var data = s.exportXML(xml);
                    if(s.type == 'image' || s.type=='audio')
                        stimuli_ids.push([s.type,s.id]);
                    
                    inst_data_xml.appendChild(data[0]);
                    this.addNodes(xml, data[0], data[1],true);
                }
            }
        }
        this.addNodes(xml, resources, stimuli_ids,false);
       
        
        return xml;
    }

    recomputeIndexes(){
        for(var i = 0; i < this.instructions.length; i++){
            this.instructions[i].position = i;   
             this.instructions[i].next = i+1;
             console.log(this.instructions[i].next);
            
         }
         this.instructions[this.instructions.length-1].next = -1;
         
    }
    
    saveActivity(dest,programId="",metaData=""){
         //recompute the instructions positions
        this.recomputeIndexes();

        var xml = this.exportXML();
        
        var xhttp = new XMLHttpRequest();
        
        var activity = this;
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
               
                if(this.responseText == "FILE_SAVE_PREVIEW_OK"){
                    window.open(activity.baseURL+"/activity/index.php?action=run&id=preview");
                }
                else if(this.responseText == "FILE_SAVE_OK"){
                    showModal("Salvo!","A atividade foi salva com sucesso!");
                }
                else if(this.responseText == "FILE_SAVE_AS_TEMPLATE_OK"){
                    showModal("Template Salvo!","A atividade foi salva como template com sucesso! Você pode utilizar este template ao criar nova ativiade.");
                }
                else if(this.responseText == "FILE_SAVE_AS_NEW_OK"){
                    showModal("Template Salvo!","A atividade foi salva como Nova atividade. Você pode encontrá-la em seu repositório! ");
                }
                else if(this.responseText == "FILE_SAVE_AUTO_OK"){
                    
                }
            }
        }
        

       

        ////get image...
        var activityPreview = this.canvas.toDataURL("image/png");//.replace("image/png", "image/octet-stream");
        
        if(dest == 'preview'){
            var url = this.baseURL + "/activity/index.php";
            xhttp.open('POST', url, true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            var s = new XMLSerializer();
            var newXmlStr = s.serializeToString(xml);
            xhttp.send("action=save&id=preview&xml=" +newXmlStr+"&image="+activityPreview+"&metadata="+metaData);
        }
        else if(dest == "asNew"){
            var url = this.baseURL + "/activity/index.php";
            xhttp.open('POST', url, true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            var s = new XMLSerializer();
            var newXmlStr = s.serializeToString(xml);
            xhttp.send("action=save&asNew=1&id="+this.id+"&xml=" +newXmlStr+"&image="+activityPreview+"&metadata="+metaData);
        }
        else if(dest == "asTemplate"){
            var url = this.baseURL + "/activity/index.php";
            xhttp.open('POST', url, true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            var s = new XMLSerializer();
            var newXmlStr = s.serializeToString(xml);
            xhttp.send("action=save&asTemplate=1&id="+this.id+"&xml=" +newXmlStr+"&image="+activityPreview+"&metadata="+metaData);
        }
        else if(dest == "save"){
            var url = this.baseURL + "/activity/index.php";
            xhttp.open('POST', url, true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            var s = new XMLSerializer();
            var newXmlStr = s.serializeToString(xml);
            xhttp.send("action=save&id="+this.id+"&xml=" +newXmlStr+"&image="+activityPreview+"&metadata="+metaData);
        }
        else if(dest=='auto'){
            console.log("SAVE AUTO...");
            var url = this.baseURL + "/activity/index.php";
            xhttp.open('POST', url, true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            var s = new XMLSerializer();
            var newXmlStr = s.serializeToString(xml);
            xhttp.send("action=save&asNew=1&id="+this.id+"&xml=" +newXmlStr+"&image="+activityPreview+"&auto=1&programId="+programId+"&metadata="+metaData);
        }
    }
    
    get_stimulus(id){
        var i;
        for(i=0; i < this.images.length; i++){
            if(this.images[i].id == id || this.audios[i].id == id){
                
                if(this.images[i].id == id)
                    return this.images[i];
                else if(this.audios[i].id == id){
                    return this.audios[i];
                }
            }
        }
        return null;
    }
    /**
     * Get a stimulus from database (image, audio or video).
     * @param {type} id
     * @param {type} type
     * @return {undefined}
     */
    addStimulus(id){       
        var i;
        for(i=0; i < this.images.length; i++){
            if(this.images[i].id == id || this.audios[i].id == id){
                //already loaded stimulus
                if(this.images[i].id == id)
                    return this.images[i];
                else if(this.audios[i].id == id){
                    return this.audios[i];
                }
            }
        }
        
        return this.getStimulus(id, this);
    }

    /**
     * Get stimulus data from database.
     * @param {type} id
     * @param {type} activity
     * @returns {undefined}
     */
    getStimulus(id, activity) {
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {

            if (this.readyState == 4 && this.status == 200) {
                
                if (this.responseText.length <= 0)
                {
                    alert("Erro carregando a atividade. Contate o administrador");
                } else {
                    if(this.responseText == "STIMULI_NOT_FOUND")
                    {
                        
                        
                        var img = document.createElement('img');
                        if(document.getElementById(id)!=null){
                            img =document.getElementById(id);
                            document.getElementById('runActivityData').appendChild(img);

                            activity.images[id] = document.getElementById(id);
                            activity.dataReceived[id] = 0;
                        }
                        else{
                            img.src = activity.baseURL + '/ui/Image-Not-Found1.png';
                            img.id = id;
                            document.getElementById('runActivityData').appendChild(img);

                            activity.images[id] = document.getElementById(id);
                            activity.dataReceived[id] = 0;
                        }
                        return img;
                    }
                    
                    //console.log(this.responseText);
                    var obj = JSON.parse(this.responseText, true);
                
                    if(obj.length<=0){
                        console.log("nao achou");
                    }
                    if(obj['type'] == 'image'){
                        
                        var img = document.createElement('img');
                        img.src = obj['data'];
                        img.id = obj['id'];
                        //Meta data
                        //databaseID, name, owner_id, description, type, version
                        img.setAttribute('data-databaseid', obj['id']);
                        img.setAttribute('data-name', obj['name']);
                        img.setAttribute('data-owner_id', obj['owner_id']);
                        img.setAttribute('data-description', obj['description']);
                        img.setAttribute('data-type', obj['type']);
                        img.setAttribute('data-version', obj['version']);
                        
                        
                        document.getElementById('runActivityData').appendChild(img);
                        activity.images[obj['id']] = document.getElementById(obj['id']);
                        activity.dataReceived[obj['id']] = 0;
                        
                        return img;
                        
                    }
                    else if(obj['type'] == 'audio'){
                        
                        var audio = document.createElement('audio');
                        
                        var source = document.createElement('source');
                        
                        source.src = activity.baseURL + "/" + obj['url'];
                        audio.id = obj['id'];
                        audio.appendChild(source);
                        //Meta data
                        //databaseID, name, owner_id, description, type, version
                        audio.setAttribute('data-databaseID', obj['id']);
                        audio.setAttribute('data-name', obj['name']);
                        audio.setAttribute('data-owner_id', obj['owner_id']);
                        audio.setAttribute('data-description', obj['description']);
                        audio.setAttribute('data-type', obj['type']);
                        audio.setAttribute('data-version', obj['version']);
                        
                        document.getElementById('runActivityData').appendChild(audio);
                        activity.audios[obj['id']] = document.getElementById(obj['id']);
                        activity.dataReceived[obj['id']] = 0;
                        return audio;
                    }                                        
                }
            }
        };
        var url = "../utils/GetData.php?type=stimuli&id=" + id;

        if(document.getElementById(id)!=null && !this.editing){
            
            url+="&only_db=1"
        }
        
        xhttp.open("GET",url , true);
        xhttp.send();
        
      
    }



    proccessXML(content) {
        var parser = new DOMParser();
        var xmlDoc = parser.parseFromString(content, "text/xml");
        ///read data
        var i;
        //info...
        var info = xmlDoc.getElementsByTagName("info")[0];
        
        for (i = 0; i < info.childNodes.length; i++) {
            var el = info.childNodes[i];

            if (el.nodeType == 1)//element
            {
                var key = el.nodeName;
                var value = "";
                if (el.childNodes.length > 0) {
                    value = el.childNodes[0].nodeValue;
                }
                
                this.info[key] = value;
            }
        }


        //resources
        var res = xmlDoc.getElementsByTagName("resources")[0];

        for (i = 0; i < res.childNodes.length; i++) {
            var el = res.childNodes[i];
            
            if (el.nodeType == 1)//element
            {
                var key = el.nodeName;
                var value = "";
                if (el.childNodes.length > 0) {
                    value = el.childNodes[0].nodeValue;
                }
                

                this.dataReceived[value] = 1;
                this.getStimulus(value, this);
                
            }
        }
        
       

        var inst = xmlDoc.getElementsByTagName("instruction");

        ////////Create a new instruction.
        for (i = 0; i < inst.length; i++)
        {
            //data that will be passed to the instruciton
            var data = [];
            
            var k;
            var sons = inst[i].childNodes;
            
            for (k = 0; k < sons.length; k++){
                var el = sons[k];
                
                if (el.nodeType == 1)//element
                {
                    var key = el.nodeName;
                    
                    var value = "";
                    if (el.childNodes.length > 0) {
                        value = el.childNodes[0].nodeValue;

                        data[key] = value;
                    }
                }
                
            }
        
        
         
         
            
            //put all data into an array which will be passed as parameter to instruction
            data['images'] = [];
            data['audios'] = [];
            data['videos'] = [];
            data['texts'] = [];
            data['textInputs'] = [];
            data['camCapture'] = [];
            //TODO: video
            //extract data
            var childs = inst[i].getElementsByTagName("data")[0].childNodes;
            var j;
            for (j = 0; j < childs.length; j++) {
                var el = childs[j];
                if (el.nodeType == 1)//element
                {
                    var key = el.nodeName;
                    if(key =="image")
                    {
                        var r = this.parseStimuliDataFromXML(el);                        
                        data['images'].push(r);
                    }
                    else if(key=="audio"){
                        var r = this.parseStimuliDataFromXML(el);
                        data['audios'].push(r);
                    }
                    else if(key=="video"){
                        
                        var r = this.parseStimuliDataFromXML(el);
                        data['videos'].push(r);
                    }
                    else if(key=="text"){
                        
                        var r = this.parseStimuliDataFromXML(el);
                        data['texts'].push(r);
                    }
                    else if(key=="textInput"){
                        
                        var r = this.parseStimuliDataFromXML(el);
                        data['textInputs'].push(r);
                    }
                    else if(key=="camCapture"){
                        
                        var r = this.parseStimuliDataFromXML(el);
                        data['camCapture'].push(r);
                    }
                    else
                    {
                        var value = "";
                        if (el.childNodes.length > 0) {
                            value = el.childNodes[0].nodeValue;

                            data[key] = value;
                        }
                    }
                }
            }
            
            //TODO: not safe?
            var newInst = eval("new " + data['type'] + "(data,this)");
            
            newInst.activity = this;
            this.instructions.push(newInst);
        }
        
    }
    isDone(){
        return this.done;
    }
    parseStimuliDataFromXML(xml){
        var data = [];
        var childs = xml.childNodes;
        var j;
      
        for (j = 0; j < childs.length; j++) {
           var el = childs[j];
           if (el.nodeType == 1)//element
            {
               var key = el.nodeName;
               var value = "";
               if (el.childNodes.length > 0) {
                   value = el.childNodes[0].nodeValue;
                   data[key] = value;
               }

           }
       }
       return data;
    }
    isReady(){        
        if(this.ready){            
            return true;
        }
        else
        {
            var i =0;                        
            for (var key in this.dataReceived) {                            
                if(this.dataReceived[key] != 0)
                    return false;
            }            
            this.ready = true;
        }
        return this.ready;
    }
    startRunning() {
        this.running = true;
        this.paused = false;
        if(this.instructions[this.currentInstruction] == null){
            alert("Problema na atividade "+ this.id);
        }
        this.instructions[this.currentInstruction].startRunning();
    }
    isRunning() {
        return this.running;
    }
    /**
     * Updates the activity according to a dt elapsed time.
     * @param {type} dt
     * @returns {undefined}
     */
    update(dt) {
        if(!this.isReady())
            return;
        this.wasClick = false;
       
        if (this.isRunning()) {
            this.instructions[this.currentInstruction].update(dt);
            if (this.instructions[this.currentInstruction].isDone())
            {
                this.currentInstruction = parseInt(this.instructions[this.currentInstruction].getNext());// + 1;    
                if ( this.currentInstruction === undefined || isNaN(this.currentInstruction) || this.currentInstruction == -1 || this.currentInstruction >= this.instructions.length) {
                    this.running = false;
                    this.done = true;
                }
                else
                {
                    this.instructions[this.currentInstruction].startRunning();
                }
            }
        }
    }
    /**
     * Render screen to canvas context ctx. The scale is according to the 
     * original resolution the activity was created.
     * @param {type} ctx
     * @param {type} scale
     * @returns None
     */
    render(ctx, scale) {
        ctx.clearRect(0, 0, this.htmlCanvas.width, this.htmlCanvas.height);
       
        if(!this.ready){
            ctx.font = "30px Arial";
            ctx.fillStyle = "#000000";
            ctx.fillText("Carregando", 10, 50);
            return;
        }
        if (this.running) {
            this.instructions[this.currentInstruction].render(ctx, scale);
        } else {
            ctx.font = "30px Arial";
            ctx.fillStyle = "#000000";            
        }
    }
    
    renderPreview(ctx, scale){
        ctx.clearRect(0, 0, this.htmlCanvas.width, this.htmlCanvas.height);

        //horizontal lines
        ctx.setLineDash([5]);
        var bkp_stroke = ctx.strokeStyle;

        ctx.beginPath();
        ctx.strokeStyle = "#bbbdbc";
        
         
        
        for (var i = 0; i < 600; i+= 100){
            ctx.moveTo(0, i);
            ctx.lineTo(800, i);
        }
        //vertical lines
        for (var i = 0; i <= 800; i+=100){
            ctx.moveTo(i, 0);
            ctx.lineTo(i, 800);
        }
        ctx.stroke();
        ctx.strokeStyle = bkp_stroke;
        ctx.setLineDash([]);
        this.instructions[this.instructionBeingEdited].renderPreview(ctx, scale);
    }
    
    getMousePos(evt) {
        var mouse_evs = new Array('mousemove','mousedown','mouseup');
        var touch_evs = new Array('touchstart','touchend','touchmove','touchcancel');
        
        if(mouse_evs.includes(evt.type))
        {
            var rect = this.canvas.getBoundingClientRect();
            return [evt.clientX - rect.left, evt.clientY - rect.top];
        }
        else if(touch_evs.includes(evt.type)){
            var rect = this.canvas.getBoundingClientRect();
            if(evt.touches.length >0){
                var clientX = evt.touches[0].clientX;
                var clientY = evt.touches[0].clientY;
                return [clientX - rect.left, clientY - rect.top];
            }
            return [-1,-1];
        
        }
    }
    /**
     * Mouse or touch click.
     * @returns {undefined}
     */
    pointerDown(evt) {        
        this.dragging = true;
        
        this.lastPointerPos = this.getMousePos(evt);
        
        if(this.editing)
        {
            this.instructions[this.instructionBeingEdited].editPointerDown(evt);
            return;
        }
        if ((this.running) && (!this.done) && (this.currentInstruction < this.instructions.length))
        {
            this.instructions[this.currentInstruction].pointerDown(evt);
        }
    }

    /**
     * Mouse or touch up.
     * @param {type} evt
     * @returns {undefined}
     */
    pointerUp(evt) { 
        
        evt.stopPropagation();
        
        var l_pos = this.lastPointerPos;
        this.lastPointerPos = this.getMousePos(evt);
        this.pointerMovement[0] = this.lastPointerPos[0] - l_pos[0];
        this.pointerMovement[1] = this.lastPointerPos[1] - l_pos[1];
        this.wasClick = true;
        this.dragging = false;
        if(this.editing)
        {
            
            this.instructions[this.instructionBeingEdited].editPointerUp(evt);
            return;
        }
        if ((this.running) && (!this.done) && (this.currentInstruction < this.instructions.length))
        {
            this.instructions[this.currentInstruction].pointerUp(evt);
        }
    }

    /**
     * Mouse or finger move
     * @returns {undefined}
     */
    pointerMove(evt) {
        
        var l_pos = this.lastPointerPos;
        this.lastPointerPos = this.getMousePos(evt);
        this.pointerMovement[0] = this.lastPointerPos[0] - l_pos[0];
        this.pointerMovement[1] = this.lastPointerPos[1] - l_pos[1];
        if(this.editing)
        {
            
            if(this.dragging){
               
                this.instructions[this.instructionBeingEdited].editPointerDrag(evt);
                return;
            }
            else{
                this.instructions[this.instructionBeingEdited].editPointerMove(evt);
                return;
            }
                
        }
        if ((this.running) && (!this.done) && (this.currentInstruction < this.instructions.length))
        {
            if(this.dragging)
                this.instructions[this.currentInstruction].pointerDrag(evt);
            else
                this.instructions[this.currentInstruction].pointerMove(evt);
        }
    }
    
    removeStimulus(stimulus, inst){
         window[this.removeStimulusCallback](stimulus, inst);
    }
    configureStimulus(stimulus, inst){
         window[this.configureStimulusCallback](stimulus, inst);
    }
}


