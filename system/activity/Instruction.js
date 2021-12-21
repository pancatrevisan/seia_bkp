class Instruction{
    
    /**
     * Sets an editable attribute value.
     * @param {type} attribute Attribute name
     * @param {type} newValue attribute value; if image or audio or video, the ID
     * @param {type} type type. Is it an image? a video? an audio?
     * @returns {nothing}
     */
    setAttributeValue(attributeDescriptor, newValue, params=null){
        console.log('set attr value: '+attributeDescriptor.attributeName + " val: " + newValue + " type> " + attributeDescriptor.attributeTypes[0]);
        if(params == null){
            throw new Error("params must be filled");
            return null;
        }
        
        if(params['type'] == 'boolean' || params['type'] == 'integer' || params['type'] == 'stimulusID' || params['type'] == 'string'){
            this[attributeDescriptor.attributeName] = newValue;
            return newValue;
        }
        else{
            return this.addStimulus(attributeDescriptor, newValue, params);
        }     
    }


    
    
    pause(pause){
        
    }
    
    removeStimuli(stimuli){
        
        var i;
        for(i= 0; i < this.stimulis.length; i++){
            if(this.stimulis[i].localID==stimuli ){
                var s = this.stimulis[i];
                this.stimulis.splice(i,1);
                delete this.idStimulis[stimuli];
                return s;
            }
        }
        
    }
    addStimulus(attributeDescriptor, newValue, params=[]){
        this.activity.addStimulus(newValue); //load file
        
        var htmlObject =document.getElementById(newValue); ; 
        var stimulus = null;
        
        if(params['type']=="image"){
            console.log(params);
            var emotionDescriptor = null;
            if("emotionDescriptor" in params){
                emotionDescriptor = params['emotionDescriptor'];
            }
            console.log("add image");
            var size = [htmlObject.width, htmlObject.height];
            var pos = [10,10];   
            var fullContainer = false;

            "fullContainer" in params? fullContainer = params['fullContainer']=="true": fullContainer = false;
            console.log("fullContainer? " + fullContainer);
            
            stimulus  = new ImageStimulus(htmlObject.id, Date.now(),true, true, this.activity, this,
                size, pos,   true, null,fullContainer,emotionDescriptor);
        }
        else if(params['type']=="audio"){
            var emotionDescriptor = null;
            if("emotionDescriptor" in params){
                emotionDescriptor = params['emotionDescriptor'];
            }
            stimulus = new AudioStimulus(htmlObject.id, Date.now(),true, true, this.activity, this,true, false, 0, emotionDescriptor);
        }
        else if(params['type']=="text"){
            console.log("add text");
            var emotionDescriptor = null;
            if("emotionDescriptor" in params){
                emotionDescriptor = params['emotionDescriptor'];
            }
            stimulus = new TextStimulus(Date.now(), this.activity, this,newValue, [10, 10], 24,"#FF0000", emotionDescriptor);
        }
        else if(params['type']=="textInput"){
            throw new Error("addStimulus textInput");
        }
        else if(params['type']=="video"){
            //throw new Error("addStimulus video");
            
            stimulus = new YoutubeVideo(Date.now(), this.activity, this,null);
        }
        else{
            throw new Error("Unknown type");
        }
        
        //if(attributeDescriptor.destination == null){
            this.stimulis.push(stimulus);
            this.idStimulis[stimulus.localID] = stimulus;
        
        
            if(attributeDescriptor.attributeEditType=='swap'){
                var r_img  = this.removeStimuli(this[attributeDescriptor.attributeName],'image');
                if(r_img!=null){
                    stimulus.renderImage.position = [r_img.renderImage.position[0], r_img.renderImage.position[1]];
                }
                this[attributeDescriptor.attributeName] = stimulus.localID;
            }
        //}
        if(attributeDescriptor.destination != null){
            if(attributeDescriptor.attributeEditType=='swap'){
                this[attributeDescriptor.destination] = stimulus;
            }
            else if(attributeDescriptor.attributeEditType=='add/remove'){
                this[attributeDescriptor.destination].push(stimulus);
            }
        }
        if(attributeDescriptor.attributeValues!=null){
            for(var key in attributeDescriptor.attributeValues)
            {
                
                stimulus[key] = attributeDescriptor.attributeValues[key];
                console.log("set "+key+" val: " + stimulus[key]);
            }
        }
        return stimulus;        
    }
    
    editPointerUp(evt){        
        if(this.imageBeingEdited!=null){
            
            this.imageBeingEdited.editPointerUp(evt);
            this.imageBeingEdited.canDrag = false;
        }
    }
    checkErrors(){
        throw new Error('You have to implement the checkErrors method!');
    }
    editPointerDown(evt){
        throw new Error('You have to implement the editMouseDown method!');
    }
    
    editPointerMove(evt){
        throw new Error('You have to implement the editMouseDrag method!');
    }
    editPointerDrag(evt){
        if(this.imageBeingEdited!=null){
            this.imageBeingEdited.editPointerDrag();
        }
    }
    isEditing(){
        return this.activity.editing;
    }
    
    clickImage(){
        var i;
        
        this.selectedImage = null;
        for (i = this.stimulis.length-1; i>=0; i=i-1){
            var image = this.stimulis[i].renderImage;
            if(image!=null){
                image.canDrag = false; 
                //image.beingEdited = false;
            }
        }
        for (i = this.stimulis.length-1; i>=0; i=i-1){
            var image = this.stimulis[i].renderImage;
            if(image!=null){
                
                image.editPointerDown();
                if(image.wasPointed()){
                    this.selectedImage = image;
                    if(!image.hasButtonClick){
                        image.canDrag = true;
                        console.log('dvria mxr');
                    }
                    //image.beingEdited = true;
                    return;
                }
            }
        }
    }
    //check for clicks on stimulus; if clicked, activate edition.
    clickEditImage(){
        var i;
        
        this.imageBeingEdited = null;
        for (i = this.stimulis.length-1; i>=0; i=i-1){
            var image = this.stimulis[i].renderImage;
            if(image!=null){
                image.canDrag = false; 
                image.beingEdited = false;
            }
        }
        for (i = this.stimulis.length-1; i>=0; i=i-1){
            var image = this.stimulis[i].renderImage;
            if(image!=null){
                
                image.editPointerDown();
                if(image.wasPointed()){
                    this.imageBeingEdited = image;
                    if(!image.hasButtonClick){
                        image.canDrag = true; 
                    }
                    image.beingEdited = true;
                    return;
                }
            }
        }
        
    }
    
    getAttributeDescriptor(name){
        var i;
        for(i = 0; i < this.editableAttributes.length; i++){
            if(this.editableAttributes[i].attributeName==name)
                return this.editableAttributes[i];
        }
        return null;
    }
    exportStimulus(s, local=false){
        if(s==null){
            return {type:"", value: ""};
        }
        if(s.type == "image"){
            if(!local)
                return {type:"image", value: s.id};
            else
                return {type:"image", value: s.localID};
        }
        else if(s.type == "text"){
            
            return {type:"text", value: s.text,color:s.fontColor};
            
        }
        else if(s.type == "audio"){
            if(!local)
                return {type:"audio", value: s.id};
            else
            return {type:"audio", value: s.localID};
        }
        else if(s.type == "video"){
            if(!local)
                return {type:"video", value: s.videoId};
            else 
            return {type:"video", value: s.localID};
        }
    }
    constructor(data=null, activity=null){
        this.description = "";
        this.allowUse = false;
        this.running = false;
        this.done = false;
        
        this.activity = activity;
        this.selectedImage = null;
        //stimuli collection
        this.stimulis = [];
        this.idStimulis = [];

        
        /**
         * Which configuration the user can set in the stimuli?
         */
        this.imageConfiguratinsAllowed = [];
        this.audioConfiguratinsAllowed = [];
        
        
        this.editable = false;
        
        
        this.canDragImages = false;
        
        //colocar aqui as configuracoes para o editor, cada botao ativa uma 
        //maneira de editar.
        this.editableAttributes =[];
        
        this.imageBeingEdited = null;
       
        this.ignoreInLocalSearch = [];
        

        
        this.type = data['type'];
        this.position = data['position'];
        this.description = "description" in data? data['description'] : "";
        this.editable  = data['editable'];

        this.next = data['next'];
        if(this.next==null)
            this.next = -1;
        if(this.next === undefined)
            this.next = -1;
        if("editable" in data){
            this.editable = data['editable'] == "true";                
        }
        
        var i;
        if( 'images' in data)
        for( i = 0; i < data['images'].length; i++){
            var img = data['images'][i];
            var clickable = false;
            
            if("isClickable" in img){
                clickable = img['isClickable'] == "true";
                
            }
            
            var draggable = false;
            if("isDraggable" in img){
                draggable = data['isDraggable'] == "true";
            }
            
            var localID = null;
            if("localID" in img){
                localID = img['localID'];
            }
            
            var dragAndAssociate = false;
            if("dragAndAssociate" in img){
                dragAndAssociate = img['dragAndAssociate'];
                this.dragAndAssociate = data['dragAndAssociate'] == "true";
            }
            var containerID = null;
            if("containerID" in img){
                containerID = img['containerID'];
                this.containerID = data['containerID'];
            }

            var fullContainer;

            "fullContainer" in img? fullContainer = img['fullContainer'] == "true": fullContainer = false;
           
            var size = [parseInt(img['imageWidth']),parseInt(img['imageHeight'])];
            var pos = [parseInt(img['imageX']),parseInt(img['imageY'])];
            
            var emotionDescriptor = null;
            if("emotionDescriptor" in img){
                emotionDescriptor = img['emotionDescriptor'];
            }

            var stimuli = new ImageStimulus(img['imageID'], localID,clickable, draggable,
                this.activity,this,size, pos, dragAndAssociate,containerID,fullContainer,emotionDescriptor);
            
            
            this.stimulis.push(stimuli);
            this.idStimulis[stimuli.localID] = stimuli;        
        }
        if( 'audios' in data)
        for( i = 0; i < data['audios'].length; i++){
            console.log("criando audio novo...");
            var audio = data['audios'][i];
            var pos = [parseInt(audio['imageX']),parseInt(audio['imageY'])];
            
            var audioID = null;
            if("audioID" in audio){
                audioID = audio['audioID'];
            }
            
            var isClickable = false;
            if("isClickable" in audio){
                isClickable = audio['isClickable'] == "true";    
            }
            var isDraggable = false;
            if("isDraggable" in audio){
                isDraggable = audio['isDraggable'];
            }
            var localID = null;
            if("localID" in audio){
                localID = audio['localID'];
            }
            var autoplay = ("autoplay" in audio)? audio['autoplay']: false;
            var repeat = ("repeat" in audio)? audio['repeat']: false;
            var numberOfRepeats = ("numberOfRepeats" in audio)? parseInt(audio['numberOfRepeats']): 0;
            var renderIcon = ("renderIcon" in audio)? audio['renderIcon']: false;
            
            var emotionDescriptor = null;
            if("emotionDescriptor" in audio){
                emotionDescriptor = audio['emotionDescriptor'];
            }
            
            var audio_stimulus = new AudioStimulus(audioID, localID,isClickable, isDraggable, 
            this.activity, this, autoplay, repeat, numberOfRepeats, renderIcon, pos, emotionDescriptor);// data['audios'][i];
            this.stimulis.push(audio_stimulus);
            this.idStimulis[audio_stimulus.localID] = audio_stimulus;        
        }
        if( 'videos' in data)
        for( i = 0; i < data['videos'].length; i++){
            var video = data['videos'][i];
            
            var clickable = video['isClickable'] == "true";
                
            var draggable = video['isDraggable'] == "true";
            
            var localID =  video['localID'];
            
            var pos = [parseInt(video['posX']),parseInt(video['posY'])];
            var size = [parseInt(video['sizeX']),parseInt(video['sizeY'])];
            var url = video['url'];
            console.log("ACTIVITY ");
            console.log(this.activity);
            console.log("url: "+url);
            var videoStimulus = new YoutubeVideo(localID, this.activity, this, url,size,pos);
            this.stimulis.push(videoStimulus);
            this.idStimulis[videoStimulus.localID] = videoStimulus;        
        }
        if( 'texts' in data)
        for( i = 0; i < data['texts'].length; i++){
            
            var text = data['texts'][i];
            var text_val = text['text'];
            var pos = [parseInt(text['textX']),parseInt(text['textY'])];
            var fontSize = parseInt(text['fontSize']);    
            var fontColor = text['fontColor'];
            var localID = text['localID'];

            var emotionDescriptor = null;
            if("emotionDescriptor" in text){
                emotionDescriptor = text['emotionDescriptor'];
            }

            var textStimulus = new TextStimulus(localID, this.activity, this, text_val, pos, fontSize, fontColor,emotionDescriptor);
            
            this.stimulis.push(textStimulus);
            this.idStimulis[textStimulus.localID] = textStimulus;        
        }
        
        //////TODO: for text inputs
        if( 'textInputs' in data)
        for( i = 0; i < data['textInputs'].length; i++){
            console.log("new textInput man");
            var text = data['textInputs'][i];
            var pos = [parseInt(text['posX']),parseInt(text['posY'])];
            var size = [parseInt(text['sizeX']),parseInt(text['sizeY'])];
            var localID = text['localID'];
            var fontSize = parseInt(text['fontSize']);    
            var fontColor = text['fontColor'];
            var fontFamily = text['fontFamily'];
            var numberOfLines = text['numberOfLines'];
            var numberOfColumns = text['numberOfColumns'];
            var text_val = text['text'];
            var textInput = new TextInputStimulus(localID, this.activity, this, text_val, pos, size,
                 fontSize, fontColor, fontFamily, numberOfLines, numberOfColumns);
            
            this.stimulis.push(textInput);
            this.idStimulis[textInput.localID] = textInput;
            
        }


        ////FOR CAM CAPTURE
        if( 'camCapture' in data)
        for( i = 0; i < data['camCapture'].length; i++){
            console.log("new camCapture man");
            var cc = data['camCapture'][i];

            var pos = [parseInt(cc['posX']),parseInt(cc['posY'])];
            var size = [parseInt(cc['sizeX']),parseInt(cc['sizeY'])];
            var localID = cc['localID'];
            
            var camCapture = new CamCaptureStimulus(localID, this.activity, this,  pos, size );
            
            this.stimulis.push(camCapture);
            this.idStimulis[camCapture.localID] = camCapture;
            
        }
        
        
    }
    removeStimulus(stimulus){
        
        this.activity.removeStimulus(stimulus,this);        
    }
    configureStimulus(stimulus){
        this.activity.configureStimulus(stimulus,this);        
    }
    configureImage(img){
        console.log("configure " + img);
        this.activity.configureImage(img,this);        
    }
    getNext(){
        return this.next;
    }
    startRunning(){
        this.running = true;
    }
    terminate(){
        this.done = true;
    }
    
    isDone(){
        return this.done;
    }
    
    update(dt){
        throw new Error('You have to implement the run method!');
    }
    
    exportXML(){
        throw new Error('You have to implement the exportXML method!');
    }
    /*
     * 
     * @param {type} ctx canvas context
     * 
     */
    render(ctx, scale){
        
        throw new Error('You have to implement the render method!');
    }
    
    
    renderPreview(ctx, scale){
        throw new Error('You have to implement the renderPreview method!');
    }
    
    /**
     * The instruction receives a mouse down or touch event
     * @param {type} evt
     * @returns {undefined}
     */
    pointerDown(evt) {
       
    
    }

    /**
     * The instruction receives a mouse up or touch up event
     * @param {type} evt
     * @returns {undefined}
     */
    pointerUp(evt) {
       
       
    }

    /**
     * Mouse or finger move
     * @returns {undefined}
     */
    pointerMove(evt) {
                
    }
    pointerDrag(evt){
        
    }
    /**
     * 
     * @returns {Returns a HTML body to edit the instruction}
     */
    edit(){
        throw new Error('You have to implement the edit method!');
    }
    /**
     * 
     * @returns {A HTML to draw the command block.}
     */
    commandBlock(){
        throw new Error('You have to implement the commandBlock method!');
    }
    
    /**
     * 
     * @returns {A HTML to draw the command block.}
     */
    resize(scale){
        throw new Error('You have to implement the resize method!');
    }
}

