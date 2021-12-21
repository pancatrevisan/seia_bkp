class TextStimulus extends Stimulus{

    constructor(localID, activity, instruction, text, position, fontSize, fontColor, emotionDescriptor=null){
        super(null,'text', localID,true,true, activity, instruction);
        
        this.text = text;
        this.fontSize = fontSize;
        this.fontColor = fontColor;
        var size = [64,64];
        this.renderImage = new DFTImage('_textFrame',size ,position,instruction,this, false);   
        this.scale = [1,1];
        if(emotionDescriptor!=null){
            this.emotionDescriptor = emotionDescriptor;
        }
    }
     wasPointed(){
        
      
        var r =this.renderImage.wasPointed(); 
        
        return r;
    }
    exportXML(xmlDoc){
        var text_xml = xmlDoc.createElement('text');
        var txtData =[];
        
        txtData['text'] = this.text;
        txtData['textX'] = this.renderImage.position[0];
        txtData['textY'] = this.renderImage.position[1];
        txtData['fontSize'] = this.fontSize;
        txtData['fontColor'] = this.fontColor;
        txtData['localID'] = this.localID;
        if(this.hasOwnProperty('emotionDescriptor')){
            txtData['emotionDescriptor']  = this.emotionDescriptor;
        }
        return [text_xml,txtData];
    }
    render(ctx, scale=1){
        if(!this.shouldRender)
            return;
        if(this.activity.editing)
            this.renderImage.borderColor = "green";
        this.renderImage.render(ctx, scale);
        ctx.font = this.fontSize + "px Georgia";
        ctx.fillStyle = this.fontColor;
        ctx.textBaseline = 'top';
        var size = [ctx.measureText(this.text).width, parseInt(this.fontSize)];
        this.renderImage.setSize(size);
       
       
       
       
       /*var drawPos = [this.renderImage.position[0],this.renderImage.position[1]+parseInt(this.fontSize)];
       var drawPos2 = [drawPos[0]*this.scale[0], drawPos[1]*1];
       ctx.fillText(this.text, drawPos2[0],drawPos2[1]);*/

       ctx.fillText(this.text, this.renderImage.position[0],this.renderImage.position[1]);//+parseInt(this.fontSize));
       
    }
    
    
    resize(scale){
        console.log(this.fontSize);

        this.fontSize = this.fontSize * scale[0];
        console.log(this.fontSize);
        this.scale = scale;

        
       
       var drawPos = [this.renderImage.position[0],this.renderImage.position[1]];//+parseInt(this.fontSize)];
       //var drawPos2 = [drawPos[0]*this.scale[0], drawPos[1]*1];
       this.renderImage.position = [drawPos[0]*this.scale[0], drawPos[1]*1];
       //ctx.fillText(this.text, drawPos2[0],drawPos2[1]);
       

        //this.renderImage.position = [this.renderImage.position[0]*scale[0],this.renderImage.position[1]*scale[1]];
        //this.renderImage.position = [this.renderImage.position[0]*scale[0],this.renderImage.position[1]*scale[1]];
        //this.renderImage.setSize([this.renderImage.size[0]*scale[0],this.renderImage.size[1]*scale[1]]);
        
    }
    renderPreview(ctx, scale){
        this.render(ctx, scale);
        //throw new Error('You have to implement the renderPreview method!');
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
