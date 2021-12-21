class End extends Instruction{
    resize(scale){}
    exportXML(){
        var exp =[];
        exp['stimuli']   = [];
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="End";
        exp['header']['position']=this.position;
        exp['header']['next']= this.next;
        exp['header']['description']=this.description;
        exp['header']['editable'] = this.editable;
        
        exp['stimuli'] = this.stimulis;
        
        return exp;
    }
    constructor(data={'type':'End','position':-1,'editable':true,'next':-1}){
        super(data);
        this.description = "Finaliza a Atividade";
        
    }
    startRunning(){
        super.startRunning();
        this.done = true;
    }
  
    update(dt){
        
    }
    
    render(ctx, scale=1){
        
    }
    
    
    renderPreview(ctx, scale=1){
        
        this.render(ctx, scale);
    }
    
}