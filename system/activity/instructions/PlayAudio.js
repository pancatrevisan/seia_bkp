class PlayAudio extends Instruction{
    exportXML(){
        var exp =[];
        exp['stimuli']   = [];
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="PlayAudio";
        exp['header']['position']=this.position;
        exp['header']['next']= this.next;
        exp['header']['description']=this.description;
        exp['header']['editable'] = this.editable;
        exp['data']['backgroundImageId'] = this.backgroundImageId;
        exp['data']['audioID'] = this.audioID;
        exp['data']['numRepeat'] = this.numRepeat;
        exp['data']['repeat'] = this.repeat;
        
        if(this.backgroundImageId!=null)
            exp['stimuli'].push(this.idStimulis[this.backgroundImageId]);
            
        //exp['stimuli'].push(this.idStimulis[this.backgroundImageId]);
        //exp['audios'].push(this.audioID);
        
        return exp;
    }
    
    constructor(data={'type':'PlayAudio','position':-1,'editable':true,'next':-1}){
        super(data);
        this.description = "Permite reproduzir um clipe de áudio";
        
        this.editableAttributes.push(
            {name:"backgroundImageId",   type:"image", description:"Imagem que será exibida ao fundo", editType:'swap'},
            {name:"audioID",  type:'audio', description:"Audio a ser executado",editType:'swap'}
            );
        this.backgroundImageId = null;
        if(data == null)
            return;
        if("backgroundImageId" in data){
            this.backgroundImageId = data['backgroundImageId'];                        
        }
        
        this.audioID    = data['audioID'];
        this.repeat     = data['repeat'];
        this.numRepeat  = data['numRepeat'];
        
        
    }
    startRunning(){
        super.startRunning();
        var audio = document.getElementById(this.audioID);
        if(audio!=null){
            
            audio.play();
        }
    }
    update(dt){
        var audio = document.getElementById(this.audioID);
        if(audio!=null){
            if(audio.ended)
                this.done = true;
        }
        else
            this.done = true;
    }
    
    
    renderPreview(ctx, scale=1){
       this.render(ctx, scale);
    }
    
    render(ctx, scale=1){
        //do nothing
        var backgroundImage = this.idStimulis[this.backgroundImageId];
        if(backgroundImage!=null){
            
            backgroundImage.render(ctx,scale);
        }
    }
    
    editPointerDown(evt){
        var image = this.idStimulis[this.backgroundImageId];
        if(image.wasPointed())
        {
            image.canDrag = true;         
        }
        
    }

    editPointerMove(evt){
       
    }
    editPointerDrag(evt){
        var image = this.idStimulist[this.backgroundImageId];
        image.drag(this.activity.pointerMovement, this.activity.lastPointerPos);
    }
    
    
}