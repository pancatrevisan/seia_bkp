class FillText extends Instruction{
     resize(scale){
    
    }
    exportXML(){
        var exp =[];
        exp['stimuli']  = [];
        
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="FillText";
        exp['header']['position']=this.position;
        exp['header']['next']= this.next;
        exp['header']['description']=this.description;
        exp['header']['editable'] = this.editable;
        
        exp['data']['textEditor'] = this.textEditor;
        exp['data']['finishButton'] = this.finishButton;
        
        exp['stimuli'] = this.stimulis;
        return exp;
    }
    constructor(data={'type':'FillText','position':-1,'editable':true,'next':-1},activity){
        super(data,activity);
        
        this.editableAttributes.push(
            new AttributeDescriptor("images",['image','text','audio'],true,"Adicionar Estí­mulo",'add/remove')            
            );
        this.description = "O estudante pode inserir textos";
        

        if(data == null)
            return;
        
        'finishButton' in data?this.finishButton = data['finishButton']:this.finishButton = null;;
        if(this.finishButton!=null)
            this.idStimulis[this.finishButton].canRemove =false;

        'textEditor' in data? this.textEditor = data['textEditor']:this.textEditor = null;
        this.allowUse = true;
        this.editable = true;

    }
    pause(pause){
        var textInput = this.idStimulis[this.textEditor];
        if(pause)
            textInput.hide();
        else
            textInput.show();
    }
    terminate(){
        
        var textInput = this.idStimulis[this.textEditor];
        
        this.activity.resultData = '{"type":"text", "value":"' +  textInput.getValue().replace(/(\r\n|\n|\r)/gm,"<br>")+ '"}';
        
        textInput.hide();
        textInput.removeFromBody();
        
        
        this.done=true;

        //this.activity.result = this.activity.RESULT_CORRECT_DATA;
        
        
    }
    startRunning(){
        
        super.startRunning();
        var textInput = this.idStimulis[this.textEditor];
        
        textInput.show();
        
    }
    pointerUp(evt) {
        
        var i;
        for (i=0; i < this.stimulis.length; i++){
            if(this.stimulis[i].type=='audio'){
                this.stimulis[i].pointerUp(evt);
            }
        }
        
       var finBut = this.idStimulis[this.finishButton];
       if(finBut.wasPointed()){
           this.terminate();
       }
    }
    update(dt){
        var i;
        if(this.activity.paused)
            return;
        for (i=0; i < this.stimulis.length; i++){
            if(this.stimulis[i].type=='audio'){
                if(!this.stimulis[i].played)
                    this.stimulis[i].play();
            }
        }
    }
    
    render(ctx, scale=1){
        
       var i;
        for(i = 0; i < this.stimulis.length; i++){
            this.stimulis[i].render(ctx, scale);
        }
    }
    
    
    renderPreview(ctx, scale=1){
        
         var i;
        for(i = 0; i < this.stimulis.length; i++){
            this.stimulis[i].renderPreview(ctx, scale);
        }
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