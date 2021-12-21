class SelectImage extends Instruction {

    exportXML() {
        var exp = [];
        exp['stimuli'] = [];
        exp['header'] = [];
        exp['data'] = [];

        exp['header']['type'] = "SelectImage";
        exp['header']['position'] = this.position;
        exp['header']['next'] = this.next;
        exp['header']['description'] = this.description;

        exp['header']['editable'] = this.editable;

        exp['data']['expectedImage'] = this.expectedImage;
        exp['data']['model'] = this.model;
        exp['data']['onCorrect'] = this.onCorrect;
        exp['data']['onWrong'] = this.onWrong;

        exp['stimuli'] = this.stimulis;

        return exp;
    }
    constructor(data = { 'type': 'SelectImage', 'position': -1, 'editable': true, 'next': -1 }, activity) {
        super(data,activity);
        this.allowUse = true;
        this.editableAttributes.push(
            new AttributeDescriptor("images", ['image', 'text'], true, "Adicionar Estímulo", 'add/remove'),
            new AttributeDescriptor("model", ['image', 'audio', 'text'], false, "Estímulo modelo", 'swap'),
            new AttributeDescriptor("expectedImage", ['stimulusID'], false, "Estímulo correto", 'swap'));

        this.description = "Apresenta estímulos e permite ao estudante selecionar um";
        if (data == null)
            return;
        this.expectedImage = data['expectedImage'];
        this.onCorrect = data['onCorrect'];
        this.onWrong = data['onWrong'];
        this.model = data['model'];
        console.log("MODEL: " +this.model);
        if(this.model == "undefined"|| this.model=="null")
            this.model = null;
        if( ! ( this.model in this.idStimulis) )
            this.model = null;
        
        


        this.editable = true;
        this.selectedImageEditing = null;
        this.ignoreInLocalSearch.push('model');
        //this.imageConfiguratinsAllowed.push('');
        this.audioConfiguratinsAllowed.push('autoplay', 'repeat', 'numberOfRepeats');


        //for tips
        this.elapsedTime = 0;
        this.appliedTip = false;
    }
    resize(scale) {

    }
    startRunning() {
        super.startRunning();
        if (this.model != null)// && this.model.length>0)
            if(this.idStimulis[this.model]!=null)
                this.idStimulis[this.model].isClickable = false;



    }
    pointerUp(evt) {


    }
    pointerDown(evt) {
        if (this.model != 'undefined' && this.model != null) {
            if (this.idStimulis[this.model].type == "audio") {
                    //console.log("CHECK_AUDIO_END: "+ this.idStimulis[this.model].finished());
                    if(!this.idStimulis[this.model].finished())
                        return;
            }
        }
        var pos = this.activity.lastPointerPos;
        var resultData = {};
        this.next = this.onWrong;
        this.activity.result = this.activity.RESULT_WRONG;
        var i;
        for (i = 0; i < this.stimulis.length; i++) {
            this.stimulis[i].pointerDown(evt);

            if (this.stimulis[i].type!="audio" && this.stimulis[i].wasPointed()) {
                if (this.stimulis[i].localID == this.expectedImage) {
                    this.next = this.onCorrect;
                    this.activity.result = this.activity.RESULT_CORRECT;
                    if (this.activity.showTip) {
                        this.activity.result = this.activity.RESULT_CORRECT_TIP;
                    }
                }
                resultData['type'] = "select";
                
                resultData['model']= this.exportStimulus(this.idStimulis[this.model]);
                
                
                
                resultData['expected'] =this.exportStimulus(this.idStimulis[this.expectedImage]);
                resultData['stimulis'] = [];
                for(var j =0; j< this.stimulis.length; j++){
                    console.log("model: "+ this.model);
                    if(this.model!=null && this.stimulis[j].id!= this.idStimulis[this.model].id){
                        resultData['stimulis'].push(this.exportStimulus(this.stimulis[j]));
                        
                    }
                }
                resultData['selected'] = this.exportStimulus(this.stimulis[i]);
                
                this.done = true;
                this.activity.resultData =JSON.stringify(resultData);
            }
        }

    }
    update(dt) {

        

        if (this.model != 'undefined' && this.model != null) {
            if (this.idStimulis[this.model].type == "audio") {
                if (!this.idStimulis[this.model].played) {
                    this.idStimulis[this.model].play();
                }
                
            }
        }

        if (this.model != 'undefined' && this.model != null) {
            if (this.idStimulis[this.model].type == "audio") {
                   // console.log("CHECK_AUDIO_END: "+ this.idStimulis[this.model].finished());
                    if(!this.idStimulis[this.model].finished())
                        return;
            }
        }

        this.elapsedTime += dt;
        
        if (this.activity.showTip) {
            if (this.expectedImage == null) {
                return;
            }
            if (this.activity.tipType == 'fadeIn') {
                console.log('fadeIn: ' + this.elapsedTime);
                this.idStimulis[this.expectedImage].shouldRender = false;
                if (this.elapsedTime > 2000) {
                    this.idStimulis[this.expectedImage].shouldRender = true;
                }
            }
            else if (this.activity.tipType == 'shrink' && !this.appliedTip) {
                this.appliedTip = true;
                console.log(">>>>>>>>>REDIMENSIONA");
                console.log(this.idStimulis[this.expectedImage]);
                this.idStimulis[this.expectedImage].scale([0.6, 0.6]);
            }
            else if (this.activity.tipType == 'enlarge' && !this.appliedTip) {
                this.appliedTip = true;
                this.idStimulis[this.expectedImage].scale([1.4, 1.4]);
            }
            else if (this.activity.tipType == 'blink') {
                
                if (this.elapsedTime > 1000) {
                    this.elapsedTime = 0;
                    if (this.idStimulis[this.expectedImage].renderImage.borderColor == "yellow") {
                        this.idStimulis[this.expectedImage].renderImage.borderColor = "blue";
                    } else if (this.idStimulis[this.expectedImage].renderImage.borderColor == "blue") {
                        this.idStimulis[this.expectedImage].renderImage.borderColor = "yellow";
                    }
                    else {
                        this.idStimulis[this.expectedImage].renderImage.borderColor = "yellow";
                    }
                }
            }
        }

    }

    render(ctx, scale = 1) {
        var i;
        for (i = 0; i < this.stimulis.length; i++) {
            if (!this.running) {
                if (this.stimulis[i].localID == this.expectedImage) {
                    this.stimulis[i].renderImage.borderColor = "green";
                }
                else if (this.stimulis[i].localID == this.model) {
                    this.stimulis[i].renderImage.borderColor = "yellow";
                }
                else {
                    this.stimulis[i].renderImage.borderColor = null;
                }
            }


            this.stimulis[i].render(ctx, scale);


        }
    }


    renderPreview(ctx, scale = 1) {

        this.render(ctx, scale);
    }
    editPointerDown(evt) {
        
        this.clickEditImage();

    }

    editPointerMove(evt) {

    }
    editPointerDrag(evt) {
        if (this.imageBeingEdited != null) {
            this.imageBeingEdited.editPointerDrag();
        }
    }
}