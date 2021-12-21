class Stimulus{
    /**
     * 
     * @param {type} databaseID
     * @param {type} name
     * @param {type} owner_id
     * @param {type} description
     * @param {type} type
     * @param {type} presentable_stimuli
     * @param {type} version
     * @param {type} localID
     * @return {Stimuli}
     */
    constructor(databaseID, type, localID,isClickable, isDraggable, activity, instruction, fullContainer=false){
        
        this.instruction = instruction;
        this.id = databaseID;
        this.activity = activity;
        this.type = type;        
        this.renderImage = null;        
        this.localID = localID;        
        this.isClickable = isClickable;
        this.isDraggable = isDraggable;
        this.canRemove = true;
        this.shouldRender = true;
        this.isContainer = false;
        this.fullContainer = fullContainer;
        
        
        
    }

    getPosition(){
        if(this.instruction!=null){
            for(var i =0; i < this.instruction.stimulis.length; i++){
                if(this.instruction.stimulis[i].localID == this.localID)
                    return i;
            }
        }
    }
    exportXML(xmlDoc){
        throw new Error('You have to implement the exportXML method!');
    }
    render(ctx, scale=1){
       throw new Error('You have to implement the render method!');
    }
    
    renderPreview(ctx, scale){
        throw new Error('You have to implement the renderPreview method!');
    }
    
    pointerDown(evt) {
        throw new Error('You have to implement the pointerDown method!');
    }
    
    pointerUp(evt) {
        throw new Error('You have to implement the pointerUp method!');
    }
    pointerMove(evt) {
        throw new Error('You have to implement the pointerMove method!');
    }
    pointerDrag(evt){
        throw new Error('You have to implement the pointerDrag method!');
    }
    
    editPointerUp(evt){
        throw new Error('You have to implement the editPointerUp method!');
    }
    
    editPointerDown(evt){
        throw new Error('You have to implement the editMouseDown method!');
    }
    
    editPointerMove(evt){
        throw new Error('You have to implement the editMouseDrag method!');
    }
    editPointerDrag(evt){
        throw new Error('You have to implement the editPointerDrag method!');
    }
    wasPointed(){
        throw new Error('You have to implement the wasPointed method!');
    }
    resize(scale){
        
        this.renderImage.position = [this.renderImage.position[0]*scale[0],this.renderImage.position[1]*scale[1]];
        this.renderImage.setSize([this.renderImage.size[0]*scale[0],this.renderImage.size[1]*scale[1]]);
        
    }

    moveInCanvas(newPosition){
        var w = this.instruction.activity.canvas.width;
        var h = this.instruction.activity.canvas.height;
        
        if(h>w){
            var x = h;
            h = w;
            w = x;
        }
        var scale = [w/this.instruction.activity._editorSize[0], h/this.instruction.activity._editorSize[1]];
        this.renderImage.position = [newPosition[0]*scale[0],newPosition[1]*scale[1]];
    }
}


