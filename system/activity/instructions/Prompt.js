class Prompt extends Instruction{
    
    resize(scale){
         //nothin special
    }
    
    constructor(data={'type':'Prompt','position':-1,'editable':true,'next':-1},activity){
        super(data,activity);
        this.editableAttributes.push(
            new AttributeDescriptor("images",['image','text','audio','video'],true,"Adicionar Estímulo",'add/remove'),
            new AttributeDescriptor("showTime",['integer'],false,"Tempo de exibição",'swap'),
            new AttributeDescriptor("clickToFinish",['boolean'],false,"Finalizar ao clicar na tela?",'swap')
        );

        this.time = 0;
        this.description = "Exibe estímulos (vídeo, texto, áudio, imagem).";
        if(data == null)
            return;
        this.showTime = data['showTime'];// * 1000;
        //if(this.showTime  < 1000)
        //    this.showTime = this.showTime  * 1000;
        this.allowUse = true;
        this.editable = true;
        if("editable" in data){
            if(data['editable']=="false"){ 
                this.editable = false;
                
            }
        }
        this.clickDown = false;
        this.startFade = 0;
        this.maxStartFade = 1500;
        var i;
        for(i=0; i<this.stimulis.length; i++){
            if(this.stimulis[i].type == 'video'){
        
                this.stimulis[i].createAndAddPlayer();
         //       this.stimulis[i].show();
                    
            }
        }
        "clickToFinish" in data? this.clickToFinish = data['clickToFinish'] == 'true' : this.clickToFinish = false;
        console.log("Click to finish? "+this.clickToFinish);
        //this.editable = true;
    }
    terminate(){
        
        var i=0;
        for(i=0; i<this.stimulis.length; i++){
        
            if(this.stimulis[i].type == 'audio'){
                this.stimulis[i].stop();
            }
            else if(this.stimulis[i].type == 'video'){
        
                this.stimulis[i].removeFromBody();
            }
        }
        
        this.activity.result=this.activity.RESULT_NEUTRAL;
        this.activity.resultData = "ONLY_SHOW"; 
        
        this.done = true;
    }
    exportXML(){
        var exp =[];
        exp['stimuli']   = [];
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="Prompt";
        exp['header']['position']=this.position;
        exp['header']['next']= this.next;
        exp['header']['description']=this.description;
        exp['header']['editable'] = this.editable;
        
        exp['data']['showTime'] = this.showTime;
        exp['data']['clickToFinish'] = this.clickToFinish;
        
        
        exp['stimuli'] = this.stimulis;
        return exp;
    }
    
   
    

    startRunning(){
        
        super.startRunning();
        console.log("start running");
        var i;
        for(i=0; i<this.stimulis.length; i++){
            if(this.stimulis[i].type == 'video' && !this.activity.paused){
                console.log('show');
               // this.stimulis[i].createAndAddPlayer();
                this.stimulis[i].show();
                    
            }
        }
        
    }
    pause(){
        
        if(this.activity.paused){
            var i;
        for(i=0; i<this.stimulis.length; i++){
            if(this.stimulis[i].type == 'audio'){
            
                if(!this.stimulis[i].played){
                    this.stimulis[i].stop();
                }
            }
            if(this.stimulis[i].type == 'video'){
                this.stimulis[i].hide();
            }
        }
        }
        else{
            var i;
        for(i=0; i<this.stimulis.length; i++){
            if(this.stimulis[i].type == 'audio'){
            
                if(!this.stimulis[i].played){
                    this.stimulis[i].play();
                }
            }
            if(this.stimulis[i].type == 'video'){
                this.stimulis[i].show();
            }
        }
        }
        
    }

    update(dt){
        if(!this.activity.isRunning())
            return;
        
        this.startFade += dt;
        var i =0;
        if(!this.activity.paused){
            for(i=0; i<this.stimulis.length; i++){
                if(this.stimulis[i].type == 'audio'){
                
                    if(!this.stimulis[i].played){
                        this.stimulis[i].play();
                    }
                    else{
                        if(this.stimulis[i].finished() && !this.clickToFinish)
                            this.terminate();
                    }
                        
                }else if(this.stimulis[i].type == 'video'){
                    if(!this.stimulis[i].playerDone){
                        return;
                    }
                        
                    if(!this.stimulis[i].startedToPlay){
                        this.stimulis[i].show();
                        return;
                    }
                    if(this.stimulis[i].type == 'video' && !this.activity.paused && !this.stimulis[i].startedToPlay){
                        this.stimulis[i].show();
                    }
                    
                    if(this.stimulis[i].player.getCurrentTime() >= this.showTime || this.stimulis[i].endedVideo())
                    {
                        this.terminate();
                    }
                    return;           
                }
            }
            if(this.clickToFinish)
            {   
                return;
            }
            

            this.time = this.time + dt;
            if(this.time >= (1000*this.showTime)){
                this.terminate();
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
        
        this.render(ctx, scale);
    }
    
    pointerUp(evt){
        if(this.clickToFinish &&this.clickDown)
        {   
            this.terminate();
        }
    }
    pointerDown(evt){
        if(this.startFade >= this.maxStartFade)
            if(!this.activity.paused)
                this.clickDown = true;
    }
    editPointerDown(evt){
        this.clickEditImage();       
    }
    
    editPointerMove(evt){
       
    }
    editPointerDrag(evt){
        if(this.imageBeingEdited!=null){
            this.imageBeingEdited.editPointerDrag();
        }
    }
}