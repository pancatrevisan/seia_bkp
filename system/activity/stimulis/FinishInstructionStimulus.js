class TextInputStimulus extends Stimulus{
    //https://fivera.net/paper-text-area-created-with-css-only-editable-example/
    constructor(localID, activity, instruction, position){
        super(null,'finishInstruction', localID,true,true, activity, instruction);
        
        
        var size = [64,64];
        this.renderImage = new DFTImage('_finishButton',size ,position,instruction,this, true);        
        
    }
    
    
        
     wasPointed(){
        
        return this.renderImage.wasPointed();
    }
    

    exportXML(xmlDoc){
        var text_xml = xmlDoc.createElement('finishInstruction');
        var txtData =[];
        
        
        txtData['isClicable'] = this.isClickable;
        txtData['isDraggable'] = this.isDraggable;
        txtData['localID'] = this.localID;
        txtData['posX'] = this.renderImage.position[0];
        txtData['posY'] = this.renderImage.position[1];
        txtData['sizeX'] = this.renderImage.size[0];
        txtData['sizeY'] = this.renderImage.size[1];
        txtData['text'] = this.text;
        
        
        
        return [text_xml,txtData];
    }
    render(ctx, scale=1){
        
       
       /*ctx.beginPath();
       var dashSpace = 3;
       ctx.strokeStyle = "#000000";
       ctx.setLineDash([dashSpace,dashSpace * 1.3]);
       var i;
       for (i =0; i < this.numberOfLines; i++){
           var lineStart = [this.renderImage.position[0],this.renderImage.position[1] + i * this.fontSize + this.fontSize];
           var lineEnd   =   [this.renderImage.position[0] + this.renderImage.size[0],this.renderImage.position[1] + i * this.fontSize + this.fontSize];
            ctx.moveTo(lineStart[0], lineStart[1]);
            ctx.lineTo(lineEnd[0], lineEnd[1]);
       }
       
       ctx.stroke();
       ctx.setLineDash([]);
       */
       
       
       //var size = [ctx.measureText(this.text).width, parseInt(this.fontSize)];
       //this.renderImage.setSize(maxSize);
       
       
       //this.roundRect(ctx, drawPos[0], drawPos[1]-this.fontSize, size[0], size[1]);
       var drawPos = [this.renderImage.position[0],this.renderImage.position[1]+parseInt(this.fontSize)];
       var maxSize  = [this.renderImage.size[0],this.renderImage.size[1]];
       this.textAreaDiv.style.left = drawPos[0];
       this.textAreaDiv.style.top  = drawPos[1] - this.fontSize;
       this.textAreaDiv.style.width = maxSize[0];
       this.textAreaDiv.style.height = maxSize[1];
       
      
    }
    
    renderPreview(ctx, scale){
        console.log('rndr prviw');
        ctx.font = this.fontSize + "px Georgia";
       ctx.fillStyle = this.fontColor;
       
       
       //var maxSize = [this.numberOfColumns*ctx.measureText("M").width, parseInt(this.fontSize)*this.numberOfLines];
       ctx.strokeStyle = "#D44147";
       ctx.fillStyle = "linear-gradient(transparent, transparent 28px, #91D1D3 28px)";
       var drawPos = [this.renderImage.position[0],this.renderImage.position[1]+parseInt(this.fontSize)];
       this.renderImage.render(ctx, scale);
       ctx.fillText(this.text, drawPos[0],drawPos[1]);
        //throw new Error('You have to implement the renderPreview method!');
    }
    
     pointerDown(evt) {
        //throw new Error('You have to implement the pointerDown method!');
    }
    
    pointerUp(evt) {
        this.instruction.terminate();
    }
    pointerMove(evt) {
        //throw new Error('You have to implement the pointerMove method!');
    }
    pointerDrag(evt){
        //throw new Error('You have to implement the pointerDrag method!');
    }
    
    editPointerUp(evt){
        
        
    }
    
    editPointerDown(evt){
        //throw new Error('You have to implement the editMouseDown method!');
    }
    
    editPointerMove(evt){
        //throw new Error('You have to implement the editMouseDrag method!');
    }
    editPointerDrag(evt){
        //throw new Error('You have to implement the editPointerDrag method!');
    }
    
}





