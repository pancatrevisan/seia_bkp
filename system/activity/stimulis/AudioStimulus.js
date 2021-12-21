class AudioStimulus extends Stimulus{

    constructor(databaseID, localID,isClickable, isDraggable, activity, instruction, autoplay, repeat, numberOfRepeats, renderIcon=false, imgPos=null, emotionDescriptor = null){
        super(databaseID,'audio', localID,isClickable, isDraggable,  activity,instruction);
        
        this.autoplay = autoplay;
        this.repeat   = repeat;
        this.numberOfRepeats = numberOfRepeats;
        this.renderIcon = renderIcon;
        this.iconSize = 50;
        var size = [this.iconSize, this.iconSize];
        var pos = imgPos!=null? imgPos : [10,10];
        this.renderImage = new DFTImage('_audioIcon',size,pos,instruction,this, false);  
        this.played = false;
        this.renderIcon = false;
        if(emotionDescriptor!=null){
            this.emotionDescriptor = emotionDescriptor;
        }

        
    }
    finished(){
        var audioControl = document.getElementById(this.id);
        if(audioControl!=null)
            return audioControl.ended;
        return false;
    }
    play(){
        var audioControl = document.getElementById(this.id);
        
        audioControl.currentTime = 0;
        this.played = true;
        audioControl.play();
    }
    stop(){
        var audioControl = document.getElementById(this.id);
        if(audioControl!=null)
            audioControl.pause();
    }
    exportXML(xmlDoc){
        var aud_xml = xmlDoc.createElement('audio');
        var audData =[];
        audData['isClickable'] = this.isClickable;
        audData['isDraggable'] = this.isisDraggable;
        audData['audioID'] = this.id;
        audData['localID'] = this.localID;
        audData['autoplay'] = this.autoplay;
        audData['repeat'] = this.repeat;
        audData['numberOfRepeats'] = this.numberOfRepeats;
        audData['renderIcon'] = this.renderIcon;        

        audData['imageX'] = this.renderImage.position[0];
        audData['imageY'] = this.renderImage.position[1];
        if(this.hasOwnProperty('emotionDescriptor')){
            audData['emotionDescriptor']  = this.emotionDescriptor;
        }
        return [aud_xml,audData];
    }
    render(ctx, scale=1){
       if(this.renderIcon || this.instruction.isEditing()){
           this.renderImage.render(ctx,scale);
       }
    }
    
    renderPreview(ctx, scale){
        this.render(ctx, scale=1);
    }
    
    pointerDown(evt) {
        //throw new Error('You have to implement the pointerDown method!');
    }
    
    pointerUp(evt) {
        console.log("pointer up no audio");
        if(!this.renderIcon)
            return;
        if(this.renderImage.wasPointed())
            this.play();
    }
    pointerMove(evt) {
        //throw new Error('You have to implement the pointerMove method!');
    }
    pointerDrag(evt){
        //throw new Error('You have to implement the pointerDrag method!');
    }
    
    editPointerUp(evt){
        
        var audio = document.getElementById(this.id);
        var pos = this.instruction.activity.lastPointerPos;
        if(pos[0]==this.renderImage.mouse_first_click[0] && pos[1]==this.renderImage.mouse_first_click[1] )        
            audio.play();
        //throw new Error('You have to implement the editPointerUp method!');
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





