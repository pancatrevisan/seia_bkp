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
        this.editable = true;
        this.positions = [];  
        this.editableAttributes.push(
                new AttributeDescriptor("images",['image'],true,"Adicionar Estímulo",'add/remove',null,null,null,null,{'dragAndAssociate':true}),
                new AttributeDescriptor("images",['image'],true,"Adicionar conteiner de imagens",'add/remove',null,null,null,'positions'),
                new AttributeDescriptor("moreThanOneStimulusInContainer",['boolean'],false,"Mais de uma imagem por conteiner?",'swap'),
                new AttributeDescriptor("allowWrongStimuliInContainer",['boolean'],false,"Permitir colocar imagem em container errado?",'swap')
                        
                );
        this.allowUse = true;
        this.description = "Permite arrastar e agrupar imagens.";
        if(data == null)
            return;
        var i = 0;
        'finishButton' in data? this.finishButton = data['finishButton']: this.finishButton = null;
        
        //if(this.finishButton!=null){
        //    if(this.idStimulis[this.finishButton])
        //    this.idStimulis[this.finishButton].canRemove =false;
        //}
            
        

            this.removeStimuli(this.finishButton);
        while(('positions'+i) in data){
            var posID = data[('positions'+i)];    
            this.positions.push(this.idStimulis[posID]);
            this.idStimulis[posID].isDraggable = false;
            i++;
        }
        this.moreThanOneStimulusInContainer = true;
        
        'moreThanOneStimulusInContainer' in data? this.moreThanOneStimulusInContainer = 
                data['moreThanOneStimulusInContainer']=='true' : this.moreThanOneStimulusInContainer = true;
        
        this.allowWrongStimuliInContainer = true;
        'allowWrongStimuliInContainer' in data? this.allowWrongStimuliInContainer = 
                data['allowWrongStimuliInContainer']=='true' : this.allowWrongStimuliInContainer = true;
        
        
        this.originalPositions = [];
        this.numContainers = this.positions.length ;
        this.insertedImages = 0;
        this.containers = [];
        for (i = 0; i < this.numContainers; i++){
            
            this.containers[this.positions[i].localID] = [];
        }
        
        this.content = [];
        
        
        for (i = 0; i < this.stimulis.length; i++){
            this.originalPositions[this.stimulis[i].localID] =[ 
                        this.stimulis[i].renderImage.position[0],this.stimulis[i].renderImage.position[1] ];
                    
            if(!this.positions.includes(this.stimulis[i])){
                this.stimulis[i].dragAndAssociate = true;
                this.stimulis[i].isDraggable = true;
                
                this.content.push(this.stimulis[i]);
            }
        }
        console.log(this.originalPositions);
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
        exp['data']['finishButton'] = this.finishButton;
        exp['stimuli'] = this.stimulis;
        
        return exp;
    }
    
    
    terminate(){
        this.done=true;
    }
    
    putImageInsideContainer(image, container){
   
        
        var contID = container;
        
       
        if(image.containerID != contID)
        {
            if(!this.allowWrongStimuliInContainer){
                
                
                image.renderImage.position = [this.originalPositions[image.localID][0],this.originalPositions[image.localID][1]];
                
                this.selectedImage = null;
                return;
            }
        }
        
             //pode colocar mais de um estímulo em cada conteiner?
        if(this.containers[container].length >= 1 && !this.moreThanOneStimulusInContainer){
            image.renderImage.position = 
                    [this.originalPositions[image.localID][0],this.originalPositions[image.localID][1]];
            this.selectedImage = null;
            
            return;
        }
        
       
        this.containers[container].push(image);
        image.isDraggable = false;
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
    
    update(dt){
        if(this.selectedImage == null)
            return;
        if(!this.selectedImage.stimulus.isDraggable){
            this.selectedImage = null;
            return;
        }
        
        if(this.content.length<= 0)
        {
            this.terminate();
        }
        /*if(this.selectedImage!=null){
            var i; 
            for(i=0; i < this.positions.length; i++){                
                if(this.selectedImage.isClose(this.positions[i].renderImage, 50)){//if(this.selectedImage.touches(this.positions[i].renderImage)){
                   this.putImageInsideContainer(this.selectedImage.stimulus, this.positions[i].localID);
                   return;
                }
            }
            
        }*/
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
    render(ctx, scale=1){
        console.log('content...');
        console.log(this.content);
        
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
    }
    
    pointerUp(evt) {
       if(this.selectedImage!=null){
            var i; 
            for(i=0; i < this.positions.length; i++){                
                if(this.selectedImage.isClose(this.positions[i].renderImage, 50) || this.selectedImage.touches(this.positions[i].renderImage)){
                   this.putImageInsideContainer(this.selectedImage.stimulus, this.positions[i].localID);
                   //return;
                }
            }
            
        }
        
       this.selectedImage= null;
       
       var finBut = this.idStimulis[this.finishButton];

       if(finBut && finBut.wasPointed()){
           this.terminate();
       }
    }
    
    pointerDown(evt) {
       this.clickImage();
    }
    
    pointerDrag(evt){
        
        if(this.selectedImage!= null){
            this.selectedImage.pointerDrag();
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


