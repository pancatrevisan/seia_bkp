class Feedback extends Instruction{
    
    resize(scale){
         //nothin special
    }
    
    constructor(data={'type':'Feedback','position':-1,'editable':true,'next':-1}){
        super(data);
        this.description = "Pede para o aplicador avaliar o estudante (certo, certo com dica, errado)";
        if(data==null)
            return; 
       this.correctButton = data['correctButton'];
       this.wrongButton = data['wrongButton'];
       this.correctWithTip = data['correctWithTip'];
       this.allowUse = true;
       this.editable = false;
       
    }
    
    exportXML(){
        var exp =[];
        exp['stimuli']   = [];
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="Feedback";
        exp['header']['position']=this.position;
        exp['header']['next']= this.next;
        exp['header']['description']=this.description;
        exp['header']['editable'] = this.editable;
        
        exp['data']['correctButton'] = this.correctButton;
        exp['data']['correctWithTip'] = this.correctWithTip;
        exp['data']['wrongButton'] = this.wrongButton;
        
        exp['stimuli'] = this.stimulis;
        return exp;
    }
    
    startRunning(){
        super.startRunning();
        
    }
    
    pointerUp(evt) {
        
        var i;
        for (i=0; i < this.stimulis.length; i++){
            if(this.stimulis[i].type=='audio'){
                this.stimulis[i].pointerUp(evt);
            }
        }
        
       var correctBut = this.idStimulis[this.correctButton];
       if(correctBut.wasPointed()){
           this.activity.result = this.activity.RESULT_CORRECT;
           var curr_res_data = "";
           if(this.activity.resultData.length > 0 && this.activity.resultData!="ONLY_SHOW"){
           if(this.activity.resultData.length > 0){
                var js = JSON.parse(this.activity.resultData, true);
                if( "type" in js && js['type']=="text"){
                    curr_res_data = js['value'];
                }
                
           }
        }
           if(curr_res_data.length>0)
                this.activity.resultData = '{"type":"feedback","value":"CORRECT","text":"' + curr_res_data+'"}';
            else
                this.activity.resultData = '{"type":"feedback","value":"CORRECT"}';

           this.terminate();
       }
       
       
       var correctTipBut = this.idStimulis[this.correctWithTip];
       if(correctTipBut.wasPointed()){
        this.activity.result = this.activity.RESULT_CORRECT_TIP;
        var curr_res_data = "";
        if(this.activity.resultData.length > 0 && this.activity.resultData!="ONLY_SHOW"){
             var js = JSON.parse(this.activity.resultData, true);
             if( "type" in js && js['type']=="text"){
                 curr_res_data = js['value'];
             }
             
        }
        if(curr_res_data.length>0)
             this.activity.resultData = '{"type":"feedback","value":"CORRECT_TIP","text":"' + curr_res_data+'"}';
         else
             this.activity.resultData = '{"type":"feedback","value":"CORRECT_TIP"}';

        this.terminate();
       }
       
       
       var wrongBut = this.idStimulis[this.wrongButton];
       if(wrongBut.wasPointed()){
        this.activity.result = this.activity.RESULT_WRONG;
        var curr_res_data = "";
        if(this.activity.resultData.length > 0 && this.activity.resultData!="ONLY_SHOW"){
        if(this.activity.resultData.length > 0){
             var js = JSON.parse(this.activity.resultData, true);
             if( "type" in js && js['type']=="text"){
                 curr_res_data = js['value'];
             }
             
        }
        }
        if(curr_res_data.length>0)
             this.activity.resultData = '{"type":"feedback","value":"WRONG","text":"' + curr_res_data+'"}';
         else
             this.activity.resultData = '{"type":"feedback","value":"WRONG"}';

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
        
        this.render(ctx, scale);
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