class RaceGame extends Instruction {

    resize(scale){
        
        this.playerMovementIncrement = [this.playerMovementIncrement[0]*scale[0], this.playerMovementIncrement[1]*scale[1]];
    }
    exportXML() {
        var exp = [];
        exp['stimuli'] = [];
        exp['header'] = [];
        exp['data'] = [];

        exp['header']['type'] = "RaceGame";
        exp['header']['position'] = this.position;
        exp['header']['next'] = this.next;
        exp['header']['description'] = this.description;
        exp['header']['editable'] = this.editable;
        console.log("type: " + (typeof this.correct_audio) + " audio: " + this.correct_audio);
        if(this.correct_audio!=null){
            if( (typeof this.correct_audio) == "object"){
                exp['data']['correct_audio'] = this.correct_audio.localID;
            }
            else{
                exp['data']['correct_audio'] = this.correct_audio;
            }
        }
        else{
            exp['data']['correct_audio'] = "null";
        }

        if(this.wrong_audio!=null){
            if( (typeof this.wrong_audio) == "object"){
                exp['data']['wrong_audio'] = this.wrong_audio.localID;
            }
            else{
                exp['data']['wrong_audio'] = this.wrong_audio;
            }
        }
        else{
            exp['data']['wrong_audio'] = "null";
        }

        //this.wrong_audio != null ? exp['data']['wrong_audio'] = this.wrong_audio.localID: exp['data']['wrong_audio'] = "null";
        exp['data']['background_image'] = this.background_image;
        

        
        exp['stimuli'] = this.stimulis;

        return exp;
    }
    constructor(data = { 'type': 'RaceGame', 'position': -1, 'editable': true, 'next': -1 }, activity) {
        super(data,activity);
        
        this.editableAttributes.push(
            new AttributeDescriptor("images",['image','text','audio'],true,"Adicionar Estímulo para emoção",'add/remove',null,null,null,null,{'isContainer':false, 'dragAndAssociate':false, 'emotionDescriptor':"none"}),
            new AttributeDescriptor("correct_audio", [ 'audio'], false, "Áudio de acerto", 'swap'),
            new AttributeDescriptor("wrong_audio", [ 'audio'], false, "Áudio de erro", 'swap'),
            new AttributeDescriptor("background_image", [ 'image'], false, "Imagem de fundo", 'swap')
            );
        
        
            this.allowUse = false;
        
        this.description = "Corrida :)";
        if (data == null)
            return;
       
        'background_image' in data? this.background_image = data['background_image']: this.background_image = null;
        
        this.onCorrect = data['onCorrect'];
        this.onWrong = data['onWrong'];
        
        'wrong_audio' in data? this.wrong_audio = data['wrong_audio']: this.wrong_audio = null;
        'correct_audio' in data? this.correct_audio = data['correct_audio']: this.correct_audio = null;
        if(this.correct_audio == "null")
            this.correct_audio = null;
        if(this.wrong_audio == "null"){
            this.wrong_audio = null;
        }

       /*if(this.correct_audio!=null && !this.activity.editing){
            console.log("+++++++++++++++++++++ AQUI ");
            this.correct_audio = this.idStimulis[this.correct_audio];
        }
        
        if(this.wrong_audio!=null &&!this.activity.isRunning()){
            this.wrong_audio = this.idStimulis[this.wrong_audio];
        }*/
       

        this.currentEmotionStimulus = -1;
        this.currentEmotion = null;
        
        this.emotionOrder = [];
        
        this.resultData = []; //o jogador acertou?

        for(var i = 0; i < this.stimulis.length; i++){
            var ignore = false;
            if( (this.stimulis[i].type =="camCapture") || 
                (this.stimulis[i].localID =="_playerStartPosition") ||
                (this.stimulis[i].localID =="_emotionPosition") ||
                (this.stimulis[i].localID =="_playerEndPosition") ){
                    ignore = true;

            } 
            if ( this.background_image!=null && this.stimulis[i].localID == this.background_image){
                ignore = true;
            }  
                if( (this.wrong_audio!=null && this.stimulis[i].localID==this.wrong_audio)){
                    ignore = true;
                }
            if ((this.correct_audio!=null && this.stimulis[i].localID==this.correct_audio)){
                ignore = true;
            }
            if(!ignore){
                this.emotionOrder.push(this.stimulis[i]);
            }
                    

        }
        
        console.log(this.emotionOrder);

        ///posicao inicial do personagem. 
        this.characterStartPosition = this.idStimulis['_playerStartPosition'];


        this.emotionDisplayPostition = this.idStimulis['_emotionPosition'];
        
        this.editable = true;
        this.selectedImageEditing = null;
        this.ignoreInLocalSearch.push('model');
        
        this.cameraView = this.idStimulis['_camInput'];

        //for tips
        this.elapsedTime = 0;
        this.appliedTip = false;

        this.timeToDetectFace = 500;//500 ms
        this.detectFaceTimer = 0 ;
    
        this.timeToShowEmotion = 10000;//10s
        this.emotionShowTimer = 0;

        this.finishLine = this.idStimulis['_playerEndPosition'];
        

        this.finishLine != null? this.playerEndPosition      = this.finishLine.renderImage.position: this.playerEndPosition = [0,0] ;
        this.idStimulis['_playerStartPosition']!=null ? this.playerStartPosition    = this.idStimulis['_playerStartPosition'].renderImage.position: this.playerStartPosition = [0,0]  ;

        console.log(this.playerEndPosition);
        console.log(this.playerStartPosition);
        var dist = [this.playerEndPosition[0]-this.playerStartPosition[0],this.playerEndPosition[1]-this.playerStartPosition[1] ];

        this.playerMovementIncrement  = [Math.ceil(dist[0]/ this.emotionOrder.length), Math.ceil(dist[1]/ this.emotionOrder.length)];



       console.log("correct audio: "+this.correct_audio + " w_audio: "+this.wrong_audio);

        
        this.numCorrect = 0;
        this.numWrong = 0;

        this.ended_moves = false;
        this.ending_timer = 0;
        this.ending_time  = 5000;

        console.log("audio correto: " + this.correct_audio);
        console.log("audio erro: " + this.wrong_audio);
    }

    pause(pause){
       
        if(pause){

        
            if(this.cameraView!=null)
                this.cameraView.hide();
        }
        else
        {
            if(this.cameraView!=null)
                this.cameraView.show();
        }
    }

    movePlayer(){
        var p = this.character.renderImage.position;
        console.log("update pos");
        console.log(this.character.renderImage.position);
        this.character.renderImage.position = [p[0]+this.playerMovementIncrement[0], p[1]+this.playerMovementIncrement[1]];
        console.log(this.character.renderImage.position);

    }
   

    checkTensorFlow(){
        
    }
    startRunning() {
        super.startRunning();

        if(this.activity.isRunning()){

            var act_json = JSON.parse(this.activity.resultData, true);
            var char_size =this.characterStartPosition.renderImage.size;
            var char_pos = this.characterStartPosition.renderImage.position;
            this.character = new ImageStimulus(act_json['selected']['value'], Date.now(),true, true, this.activity, this,
            char_size, char_pos);

            for(var i =0; i < this.stimulis.length; i++){
                if(this.stimulis[i].type == "camCapture"){
                    this.stimulis[i].startVideo();
                }
            }
           this.cameraView.startDetection();
            
            
        }
        
    }
    

    terminate(){
        super.terminate();
        this.cameraView.hide();
        console.log("+++++RESULT DATA+++++");
        console.log(this.resultData);
        var final_res = {};
        final_res['type'] = "raceGame";

        final_res['corrects'] = this.numCorrect;
        final_res['wrong'] = this.numWrong;
        final_res['trials'] = this.resultData;
        this.done = true;
        this.activity.resultData =JSON.stringify(final_res);

        if(this.numWrong == this.emotionOrder.length){
            this.activity.result = this.activity.RESULT_WRONG;
        }
        else if(this.numCorrect == this.emotionOrder.length){
            this.activity.result = this.activity.RESULT_CORRECT;
        }
        else{
            this.activity.result = this.activity.RESULT_CORRECT_TIP;
        }
        

    }
    nextEmotion(){

        
        this.currentEmotionStimulus++;
        if(this.currentEmotionStimulus >= this.emotionOrder.length){
            this.ended_moves = true;
            this.currentEmotionStimulus = null;
            return;
        }
        var emot = this.emotionOrder[this.currentEmotionStimulus];
        var size = this.emotionDisplayPostition.renderImage.size;
        var pos = this.emotionDisplayPostition.renderImage.position;
        emot.renderImage.size = size;
        emot.renderImage.position = pos;
        this.currentEmotion = emot; 
        this.emotionShowTimer = 0;
    }
    update(dt) {
        if(this.ended_moves){
            this.ending_timer += dt;
            if(this.ending_timer >= this.ending_time){
                this.terminate();
            }
            return;

        }
        if(this.cameraView!=null && this.cameraView.isReady()){
            
            
            
            this.elapsedTime += dt;
            if(this.currentEmotionStimulus < 0){
                this.nextEmotion();
                return;
            }
            var current_emotion = this.currentEmotion.emotionDescriptor;
            console.log(this.currentEmotion);
            if(this.currentEmotionStimulus >= this.emotionOrder.length){
                this.ended_moves = true;

                return;
            }
            
            if(this.cameraView.lastEmotion.length >0){
                
                if(this.cameraView.lastEmotion == current_emotion){
                    
                    this.numCorrect ++;
                    if(this.correct_audio!=null){
                        this.idStimulis[this.correct_audio].play();
                        
                    }

                    /**Correta */
                    var wrong_emot = {};
                    wrong_emot['expected'] = current_emotion;
                    wrong_emot['performed'] = this.cameraView.lastEmotion;
                    wrong_emot['stimuli'] = this.exportStimulus (this.currentEmotion);
                    wrong_emot['time'] = this.emotionShowTimer /1000;
                    wrong_emot['status'] = 'correct';
                    this.resultData.push(wrong_emot);

                    this.movePlayer();
                    this.nextEmotion();
                    return;
                }
            }
            this.detectFaceTimer+= dt;
            //if(this.detectFaceTimer > this.timeToDetectFace){
            //    this.detectEmotion();
            //    this.detectFaceTimer = 0;
            //}
            if( this.cameraView.faceVisible){
                this.emotionShowTimer+= dt;
                if(this.emotionShowTimer > this.timeToShowEmotion){
                    this.numWrong ++;
                    // NAO ACERTOU A EXPRESSAO

                    var wrong_emot = {};
                    wrong_emot['expected'] = current_emotion;
                    wrong_emot['performed'] = this.cameraView.lastEmotion;
                    wrong_emot['stimuli'] = this.exportStimulus (this.currentEmotion);
                    wrong_emot['time'] = this.emotionShowTimer /1000; //to seconds
                    wrong_emot['status'] = 'wrong';
                    this.resultData.push(wrong_emot);

                    if(this.wrong_audio!=null){
                        this.idStimulis[this.wrong_audio].play();
                    }
                    this.nextEmotion();
                    return;
                }
            }
            


        }
    }

    render(ctx, scale = 1) {
        if(this.cameraView==null || !this.cameraView.isReady()){
            ctx.fillText("Carregando camera...", 10, 10);
            return;
        }
        if(this.background_image != null){
            this.idStimulis[this.background_image].render(ctx, scale);
        }
       
        if(this.finishLine!=null){
            this.finishLine.render(ctx,scale);
        }
        if(this.currentEmotion !=null){
            this.currentEmotion.render(ctx, scale);
        }
        if(this.cameraView!=null && this.cameraView.videoStarted){
            this.cameraView.render(ctx,scale);
        }
        if(this.character != null){
            this.character.render(ctx, scale);
        }

    }


    renderPreview(ctx, scale = 1) {
        
        console.log(this.background_image);
        if(this.background_image != null){
            this.idStimulis[this.background_image].renderPreview(ctx, scale);
        }
        var i;
        for (i = 0; i < this.stimulis.length; i++) {
            if(this.stimulis[i].localID!=this.background_image)
                this.stimulis[i].renderPreview(ctx, scale);
        }
    }
    
    editPointerDrag(evt) {
        if (this.imageBeingEdited != null) {
            this.imageBeingEdited.editPointerDrag();
        }
    }

    editPointerDown(evt) {
        this.clickEditImage();
    }

    editPointerMove(evt) {}
    pointerUp(evt) {
        
         
        //this.nextEmotion();
    }
    pointerDown(evt) {}
    
}