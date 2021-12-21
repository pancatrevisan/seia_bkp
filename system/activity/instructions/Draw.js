class Draw extends Instruction{

    constructor(data={'type':'Draw','position':-1,'editable':true,'next':-1},activity){
        super(data,activity);
        this.editableAttributes.push(
            new AttributeDescriptor("images",['image','text','audio'],true,"Adicionar Estímulo",'add/remove'),
            new AttributeDescriptor("background_image", [ 'image'], false, "Imagem de fundo", 'swap'),
            new AttributeDescriptor("draw_type", [ 'selection'], false, "Tipo de contorno", 'swap',null, null,null, null,null,[['circle','Circular'],['underline','Sublinhar'],['cut','Riscar'],['triangle','Triângulo']]),
            new AttributeDescriptor("stroke_color", [ 'color'], false, "Cor da Caneta", 'swap'),
            new AttributeDescriptor("expectedStimulus", ['stimulusID'], false, "Estímulo correto", 'swap'),
            new AttributeDescriptor("showTime",['integer'],false,"Tempo de exibição",'swap')
        );
        
        'background_image' in data? this.background_image = data['background_image']: this.background_image = null;
        'draw_type' in data? this.draw_type = data['draw_type']: this.draw_type = 'underline';
        'stroke_color' in data? this.stroke_color = data['stroke_color']: this.stroke_color = '#000';
        'expectedStimulus' in data? this.expectedStimulus = data['expectedStimulus']: this.expectedStimulus = null;
        'showTime' in data? this.showTime = data['showTime']: this.showTime = -1;

        this.timeFinished = false;

        if(this.background_image == 'null')
            this.background_image = null;
        this.time = 0;
        this.description = "Desenhar.";
        if(data == null)
            return;
        

        this.allowUse = true;
        this.editable = true;
        if("editable" in data){
            if(data['editable']=="false"){ 
                this.editable = false;
                
            }
        }

        //a captura de tela é de 1 em 1 segundo. após terminar de desenhar, espera um tempinho para capturar a tela final.
        this.awaitToFinish = false;
        this.timeToAwaitToFinish = 1500;
        this.timeAwatingToFinish = 0;

        this.clickDown = false;
        this.startFade = 0;
        this.maxStartFade = 1500;
        var i;
        for(i=0; i<this.stimulis.length; i++){
            if(this.stimulis[i].type == 'video'){
        
                this.stimulis[i].createAndAddPlayer();                    
            }
        }
        "clickToFinish" in data? this.clickToFinish = data['clickToFinish'] == 'true' : this.clickToFinish = false;
        console.log("Click to finish? "+this.clickToFinish);
        //this.editable = true;

        this.drawings = [];
        this.currentDrawing = null;
        this.screenshotTimer = 0.0;
    }
    terminate(){
        
        var ret = {};
        ret['type'] = "draw";
        ret['save_screenshot'] = true;
        ret['draw_type'] = this.draw_type;
        ret['selected'] = null;
        ret['expected'] = this.exportStimulus(this.idStimulis[this.expectedStimulus]);
        
        var i=0;
        for(i=0; i<this.stimulis.length; i++){
        
            if(this.stimulis[i].type == 'audio'){
                this.stimulis[i].stop();
            }
            else if(this.stimulis[i].type == 'video'){
        
                this.stimulis[i].removeFromBody();
            }
        }

        if(this.timeFinished || this.drawings.length <= 0){
            ret['showTime'] = this.showTime;
            ret['timeFinished'] = true;
        /*    this.activity.result=this.activity.RESULT_CORRECT_TIP;*/
            this.activity.result=this.activity.RESULT_NEUTRAL;
        
            this.activity.resultData =JSON.stringify(ret);
            this.done = true;
            return;
        }
        
        
        var isLine = true;
        var isCircle = true;
        var isTriangle = true;
        var isHorizontal = true;
        //verificar se o desenho foi correto.
        if(this.draw_type == 'underline' || this.draw_type=='cut'){
            isLine = this.drawings[0].checkIsCloseToLine();
            isHorizontal = this.drawings[0].isCloseToHorizontalLine();
        }
        else if(this.draw_type == 'circle'){
            isCircle = this.drawings[0].checkIsCloseToCircle();
        }
        else if(this.draw_type == 'triangle'){
            isTriangle = this.drawings[0].checkIsCloseToTriangle();
        }

        if(!isCircle || !isLine || !isTriangle){//se não acertou o desenho, está errado.
            console.log("ERRADO. não é círculo ou reta.");
            this.activity.result=this.activity.RESULT_WRONG;
            this.activity.resultData =JSON.stringify(ret);
            this.done = true;
            
            return;
        }

        //se reconheceu, verifica se a linha está abaixo ou o circulo em volta.
        if(this.draw_type == 'triangle' && isTriangle){
            var center  = this.drawings[0].center;
            var pointedElement = null;
            
            //procura o primeiro cujo centro da esfera esteja dentro...
            for(var i = 0; i < this.stimulis.length && pointedElement == null ; i++){
                if( (this.stimulis[i].type=='image' || this.stimulis[i].type=='text') && this.stimulis[i].localID!=this.background_image && this.stimulis[i].renderImage.isPointInside(center)){
                    var wasp = this.stimulis[i].renderImage.isPointInside(center);
                    console.log("centro do triangulo: " + center[0]+","+center[1]+" was pointed: " + wasp);
                    pointedElement = this.stimulis[i];
                }
            }
            
            if (pointedElement!=null)
                ret['selected'] = this.exportStimulus(pointedElement);
            
            //se o que foi circulado é o correto...
            console.log("Expected stimulus: " + this.expectedStimulus);
            if(pointedElement!=null && pointedElement.localID == this.expectedStimulus){
                this.next = this.onCorrect;
                this.activity.result = this.activity.RESULT_CORRECT;
                if (this.activity.showTip) {
                    this.activity.result = this.activity.RESULT_CORRECT_TIP;
                }
                console.log("Correto.");
            }
            else{
                //errado.
                this.activity.result = this.activity.RESULT_WRONG;
                console.log("ERRADO. Não selecionou no triangulo");
            } 
        }
        else if(this.draw_type == 'underline' && isLine && isHorizontal){
            var center = this.drawings[0].center;//ponto central da linha
            //verifica se o ponto central está dentro (horizonalmente)
            //da imagem e abaixo
            var pointedElement = null;
            //procura o primeiro cujo centro da esfera esteja dentro...
            


            for(var i = 0; i < this.stimulis.length && pointedElement == null; i++){
                
                if( (this.stimulis[i].type=='image' || this.stimulis[i].type=='text') && this.stimulis[i].localID!=this.background_image){
                
                    var p0 = [this.stimulis[i].renderImage.position[0], this.stimulis[i].renderImage.position[1]];
                    var p1 = [this.stimulis[i].renderImage.position[0] +this.stimulis[i].renderImage.size[0],
                            this.stimulis[i].renderImage.position[1] +this.stimulis[i].renderImage.size[1]];
                    console.log("verificar...");
                    console.log(p0);
                    console.log(p1);
                    console.log(center);

                    var limInf = this.stimulis[i].renderImage.size[1]/2;
                    var limSup = p1[1] + this.stimulis[i].renderImage.size[1] / 4;

                    if(center[0] >= p0[0] && center[0] <= p1[0]){
                        //se foir maior que 3/4, pois a imagem é maior que o tamanho da fonte exibida...
                        if( center[1] >= limInf && center[1] <= limSup){
                            pointedElement = this.stimulis[i];    
                        }
                    }
                }
            }
            if (pointedElement!=null)
                ret['selected'] = this.exportStimulus(pointedElement);
            console.log(pointedElement);
            console.log("selecionado " + pointedElement + " esperado: " + this.expectedStimulus);
            //se o que foi sublinhado é o correto...
            if(pointedElement!=null && pointedElement.localID == this.expectedStimulus){
                this.next = this.onCorrect;
                this.activity.result = this.activity.RESULT_CORRECT;
                if (this.activity.showTip) {
                    this.activity.result = this.activity.RESULT_CORRECT_TIP;
                }
                console.log("Correto.");
            }
            else{
                //errado.
                this.activity.result = this.activity.RESULT_WRONG;
                console.log("ERRADO. Não selecionou no underline");
            } 
            
        }
        else if(this.draw_type == 'cut' && isLine){
            
            //verifica se o ponto central está dentro (horizonalmente)
            //da imagem e abaixo
            var pointedElement = null;
            //procura o primeiro cujo centro da esfera esteja dentro...
            var center = this.drawings[0].center;//ponto central da linha

            for(var i = 0; i < this.stimulis.length && pointedElement == null; i++){
                
                if( (this.stimulis[i].type=='image' || this.stimulis[i].type=='text') && this.stimulis[i].localID!=this.background_image){
                
                    var p0 = [this.stimulis[i].renderImage.position[0], this.stimulis[i].renderImage.position[1]];
                    var p1 = [this.stimulis[i].renderImage.position[0] +this.stimulis[i].renderImage.size[0],
                            this.stimulis[i].renderImage.position[1] +this.stimulis[i].renderImage.size[1]];
                   // var center = [(p0[0]+p1[0]) /2,(p0[1]+p1[1]) /2 ];//ponto central da linha
                    console.log("Checando o 'corte' ");
                    console.log(p0);
                    console.log(p1);
                    console.log(center);
                   
                    var wasp = this.stimulis[i].renderImage.isPointInside(center);
                    console.log("point inside? " +wasp);
                    if(this.stimulis[i].renderImage.isPointInside(center)){

                            pointedElement = this.stimulis[i];    
                        }
                    }
                }
            
                if (pointedElement!=null)
                ret['selected'] = this.exportStimulus(pointedElement);
            console.log(pointedElement);
            console.log("selecionado " + pointedElement + " esperado: " + this.expectedStimulus);
            //se o que foi sublinhado é o correto...
            if(pointedElement!=null && pointedElement.localID == this.expectedStimulus){
                this.next = this.onCorrect;
                this.activity.result = this.activity.RESULT_CORRECT;
                if (this.activity.showTip) {
                    this.activity.result = this.activity.RESULT_CORRECT_TIP;
                }
                console.log("Correto.");
            }
            else{
                //errado.
                this.activity.result = this.activity.RESULT_WRONG;
                console.log("ERRADO. Não selecionou no cut");
            } 
            
        }
        else if(this.draw_type == 'circle' && isCircle){
            var center  = this.drawings[0].center;
            var pointedElement = null;
            console.log("Backgroubnd: " + this.background_image);
            //procura o primeiro cujo centro da esfera esteja dentro...
            for(var i = 0; i < this.stimulis.length && pointedElement == null ; i++){
                if( (this.stimulis[i].type=='image' || this.stimulis[i].type=='text') && this.stimulis[i].localID!=this.background_image && this.stimulis[i].renderImage.isPointInside(center)){
                    var wasp = this.stimulis[i].renderImage.isPointInside(center);
                    console.log("centro do circulo: " + center[0]+","+center[1]+" was pointed: " + wasp);
                    pointedElement = this.stimulis[i];
                }
            }
            
            if (pointedElement!=null)
                ret['selected'] = this.exportStimulus(pointedElement);
            
            //se o que foi circulado é o correto...
            console.log("Expected stimulus: " + this.expectedStimulus);
            if(pointedElement!=null && pointedElement.localID == this.expectedStimulus){
                this.next = this.onCorrect;
                this.activity.result = this.activity.RESULT_CORRECT;
                if (this.activity.showTip) {
                    this.activity.result = this.activity.RESULT_CORRECT_TIP;
                }
                console.log("Correto.");
            }
            else{
                //errado.
                this.activity.result = this.activity.RESULT_WRONG;
                console.log("ERRADO. Não selecionou no circle");
            } 
            
        }
        this.activity.resultData =JSON.stringify(ret);
        this.done=true;
        console.log(this.activity.resultData );
    }
    exportXML(){
        var exp =[];
        exp['stimuli']   = [];
        exp['header']   = [];
        exp['data']     = [];
        
        exp['header']['type']    ="Draw";
        exp['header']['position']=this.position;
        exp['header']['next']= this.next;
        exp['header']['description']=this.description;
        exp['header']['editable'] = this.editable;
        
        
        exp['data']['clickToFinish'] = this.clickToFinish;
        exp['data']['background_image'] = this.background_image;
        exp['data']['draw_type'] = this.draw_type;
        exp['data']['stroke_color'] = this.stroke_color;
        exp['data']['showTime'] = this.showTime;
        exp['data']['expectedStimulus'] = this.expectedStimulus;
        
        exp['stimuli'] = this.stimulis;
        return exp;
    }
    
    resize(scale){
        //nothin special
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
        this.screenshotTimer += dt;

        if(!this.activity.isRunning())
        return;

        if(this.awaitToFinish){
            this.timeAwatingToFinish+= dt;
            if(this.timeAwatingToFinish > this.timeToAwaitToFinish){
                this.terminate();
            }
        }

        if(this.showTime !=-1 && this.time >= (1000*this.showTime)){
            this.timeFinished = true;
            this.awaitToFinish = true;
        }
       
        
        this.startFade += dt;
        var i =0;
        if(!this.activity.paused){
            for(i=0; i<this.stimulis.length; i++){
                if(this.stimulis[i].type == 'audio'){
                
                    if(!this.stimulis[i].played){
                        this.stimulis[i].play();
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
        }
        
    }
    
    render(ctx, scale=1){
        if(this.background_image != null){
            this.idStimulis[this.background_image].render(ctx, scale);
        }
        var i;
        for(i = 0; i < this.stimulis.length; i++){
            if(this.stimulis[i].type == "drawStimulus"){
                this.stimulis[i].color = this.stroke_color;
            }
            if(this.background_image != null){
                if(this.stimulis[i].id!= this.background_image)
                    this.stimulis[i].render(ctx, scale);
            }
            else{
                this.stimulis[i].render(ctx, scale);
            }
            
        }
        if(this.screenshotTimer >= 1000){
            this.activity.saveTemporaryScreenshot();
            this.screenshotTimer = 0.0;
        }
    }
    
    
    renderPreview(ctx, scale=1){
        
        this.render(ctx, scale);
    }
    pointerDrag(evt){
        if(this.currentDrawing == null)
            return;
        
        var pos = this.activity.lastPointerPos;
        pos[0] = parseInt(pos[0]);
        pos[1] = parseInt(pos[1]);
        this.currentDrawing.addPoint(pos);
        
    }
    pointerUp(evt){
          //this.currentDrawing.checkIsCloseToTriangle();
        this.awaitToFinish = true;
        

        //this.currentDrawing.checkIsCloseToCircle();
        //this.currentDrawing.checkIsCloseToLine();
    }
    pointerDown(evt){
        console.log("Começou desenhar...");
        if(this.startFade >= this.maxStartFade)
            if(!this.activity.paused)
                this.clickDown = true;

        var pos = this.activity.lastPointerPos;
        pos[0] = parseInt(pos[0]);
        pos[1] = parseInt(pos[1]);
        var localId = 0;
        var ds = new DrawStimulus(Date.now(), this.activity, this, pos);
        this.currentDrawing = ds;
        this.drawings.push(ds);
        this.stimulis.push(ds);


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