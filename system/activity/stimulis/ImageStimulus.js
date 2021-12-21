class ImageStimulus extends Stimulus{
    
    constructor(databaseID, localID,isClickable, isDraggable,
    activity,instruction,size, position, dragAndAssociate=false,containerID=null, fullContainer=false,emotionDescriptor=null){
        
        

        super(databaseID,'image', localID,isClickable, isDraggable, activity, instruction,fullContainer);
        
        console.log("fullCOntiner: "+fullContainer);
        this.renderImage = new DFTImage(databaseID, size, position, instruction, this);
        this.dragAndAssociate = dragAndAssociate;
        this.containerID = containerID;
        this.fullContainer = fullContainer;
        if(emotionDescriptor!=null){
            this.emotionDescriptor = emotionDescriptor;
        }
        
    }
    exportXML(xmlDoc){
        
        var img_xml = xmlDoc.createElement('image');
        var imgData =[];
        imgData['imageID'] = this.id;
        imgData['isClickable'] = this.isClickable;
        imgData['isDraggable'] = this.isDraggable;
        imgData['imageWidth'] = this.renderImage.size[0];
        imgData['imageHeight'] = this.renderImage.size[1];
        imgData['imageX'] = this.renderImage.position[0];
        imgData['imageY'] = this.renderImage.position[1];
        imgData['localID'] = this.localID;
        imgData['containerID'] = this.containerID;
        imgData['dragAndAssociate'] = this.dragAndAssociate;
        imgData['fullContainer'] = this.fullContainer;
        if(this.hasOwnProperty('emotionDescriptor')){
            imgData['emotionDescriptor']  = this.emotionDescriptor;
        }
        return [img_xml,imgData];
    }
    setDatabaseId(id){
        this.id = id;
        this.renderImage.id = id;
    }

    scale(s){
        var n_size = [this.renderImage.size[0], this.renderImage.size[1]];
        n_size[0] = n_size[0]*s[0];
        n_size[1] = n_size[1]*s[1];
        this.renderImage.setSize(n_size);
    }
    render(ctx, scale=1){
        if(!this.shouldRender){
            return;
        }
        if(this.renderImage!=null){
            this.renderImage.render(ctx, scale);
        }
            
        
    }
    
    renderPreview(ctx, scale){
        this.render(ctx, scale);
    }
    
    pointerDown(evt) {
        //this.editPointerDown(evt);
    }
    
    pointerUp(evt) {
        //this.editPointerUp(evt);
    }
    pointerMove(evt) {
        //throw new Error('You have to implement the pointerMove method!');
    }
    pointerDrag(evt){
        //throw new Error('You have to implement the pointerDrag method!');
    }
    
    editPointerUp(evt){
        var pos = this.instruction.activity.lastPointerPos;
        
        //this.renderImage.checkClickButton(pos);
    }
    
    editPointerDown(evt){
        //this.renderImage.editPointerDown(evt);
    }
    
    wasPointed(){
        
        return this.renderImage.wasPointed();
    }
    editPointerMove(evt){
        //throw new Error('You have to implement the editMouseDrag method!');
    }
    editPointerDrag(evt){
        this.renderImage.editPointerDrag(evt);
    }
    
    //checkClickButton(pos){
        
   // }
}

