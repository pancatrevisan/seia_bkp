class GroupImages extends Instruction{    
    constructor(data={'type':'GroupImages','position':-1,'editable':true,'next':-1}){
        
        super(data);
        this.positions = [];  
        this.editableAttributes.push(
                new AttributeDescriptor("imageID",['image'],false,"Imagem que será exibida",'swap'),
                new AttributeDescriptor("callFunction",['callFunction'],false,"Adicionar posição de parada",'addPosition'),
                new AttributeDescriptor("move",['boolean'],false,"Mover através posições?",'swap'),
                new AttributeDescriptor("speed",['integer'],false,"Velocidade",'swap'),
                new AttributeDescriptor("repeat",['boolean'],false,"Repetir o movimento?",'swap'),
                new AttributeDescriptor("numberOfRepeats",['integer'],false,"Número de repetições",'swap'),
                new AttributeDescriptor("waitTime",['integer'],false,"Tempo de espera em cada posição",'swap')
                
                );
        
        
        this.description = "Permite arrastar e agrupar imagens. NÃO USAR?";
        if(data == null)
            return;
        this.imageID = data['imageID'];
        
        
        //the image moves between locations? or 'transport'?
        this.move = true;
        'move' in data? this.move = data['move']: this.move = 1;
        
        this.speed = 0;
        'speed' in data? this.speed = data['speed']: this.speed = 1;
        
        
        //if transport, how long stay in any stop?
        this.waitTime = 0;
        'waitTime' in data? this.waitTime = data['waitTime']: this.waitTime = 1;
        
        //repeat the movement?
        this.repeat = false;
        'repeat' in data? this.repeat = data['repeat']: this.repeat = false;
        
        //how many times?
        this.numberOfRepeats = 1;
        'numberOfRepeats' in data? this.numberOfRepeats = data['numberOfRepeats']: this.numberOfRepeats = 1;
               
    }
    addPosition(){
        
        if(this.imageID == null){
            return;
        }
        var dbId = this.idStimulis[this.imageID].id;
        
        var attr = new AttributeDescriptor("images",['image'],true,"Adicionar Estímulo",'add/remove');
        var params = [];
        params['type'] = 'image';
        var st = this.setAttributeValue(attr, dbId, params);
        var size = this.idStimulis[this.imageID].renderImage.size;
        
        console.log("add position");
        this.positions.push(st);
        
        
    }
    removeStimuli(stimuli){
        super.removeStimuli(stimuli);
        console.log("remove aqui...");
        console.log(this.positions);
        var i;
        for(i= 0; i < this.positions.length; i++){
            if(this.positions[i].localID==stimuli ){
                this.positions.splice(i,1);
            }
        }
        console.log(this.positions);
        
    }
    
    exportXML(){
        var exp =[];
        exp['stimuli']   = [];
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="TrackObject";
        exp['header']['position']=this.position;
        exp['header']['next']= this.next;
        exp['header']['description']=this.description;
        exp['header']['editable'] = this.editable;
        exp['data']['imageID'] = this.imageID;
        exp['data']['showTime'] = this.showTime;
        
        exp['stimuli'].push(this.idStimulis[this.imageID]);
        
        return exp;
    }
    
    update(dt){
        this.time = this.time + dt;
        if(this.time >= this.waitTime){
            
        }
    }
    renderPreview(ctx, scale=1){
        var i;       
        
        for(i=0;i<this.positions.length; i++)
        {
            var image = this.positions[i];
            image.setDatabaseId(this.idStimulis[this.imageID].id);
            var size = this.idStimulis[this.imageID].renderImage.size;
            image.renderImage.setSize(size);
            image.render(ctx, scale);
            ctx.fillText(''+(i+1),image.renderImage.position[0],image.renderImage.position[1]);
        }
    }
    render(ctx, scale=1){
        //primeira posicao deseha a imagem.
       
        var image = this.idStimulis[this.imageID];
        
        if(image!=null){
            var border_bkg = image.borderColor;
            image.borderColor = null;
            image.render(ctx, scale);
            image.borderColor = border_bkg; 
        }
        else{
            console.log("why null??" +this.imageID);
        }
    }
    
     pointerUp(evt) {
       if(this.showTime == -1){
            if(this.activity.wasClick){
                this.done = true;
            }
            return;
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


