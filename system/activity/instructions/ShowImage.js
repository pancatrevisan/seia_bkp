class ShowImage extends Instruction{
    resize(scale){
        //nothing special
    }
   
    constructor(data={'type':'ShowImage','position':-1,'editable':true,'next':-1}){
        super(data);
        
       
        this.editableAttributes.push(
                new AttributeDescriptor("imageID",['image'],false,"Imagem que será exibida",'swap'),
                new AttributeDescriptor("showTime",['integer'],false,"Tempo de exibição",'swap'),
                new AttributeDescriptor("waitClick",['boolean'],false,"Esperar por um clique?",'swap'),
                );
                this.description = "Apresenta uma imagem.";
        if(data==null)
            return;
        this.imageID = data['imageID'];
        this.showTime = data['showTime'];
        this.time = 0;
        
    }
    
    
    exportXML(){
        var exp =[];
        exp['stimuli']   = [];
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="ShowImage";
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
        if(this.showTime == -1)
        {   
            return;
        }
        
        this.time = this.time + dt;
        if(this.time >= this.showTime){
            this.done = true;
        }
    }
    renderPreview(ctx, scale=1){
        
        this.render(ctx, scale);
    }
    render(ctx, scale=1){
       
        var image = this.idStimulis[this.imageID];
        
        if(image!=null){
            image.render(ctx, scale); 
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


