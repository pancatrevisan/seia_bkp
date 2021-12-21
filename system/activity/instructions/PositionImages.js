class PositionImages extends Instruction{   
    
    
    resize(scale){
        
        for(var key in this.originalPositions)
        {
          var value = this.originalPositions[key];
          
          value[0] = value[0]*scale[0];
          value[1] = value[1]*scale[1];
          this.originalPositions[key] = value;
         }
         
    }
    
    constructor(data={'type':'PositionImages','position':-1,'editable':true,'next':-1}){
        
        super(data);
         // ADD
         this.possibleConteinersList = [];
         
        this.editable = true;
        this.positions = [];  
        this.colors = ["#ff0000", "#fffe00", "#02fe00", "#0201ff","#9f01ff","#f175ff"];
        this.editableAttributes.push(
                new AttributeDescriptor("images",['image'],true,"Adicionar Estímulo",'add/remove',null,null,null,null,{'dragAndAssociate':true}),
                new AttributeDescriptor("images",['image'],true,"Adicionar conteiner de imagens",'add/remove',null,null,null,'positions',{'isContainer':true, 'dragAndAssociate':false}),
                new AttributeDescriptor("audioInstruction", ['audio'], false, "Instrução (áudio)", 'swap'),
                new AttributeDescriptor("moreThanOneStimulusInContainer",['boolean'],false,"Mais de uma imagem por conteiner?",'swap')/*,
                new AttributeDescriptor("allowWrongStimuliInContainer",['boolean'],false,"Permitir colocar imagem em container errado?",'swap')
                        */
                );
        this.allowUse = true;

        this.description = "Permite arrastar e agrupar imagens.";
        if(data == null)
            return;
        var i = 0;
        'finishButton' in data? this.finishButton = data['finishButton']: this.finishButton = null;
        
        'audioInstruction' in data? this.audioInstruction = data['audioInstruction']: this.audioInstruction = null;
        
        if(this.finishButton!=null){
            if(this.idStimulis[this.finishButton])
            this.idStimulis[this.finishButton].canRemove =false;
        }
        
        this.audioInstruction in this.idStimulis? this.audioInstruction=this.audioInstruction: this.audioInstruction = null;
        

            this.removeStimuli(this.finishButton);
        while(('positions'+i) in data){
            var posID = data[('positions'+i)];    
            this.positions.push(this.idStimulis[posID]);
            this.idStimulis[posID].isDraggable = false;
            this.idStimulis[posID].isClickable = false;
            i++;
        }
        this.moreThanOneStimulusInContainer = true;
        
        'moreThanOneStimulusInContainer' in data? this.moreThanOneStimulusInContainer = 
                data['moreThanOneStimulusInContainer']=='true' : this.moreThanOneStimulusInContainer = true;
        
        this.allowWrongStimuliInContainer = true;
        
        
        
        this.originalPositions = [];
        this.numContainers = this.positions.length ;
        this.insertedImages = 0;
        this.containers = [];
        this.positions_id = [];
        for (i = 0; i < this.numContainers; i++){
            
            this.containers[this.positions[i].localID] = [];
            this.positions_id[this.positions[i].localID] = this.positions[i];
        }
        
        this.content = [];
        this.triedToPutInWrongContainer = false;
        
        for (i = 0; i < this.stimulis.length; i++){
            this.originalPositions[this.stimulis[i].localID] =[ 
                        this.stimulis[i].renderImage.position[0],this.stimulis[i].renderImage.position[1] ];
                    
            if(!this.positions.includes(this.stimulis[i]) && this.stimulis[i].type!="audio"){
                this.stimulis[i].dragAndAssociate = true;
                this.stimulis[i].isDraggable = true;
                
                this.content.push(this.stimulis[i]);
            }
            else{
                this.stimulis[i].isContainer = true;
            }
        }
        console.log(this.idStimulis);

        this.screenshotTimer = 0.0;
        
    }
    returnStimuliToOriginalPosition(stimulus){

    }
    removeStimuli(stimuli){
        console.log("remove stimuli "+stimuli);
        super.removeStimuli(stimuli); var i;
        for(i= 0; i < this.positions.length; i++){
            if(this.positions[i].localID==stimuli ){
                this.positions.splice(i,1);
            }
        }   
    }
 
    exportXML(){
        var exp =[];
        exp['stimuli']   = [];
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="PositionImages";
        exp['header']['position']=this.position;
        exp['header']['next']= this.next;
        exp['header']['description']=this.description;
        exp['header']['editable'] = this.editable;
        var i;
        for(i=0; i < this.positions.length; i++){
            exp['data']['positions'+i] = this.positions[i].localID;
        }
        exp['data']['moreThanOneStimulusInContainer'] = this.moreThanOneStimulusInContainer;
        exp['data']['allowWrongStimuliInContainer'] = this.allowWrongStimuliInContainer;
        exp['data']['audioInstruction'] = this.audioInstruction;
        exp['data']['finishButton'] = this.finishButton;
        exp['stimuli'] = this.stimulis;
        
        return exp;
    }
    
    terminate(){
        
        this.done=true;
        var ret = {};
        ret['type'] = "association";
        ret['save_screenshot'] = true;
        
        if(this.audioInstruction!=null)
            this.idStimulis[this.audioInstruction].stop();

        ret['keys'] = [];
        this.activity.result = this.activity.RESULT_CORRECT;
        console.log(this.idStimulis);
        for (var key in this.containers){
            var db_key = this.idStimulis[key].id;
            
            var obj = {};
            obj['model'] = this.exportStimulus(this.idStimulis[key]);
            obj['correct'] = [];

            obj['inserted'] = [];
            for (var j =0; j < this.containers[key].length; j++)
            {
                
                obj['inserted'].push(this.exportStimulus(this.containers[key][j]));
                if(this.containers[key][j].containerID!= key){
                    this.activity.result = this.activity.RESULT_WRONG;
                    
                }
                else{
                    obj['correct'].push(this.exportStimulus(this.containers[key][j]));
                }
                
            }
            ret['keys'].push(obj);
        }

        if(this.activity.result==this.activity.RESULT_CORRECT && this.triedToPutInWrongContainer){
            this.activity.result=this.activity.RESULT_CORRECT_TIP;
        }
        if(this.activity.showTip && this.activity.result==this.activity.RESULT_CORRECT){
            this.activity.result=this.activity.RESULT_CORRECT_TIP;
        }

        console.log(ret);
        this.activity.resultData = JSON.stringify(ret);

        
        
    }
    
   
    
    putImageInsideContainer(){
   
        
         //add
         var image = this.selectedImage.stimulus;
         

        //ADD
        if(this.possibleConteinersList.length <= 0){
            this. checkDraggingStimuli();
        }
        
        if(this.possibleConteinersList.length > 1){ //retorna a imagem para posicao inicial.
           
            image.renderImage.position = 
                    [this.originalPositions[image.localID][0],this.originalPositions[image.localID][1]];
            this.selectedImage = null;
            
            return;
        }
        if(this.possibleConteinersList.length <= 0)
            return;
        var container = this.possibleConteinersList[0];
             //pode colocar mais de um estímulo em cada conteiner?
        if(this.containers[container].length >= 1 && !this.moreThanOneStimulusInContainer){
            image.renderImage.position = 
                    [this.originalPositions[image.localID][0],this.originalPositions[image.localID][1]];
            this.selectedImage = null;
            
            return;
        }

        //pode adicionar no conteiner?        
        if(this.idStimulis[container].fullContainer){
            image.renderImage.position = 
                    [this.originalPositions[image.localID][0],this.originalPositions[image.localID][1]];
            this.selectedImage = null;
            
            return;
        }
            
        
       
        this.containers[container].push(image);
        image.isDraggable = false;//permitir reposicionar
        var i;
        for(i=0;i<this.content.length; i++){
            if(this.content[i].localID == image.localID){
                this.content.splice(i,1);
            }
        }
        /*if(this.content.length == 0){
            this.done= true;
        }*/
        this.selectedImage = null;
        
    }
    
    //verifica se um estimulo está em um container e o remove;
    removeStimulusFromContainer(stimulus){
        console.log(this.containers);
        for(var key in this.containers){
           for (var j =0; j < this.containers[key]; i++){
               if(this.containers[key][j].localID == stimulus.localID){
                   //remover 
               }
           }
        }
    }


    update(dt){
        this.screenshotTimer += dt;
        if(this.content.length<= 0)
        {
            this.terminate();
        }

        if(this.audioInstruction != null){
                
            if(!this.idStimulis[this.audioInstruction].played){
                this.idStimulis[this.audioInstruction].play();
            }
        }

        if (this.activity.showTip) {
            for (var i =0; i < this.positions.length; i++){
                this.positions[i].renderImage.borderColor = this.colors[i];
            }
            for(var i =0; i < this.content.length; i++){
                this.content[i].renderImage.borderColor = this.positions_id[this.content[i].containerID].renderImage.borderColor;
            }
        }
        this.checkDraggingStimuli();
        if(this.selectedImage == null)
            return;
        if(!this.selectedImage.stimulus.isDraggable){
            this.selectedImage = null;
            return;
        }
        
        
        
    }
    renderPreview(ctx, scale=1){
        var i;       
        for(i=0;i<this.stimulis.length; i++)
        {
            var image = this.stimulis[i];
            if(this.positions.includes(image)){
                
                image.renderImage.borderColor = "#000000";
                ctx.setLineDash([5, 15]);
                image.render(ctx, scale);
                ctx.setLineDash([]);
            }
            else{
                image.render(ctx, scale);        
            }
        }
    }
    //ADD
    //ADD
    checkDraggingStimuli(){
        if(this.selectedImage == null){
            return;
        }
        this.possibleConteinersList =[]
        var tmp_list =  {};
        var inside_num = 0;
        var inside_i = -1;
        var i =0;
        for(i=0; i < this.positions.length; i++){                
                
            var cont_check =this.checkStimuliAndConteiner(this.selectedImage, this.positions[i]);        
            tmp_list[this.positions[i].localID] = cont_check;
            if(cont_check == "INSIDE"){
                inside_num++;
                inside_i = i;
            }
        }

        if(inside_num == 1){
            this.possibleConteinersList = [];
            this.possibleConteinersList.push(this.positions[inside_i].localID);
        }
        else{
            for (var k in tmp_list){
                if(tmp_list[k]!="NONE")
                    this.possibleConteinersList.push(k);
            }

            //verificar o mais próximo.
            
            var smaller_dist = 99999999999999999999;
            var smaller_center = -1;
            
            for (var n =0; n < this.possibleConteinersList.length; n++){
                var dist =  this.selectedImage.distFrom(this.idStimulis[this.possibleConteinersList[n]].renderImage);

                if(dist < smaller_dist){
                    smaller_dist = dist;
                    smaller_center = this.possibleConteinersList[n];
                }

            }

            this.possibleConteinersList = [];
            if(smaller_center!=-1)
                this.possibleConteinersList.push(smaller_center);

        }
    

    }
    //ADD
    checkStimuliAndConteiner(stimuli, conteiner){
        if(stimuli.inside(conteiner.renderImage)){
            return "INSIDE";
        }
        else if(stimuli.touches(conteiner.renderImage)){ //(stimuli.isClose(conteiner.renderImage, 50)  || stimuli.touches(conteiner.renderImage)){
            return "CLOSE";
        }
        return "NONE";
    }
    render(ctx, scale=1){

        

        
        
        
        var i;       
        for(i=0;i<this.positions.length;i++){
            var image = this.positions[i];
            //image.renderImage.borderColor = "#33cc33";
            image.render(ctx, scale);        
        }
        


        
        
        for(i=0;i<this.content.length;i++){
            var image = this.content[i];
            image.render(ctx, scale);        
        }
        
        for(var key in this.containers){
        //for(i=0;i<this.container.length;i++){    
            var el = this.containers[key];
            var j;
            for (j=0; j < el.length; j++){
                if(this.moreThanOneStimulusInContainer){
                    var content = el[j];
                    var container = this.idStimulis[key];

                    var size = [container.renderImage.size[0], container.renderImage.size[1]];
                    size[0] = size[0]/2;
                    size[1] = size[1]/2;

                    var pos =  [container.renderImage.position[0], container.renderImage.position[1]];

                    var dist = size[0]/4;
                    pos = [pos[0] + dist * j, pos[1] + dist * j];

                    content.renderImage.setSize(size);
                    content.renderImage.position = pos;
                    content.render(ctx,scale);
                }
                else{
                    var content = el[j];
                    var container = this.idStimulis[key];

                    var size = [container.renderImage.size[0], container.renderImage.size[1]];
                    
                    /*size[0] = size[0]/2;
                    size[1] = size[1]/2;
                    */
                    var pos =  [container.renderImage.position[0], container.renderImage.position[1]];

                    var dist = size[0]/4;
                    pos = [pos[0] + dist * j, pos[1] + dist * j];

                    content.renderImage.setSize(size);
                    content.renderImage.position = pos;
                    content.render(ctx,scale);
                }
            }           
        }

         //ADD

         for(i=0; i < this.positions.length; i++){   
            this.positions[i].renderImage.borderColor = null;
        }
        if(this.possibleConteinersList.length > 0 && this.selectedImage!=null){
            
            for(var i = 0; i < this.possibleConteinersList.length; i++){
                var key = this.possibleConteinersList[i];
                var gradient = ctx.createLinearGradient(0, 0, 170, 0);
                    gradient.addColorStop("0", "magenta");
                    gradient.addColorStop("0.5" ,"blue");
                    gradient.addColorStop("1.0", "red");
                    ctx.beginPath();
                    ctx.strokeStyle = gradient;
                    ctx.lineWidth = 4;
                    ctx.rect(this.selectedImage.position[0], this.selectedImage.position[1], this.selectedImage.size[0], this.selectedImage.size[1]);
                    ctx.rect(this.idStimulis[key].renderImage.position[0], this.idStimulis[key].renderImage.position[1], this.idStimulis[key].renderImage.size[0], this.idStimulis[key].renderImage.size[1]);
          
                    ctx.stroke();
            }
        }
        if(this.screenshotTimer >= 1000){
            this.activity.saveTemporaryScreenshot();
            this.screenshotTimer = 0.0;
        }
        
        
    }
    
    pointerUp(evt) {
        this.putImageInsideContainer();/*
            var i; 
            for(i=0; i < this.positions.length; i++){                
                if(this.selectedImage!=null){
                    if(this.selectedImage.isClose(this.positions[i].renderImage, 50)  || this.selectedImage.touches(this.positions[i].renderImage)){
                        
                    this.putImageInsideContainer(this.selectedImage.stimulus, this.positions[i].localID);
                    //return;
                    }
                }
            }
            */
        
        
       this.selectedImage= null;
       
       var finBut = this.idStimulis[this.finishButton];

       if(finBut && finBut.wasPointed()){
           this.terminate();
       }
    }
    
    pointerDown(evt) {
       this.clickImage();
      console.log(this.selectedImage);
       if(this.selectedImage!=null){
            if(!this.selectedImage.stimulus.isClickable)
                this.selectedImage = null;
       }
    }
    

    

    pointerDrag(evt){
        
        if(this.selectedImage!= null){
            if(this.selectedImage.stimulus.isClickable){
                //this.removeStimulusFromContainer(this.selectedImage.stimulus);
                this.selectedImage.pointerDrag();
            }
                
        } 
    }
    
    pointerMove(evt) {
         
    }
    
    editPointerDown(evt){
        this.clickEditImage();
        
    }
    
    editPointerMove(evt){
       
    }
    editPointerDrag(evt){
        
        if(this.imageBeingEdited!=null){
            this.imageBeingEdited.editPointerDrag();//(this.activity.pointerMovement, this.activity.lastPointerPos);
        }
    }
    
    
}


