class PositionStimulus extends Stimulus{

    constructor(localID, activity, instruction, position, number){
        super(null,'text', localID,true,true, activity, instruction);
        this.number = number;
        var size = [64,64];
        this.renderImage = new DFTImage('_textFrame',size ,position,instruction,this, false);        
    }
     wasPointed(){
        console.log('PositionStimulus.wasPointed()');
        return this.renderImage.wasPointed();
    }
    exportXML(xmlDoc){
        var text_xml = xmlDoc.createElement('text');
        var txtData =[];
        
        txtData['text'] = this.text;
        txtData['textX'] = this.renderImage.position[0];
        txtData['textY'] = this.renderImage.position[1];
        txtData['localID'] = this.localID;
        return [text_xml,txtData];
    }
    render(ctx, scale=1){
       
    }
    
    renderPreview(ctx, scale){
        
       ctx.font = this.fontSize + "px Georgia";
       ctx.fillStyle = "black";
       this.renderImage.render(ctx, scale);
       
       
       var drawPos = [this.renderImage.position[0],this.renderImage.position[1]];
       
       ctx.fillText(""+this.number, drawPos[0],drawPos[1]);
    }
    
     pointerDown(evt) {
        //throw new Error('You have to implement the pointerDown method!');
    }
    
    pointerUp(evt) {
        //throw new Error('You have to implement the pointerUp method!');
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





